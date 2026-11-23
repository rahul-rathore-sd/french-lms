<?php

use App\Http\Middleware\StartSessionIfNeeded;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
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

        // Public pages (static UI, blog, sitemap) are stateless: no session row or cookie per visitor.
        $middleware->web(remove: [
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class,
        ]);

        // JSON API: no CSRF token (the API checks the X-Requested-With header instead) and
        // no session unless an admin cookie is present, so anonymous visitors create no sessions.
        $middleware->group('apiadmin', [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSessionIfNeeded::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
