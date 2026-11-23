<?php

namespace App\Http\Controllers;

use App\Models\Learner;
use App\Support\LearnerAuth;
use App\Support\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;

/** Register / log in with a name and a numeric PIN; no emails, no passwords to reset. */
class AuthController extends Controller
{
    private const NAME_RULE = '/^[\p{L}\p{N}][\p{L}\p{N} ._-]{1,29}$/u';

    public function showLogin(Request $request): mixed
    {
        if (LearnerAuth::resolve($request)) {
            return redirect('/');
        }

        return view('auth.login', ['next' => $this->safeNext($request->query('next')), 'learners' => Learner::count()]);
    }

    public function showRegister(Request $request): mixed
    {
        if (LearnerAuth::resolve($request)) {
            return redirect('/');
        }
        View::share('learner', null);

        return view('auth.register', ['max' => config('tcf.max_learners'), 'full' => Learner::count() >= config('tcf.max_learners')]);
    }

    public function login(Request $request): JsonResponse
    {
        $this->requireFetch($request);
        $name = trim((string) $request->input('name', ''));
        $pin = (string) $request->input('pin', '');

        // 5 tries per 15 minutes for the same name from the same address, and 20 per hour for a name overall
        $key1 = 'login:'.strtolower($name).'|'.$request->ip();
        $key2 = 'login-name:'.strtolower($name);
        if (RateLimiter::tooManyAttempts($key1, 5) || RateLimiter::tooManyAttempts($key2, 20)) {
            return $this->err('Too many attempts. Please wait a few minutes and try again.', 429);
        }

        $learner = $name === '' ? null : Learner::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();
        if (! $learner || ! Hash::check($pin, $learner->pin_hash)) {
            RateLimiter::hit($key1, 900);
            RateLimiter::hit($key2, 3600);
            usleep(300_000);

            return $this->err('Wrong name or PIN.', 401);
        }
        RateLimiter::clear($key1);

        return $this->done(['ok' => true, 'redirect' => $this->safeNext($request->input('next'))], LearnerAuth::issue($learner, $request));
    }

    public function register(Request $request): JsonResponse
    {
        $this->requireFetch($request);
        if (Learner::count() >= config('tcf.max_learners')) {
            return $this->err('This site is full: ask the owner to raise the limit.', 403);
        }
        RateLimiter::hit($k = 'register:'.$request->ip(), 3600);
        if (RateLimiter::attempts($k) > 10) {
            return $this->err('Too many attempts. Please try again later.', 429);
        }

        $name = trim((string) $request->input('name', ''));
        $pin = (string) $request->input('pin', '');
        $level = (string) $request->input('level', 'A1');
        if (! preg_match(self::NAME_RULE, $name)) {
            return $this->err('Name: 2 to 30 characters (letters, numbers, space, dot, dash).');
        }
        if (! preg_match('/^\d{4,8}$/', $pin)) {
            return $this->err('PIN: 4 to 8 digits (6 is a good choice).');
        }
        if (! array_key_exists($level, Learner::CREDIT)) {
            return $this->err('Choose a starting level.');
        }
        if (Learner::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->exists()) {
            return $this->err('This name is already taken. Pick another one.', 409);
        }

        $learner = Learner::create([
            'name' => $name, 'pin_hash' => Hash::make($pin), 'start_date' => Plan::today()->toDateString(),
            'daily_minutes' => max(30, min(300, (int) $request->input('minutes', config('tcf.daily_minutes')))),
            'wish_months' => max(1, min(24, (int) $request->input('wish_months', config('tcf.wish_months')))),
            'credit_hours' => Learner::CREDIT[$level],
        ]);

        return $this->done(['ok' => true, 'redirect' => '/'], LearnerAuth::issue($learner, $request));
    }

    public function logout(Request $request): JsonResponse
    {
        $this->requireFetch($request);

        return $this->done(['ok' => true, 'redirect' => '/login'], LearnerAuth::forget($request));
    }

    private function requireFetch(Request $request): void
    {
        abort_unless($request->header('X-Requested-With') === 'fetch', 403);
    }

    private function err(string $message, int $status = 422): JsonResponse
    {
        return response()->json(['ok' => false, 'error' => $message], $status);
    }

    private function done(array $payload, $cookie): JsonResponse
    {
        $response = response()->json($payload);
        $response->headers->setCookie($cookie);

        return $response;
    }

    /** Only local paths are allowed as a post-login destination. */
    private function safeNext(mixed $next): string
    {
        $next = (string) $next;

        return preg_match('#^/[A-Za-z0-9/_-]*$#', $next) && ! str_starts_with($next, '//') && ! in_array($next, ['/login', '/register'], true) ? $next : '/';
    }
}
