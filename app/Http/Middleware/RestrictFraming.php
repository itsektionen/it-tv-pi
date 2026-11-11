<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Only the screen pages may be framed, so an iframe slide can't load our other pages.
 */
class RestrictFraming
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $framable = $request->routeIs('client', 'preview');

        $response->headers->set('Content-Security-Policy', "frame-ancestors " . ($framable ? "'self'" : "'none'"));
        $response->headers->set('X-Frame-Options', $framable ? 'SAMEORIGIN' : 'DENY');
        
        return $response;
    }
}
