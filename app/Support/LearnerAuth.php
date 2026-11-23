<?php

namespace App\Support;

use App\Models\Learner;
use App\Models\LearnerToken;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Minimal login for a few people: name + PIN, then a random token in an HttpOnly cookie.
 * Only a SHA-256 hash of the token is stored, so a leaked database cannot be used to log in.
 * No Laravel session is used (the site stays stateless and light).
 */
class LearnerAuth
{
    public const COOKIE = 'tcf_session';
    private const DAYS = 180;

    /** Creates a login token for the learner and returns the cookie to attach to the response. */
    public static function issue(Learner $learner, Request $request): Cookie
    {
        $raw = bin2hex(random_bytes(32));
        $learner->tokens()->create([
            'token_hash' => hash('sha256', $raw),
            'device' => mb_substr((string) $request->userAgent(), 0, 120),
            'last_used_at' => now(),
        ]);

        return self::cookie($learner->id.'.'.$raw, now()->addDays(self::DAYS)->getTimestamp());
    }

    public static function forget(Request $request): Cookie
    {
        if ($t = self::tokenFrom($request)) {
            LearnerToken::where('token_hash', hash('sha256', $t[1]))->delete();
        }

        return self::cookie('', 1);
    }

    public static function resolve(Request $request): ?Learner
    {
        $t = self::tokenFrom($request);
        if (! $t) {
            return null;
        }
        $token = LearnerToken::where('token_hash', hash('sha256', $t[1]))->where('learner_id', $t[0])->first();
        if (! $token) {
            return null;
        }
        // refresh "last seen" at most once an hour (keeps writes rare)
        if (! $token->last_used_at || $token->last_used_at->lt(now()->subHour())) {
            $token->forceFill(['last_used_at' => now()])->save();
            Learner::whereKey($t[0])->update(['last_seen_at' => now()]);
        }

        return Learner::find($t[0]);
    }

    /** Hash of the token in this request's cookie (to keep the current device logged in). */
    public static function currentHash(Request $request): string
    {
        $t = self::tokenFrom($request);

        return $t ? hash('sha256', $t[1]) : '';
    }

    /** @return array{0:int,1:string}|null */
    private static function tokenFrom(Request $request): ?array
    {
        $c = (string) $request->cookies->get(self::COOKIE, '');
        if (! preg_match('/^(\d+)\.([0-9a-f]{64})$/', $c, $m)) {
            return null;
        }

        return [(int) $m[1], $m[2]];
    }

    private static function cookie(string $value, int $expires): Cookie
    {
        return new Cookie(self::COOKIE, $value, $expires, '/', null, app()->isProduction(), true, false, Cookie::SAMESITE_LAX);
    }
}
