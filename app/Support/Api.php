<?php

namespace App\Support;

use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Small helpers shared by the API controllers (ported from the original PHP lib.php).
 */
class Api
{
    public const CEFR = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];
    public const LEAD_STATUS = ['new', 'contacted', 'qualified', 'converted', 'lost'];
    public const CLIENT_STATUS = ['active', 'paused', 'completed'];
    public const BLOG_STATUS = ['draft', 'published', 'scheduled'];
    public const SOCIAL_STATUS = ['draft', 'approved', 'scheduled', 'posted', 'failed'];
    public const PLATFORMS = ['instagram', 'facebook', 'linkedin', 'x', 'youtube', 'whatsapp', 'other'];
    public const EMAIL_IN = ['unread', 'read', 'replied', 'archived'];
    public const EMAIL_OUT = ['draft', 'approved', 'sent', 'failed'];
    public const EMAIL_RE = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';

    public static function nowIso(): string
    {
        return gmdate('Y-m-d\TH:i:s\Z');
    }

    /** Trimmed, length-limited string from untrusted input. */
    public static function s(mixed $value, int $max): string
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return '';
        }

        return mb_substr(trim((string) $value), 0, $max, 'UTF-8');
    }

    public static function inList(mixed $v, array $allowed, ?string $default = null): ?string
    {
        return is_string($v) && in_array($v, $allowed, true) ? $v : $default;
    }

    public static function siteUrl(): string
    {
        return config('french.site_url');
    }

    /** Casts id-like columns to int, as the original API did. */
    public static function ints(array $row, array $keys = ['id', 'lead_id', 'blog_id', 'entity_id', 'reply_to_id']): array
    {
        foreach ($keys as $k) {
            if (array_key_exists($k, $row) && $row[$k] !== null) {
                $row[$k] = (int) $row[$k];
            }
        }

        return $row;
    }

    /* ------------------------------------------------------------ output --- */

    public static function json(mixed $data, int $status = 200): JsonResponse
    {
        return response()->json($data, $status, ['Cache-Control' => 'no-store', 'X-Content-Type-Options' => 'nosniff'],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /** Aborts the request with {"success":false,"error":...}. */
    public static function fail(string $message, int $status = 400): never
    {
        throw new HttpResponseException(self::json(['success' => false, 'error' => $message], $status));
    }

    public static function mustInt(?string $id): int
    {
        if ($id === null || ! ctype_digit($id)) {
            self::fail('A numeric id is required.', 422);
        }

        return (int) $id;
    }

    /** JSON body as an array (invalid JSON is a 400). */
    public static function body(Request $request): array
    {
        $raw = $request->getContent();
        if ($raw === '' || $raw === false) {
            return [];
        }
        $data = json_decode($raw, true);
        if (! is_array($data)) {
            self::fail('Invalid JSON body.', 400);
        }

        return $data;
    }

    /* -------------------------------------------------------------- auth --- */

    /** 'hermes' (Bearer key), 'admin' (browser session) or null. */
    public static function actor(Request $request): ?string
    {
        $key = (string) config('french.hermes_api_key');
        $token = self::bearerToken($request);
        if ($token !== '') {
            return strlen($key) >= 24 && hash_equals($key, $token) ? 'hermes' : null;
        }
        if ($request->hasSession() && $request->session()->get('admin')) {
            return 'admin';
        }

        return null;
    }

    public static function requireActor(Request $request): string
    {
        $who = self::actor($request);
        if ($who === null) {
            self::fail('Unauthorized.', 401);
        }
        if ($who === 'admin' && ! in_array($request->method(), ['GET', 'HEAD'], true)) {
            // CSRF defence on top of SameSite=Strict: custom header + same-origin Origin.
            if ($request->header('X-Requested-With') !== 'fetch') {
                self::fail('Bad request.', 403);
            }
            $origin = (string) $request->header('Origin', '');
            if ($origin !== '' && parse_url($origin, PHP_URL_HOST) !== $request->getHost()) {
                self::fail('Bad origin.', 403);
            }
        }

        return $who;
    }

    private static function bearerToken(Request $request): string
    {
        $h = (string) $request->header('Authorization', '');
        if (stripos($h, 'Bearer ') === 0) {
            return trim(substr($h, 7));
        }

        // Some proxies strip Authorization; accept a dedicated header too.
        return trim((string) $request->header('X-Hermes-Key', ''));
    }

    /** Allows `$limit` hits per `$window` seconds for this IP and bucket. */
    public static function throttle(Request $request, string $bucket, int $limit, int $window): void
    {
        $key = $bucket.':'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, $limit)) {
            self::fail('Too many requests. Please try again later.', 429);
        }
        RateLimiter::hit($key, $window);
    }

    public static function log(string $actor, string $action, string $entity, ?int $entityId, string $detail = ''): void
    {
        Activity::create([
            'actor' => $actor, 'action' => $action, 'entity' => $entity, 'entity_id' => $entityId,
            'detail' => self::s($detail, 500), 'created_at' => self::nowIso(),
        ]);
    }

    public static function notifyAdmin(string $subject, string $text): void
    {
        $to = config('french.notify_email');
        if ($to === '' || ! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return;
        }
        try {
            Mail::raw($text, fn ($m) => $m->to($to)->subject(preg_replace('/[\r\n]+/', ' ', $subject)));
        } catch (\Throwable $e) {
            report($e); // an email failure must never break the form
        }
    }

    /* ------------------------------------------------------------- misc ---- */

    public static function slugify(string $text): string
    {
        $text = (string) @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        $text = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $text) ?? '');
        $text = trim($text, '-');

        return $text !== '' ? substr($text, 0, 120) : 'post';
    }

    public static function isPublicPost(array $row): bool
    {
        if ($row['status'] === 'published') {
            return true;
        }

        return $row['status'] === 'scheduled' && ! empty($row['publish_at']) && $row['publish_at'] <= self::nowIso();
    }

    /** Eloquent scope helper: posts visible to the public. */
    public static function wherePublic($query)
    {
        $now = self::nowIso();

        return $query->where(function ($q) use ($now) {
            $q->where('status', 'published')
                ->orWhere(fn ($q2) => $q2->where('status', 'scheduled')->whereNotNull('publish_at')->where('publish_at', '<=', $now));
        });
    }

    /** Minimal, safe Markdown to HTML (input is escaped first, so raw HTML never passes). */
    public static function mdToHtml(string $md): string
    {
        $md = str_replace(["\r\n", "\r"], "\n", $md);
        $esc = static fn (string $t): string => htmlspecialchars($t, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $inline = static function (string $t) use ($esc): string {
            $t = $esc($t);
            $t = preg_replace_callback('/`([^`]+)`/', fn ($m) => '<code>'.$m[1].'</code>', $t) ?? $t;
            $t = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $t) ?? $t;
            $t = preg_replace('/(?<![\w*])\*([^*\n]+)\*(?![\w*])/', '<em>$1</em>', $t) ?? $t;
            $t = preg_replace_callback('/!\[([^\]]*)\]\((https?:\/\/[^\s)]+|\/[^\s)]*)\)/',
                fn ($m) => '<img src="'.$m[2].'" alt="'.$m[1].'" loading="lazy">', $t) ?? $t;
            $t = preg_replace_callback('/\[([^\]]+)\]\((https?:\/\/[^\s)]+|\/[^\s)]*|mailto:[^\s)]+)\)/',
                fn ($m) => '<a href="'.$m[2].'" rel="noopener'.(str_starts_with($m[2], '/') ? '' : ' nofollow').'">'.$m[1].'</a>', $t) ?? $t;

            return $t;
        };

        $out = [];
        $para = [];
        $list = null;
        $inCode = false;
        $code = [];

        $flushPara = function () use (&$para, &$out, $inline) {
            if ($para) {
                $out[] = '<p>'.$inline(implode(' ', $para)).'</p>';
                $para = [];
            }
        };
        $closeList = function () use (&$list, &$out) {
            if ($list) {
                $out[] = "</$list>";
                $list = null;
            }
        };

        foreach (explode("\n", $md) as $line) {
            if (preg_match('/^```/', $line)) {
                if ($inCode) {
                    $out[] = '<pre><code>'.$esc(implode("\n", $code)).'</code></pre>';
                    $code = [];
                    $inCode = false;
                } else {
                    $flushPara();
                    $closeList();
                    $inCode = true;
                }

                continue;
            }
            if ($inCode) {
                $code[] = $line;

                continue;
            }
            if (trim($line) === '') {
                $flushPara();
                $closeList();

                continue;
            }
            if (preg_match('/^(#{1,4})\s+(.+)$/', $line, $m)) {
                $flushPara();
                $closeList();
                $level = min(4, strlen($m[1]) + 1); // the page title is the h1
                $out[] = "<h$level>".$inline($m[2])."</h$level>";

                continue;
            }
            if (preg_match('/^(-{3,}|\*{3,})$/', trim($line))) {
                $flushPara();
                $closeList();
                $out[] = '<hr>';

                continue;
            }
            if (preg_match('/^>\s?(.*)$/', $line, $m)) {
                $flushPara();
                $closeList();
                $out[] = '<blockquote>'.$inline($m[1]).'</blockquote>';

                continue;
            }
            if (preg_match('/^\s*[-*+]\s+(.+)$/', $line, $m)) {
                $flushPara();
                if ($list !== 'ul') {
                    $closeList();
                    $out[] = '<ul>';
                    $list = 'ul';
                }
                $out[] = '<li>'.$inline($m[1]).'</li>';

                continue;
            }
            if (preg_match('/^\s*\d+[.)]\s+(.+)$/', $line, $m)) {
                $flushPara();
                if ($list !== 'ol') {
                    $closeList();
                    $out[] = '<ol>';
                    $list = 'ol';
                }
                $out[] = '<li>'.$inline($m[1]).'</li>';

                continue;
            }
            $closeList();
            $para[] = trim($line);
        }
        if ($inCode) {
            $out[] = '<pre><code>'.$esc(implode("\n", $code)).'</code></pre>';
        }
        $flushPara();
        $closeList();

        return implode("\n", $out);
    }
}
