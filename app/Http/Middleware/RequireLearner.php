<?php

namespace App\Http\Middleware;

use App\Support\LearnerAuth;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/** Pages for logged-in learners only. Unauthenticated visitors are sent to /login. */
class RequireLearner
{
    public function handle(Request $request, Closure $next): Response
    {
        $learner = LearnerAuth::resolve($request);

        if (! $learner) {
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'fetch') {
                return response()->json(['ok' => false, 'error' => 'Please log in again.'], 401);
            }

            return redirect('/login'.($request->isMethod('GET') && $request->path() !== '/' ? '?next=/'.$request->path() : ''));
        }

        // CSRF defence for state-changing calls (on top of the SameSite=Lax cookie): custom header + same-origin.
        if (! in_array($request->method(), ['GET', 'HEAD'], true)) {
            if ($request->header('X-Requested-With') !== 'fetch') {
                return response()->json(['ok' => false, 'error' => 'Bad request.'], 403);
            }
            $origin = (string) $request->header('Origin', '');
            if ($origin !== '' && parse_url($origin, PHP_URL_HOST) !== $request->getHost()) {
                return response()->json(['ok' => false, 'error' => 'Bad origin.'], 403);
            }
        }

        $request->attributes->set('learner', $learner);
        View::share('learner', $learner);

        return $next($request);
    }
}
