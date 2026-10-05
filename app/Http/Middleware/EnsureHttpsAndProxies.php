<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHttpsAndProxies
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            $request->header('X-Forwarded-Port') == 443
            || $request->server('SERVER_PORT') == 443
            || $request->header('X-Forwarded-Proto') === 'https'
            || str_starts_with(config('app.url'), 'https://')
        ) {
            if (!$request->headers->has('X-Forwarded-Proto') || $request->header('X-Forwarded-Proto') !== 'https') {
                $request->headers->set('X-Forwarded-Proto', 'https');
            }
            $request->server->set('HTTPS', 'on');
            $request->server->set('SERVER_PORT', 443);
        }

        return $next($request);
    }
}
