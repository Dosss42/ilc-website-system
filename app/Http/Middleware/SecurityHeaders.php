<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Legacy XSS protection for older browsers
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Control referrer info sent with requests
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Remove server fingerprint
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        // Permissions policy — restrict browser features
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // HSTS — only when actually on HTTPS
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Content Security Policy
        // 'unsafe-inline' needed for Blade inline styles/scripts; tighten later when ready
        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://www.google.com https://www.gstatic.com; " .
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; " .
            "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net; " .
            "img-src 'self' data: https:; " .
            "connect-src 'self' https://cdn.jsdelivr.net; " .
            "frame-src https://www.google.com; " .
            "object-src 'none';"
        );

        return $response;
    }
}
