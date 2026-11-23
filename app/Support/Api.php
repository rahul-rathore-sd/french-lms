<?php

namespace App\Support;

use App\Models\Activity;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Small helpers shared by the API controllers.
 */
class Api
{
    public const CEFR = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];

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

    public static function siteUrl(): string
    {
        return config('french.site_url');
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

    /** 'hermes' when the request carries the right bearer key (Authorization or X-Hermes-Key), else null. */
    public static function actor(Request $request): ?string
    {
        $key = (string) config('french.hermes_api_key');
        $token = self::bearerToken($request);

        return $token !== '' && strlen($key) >= 24 && hash_equals($key, $token) ? 'hermes' : null;
    }

    public static function requireActor(Request $request): string
    {
        return self::actor($request) ?? self::fail('Unauthorized.', 401);
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

    public static function log(string $actor, string $action, string $entity, ?int $entityId, string $detail = ''): void
    {
        Activity::create([
            'actor' => $actor, 'action' => $action, 'entity' => $entity, 'entity_id' => $entityId,
            'detail' => self::s($detail, 500), 'created_at' => self::nowIso(),
        ]);
    }
}
