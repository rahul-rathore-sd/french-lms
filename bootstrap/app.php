<?php

use App\Http\Middleware\NoIndex;
use App\Http\Middleware\RequireLearner;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // The site sits behind CloudPanel's Nginx proxy: trust it for the client IP and scheme.
        $middleware->trustProxies(at: '*');

        // Private by default: tell search engines not to index anything (see FRENCH_NOINDEX).
        $middleware->append(NoIndex::class);

        // Pages for logged-in learners (name + PIN, own token cookie).
        $middleware->alias(['learner' => RequireLearner::class]);

        // Lightweight: no Laravel sessions or CSRF tokens. Login uses our own small token cookie
        // (App\Support\LearnerAuth); the JSON API is protected by the bearer key.
        $middleware->web(remove: [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
