<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;

/**
 * Starts the (database) session only for admins: when the session cookie is already present,
 * or on the login endpoint. Everyone else (leads, blog readers, Hermes with its Bearer key)
 * stays stateless, so no session rows pile up.
 */
class StartSessionIfNeeded extends StartSession
{
    public function handle($request, Closure $next)
    {
        $cookie = $this->manager->getSessionConfig()['cookie'];

        if (! $request->cookies->has($cookie) && ! $request->is('api/v1/admin/login')) {
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}
