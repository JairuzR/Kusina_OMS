<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // Rate limiting — 100 requests per minute per IP
        $key = 'global:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 100)) {
            abort(429, 'Too many requests. Please slow down.');
        }

        RateLimiter::hit($key, 60);

        $response = $next($request);

        // Security headers
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        return $response;
    }
}