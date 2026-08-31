<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // Nonce generado antes del $next() para que las vistas puedan usarlo via view()->share()
        $nonce = base64_encode(random_bytes(16));
        view()->share('cspNonce', $nonce);

        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'nonce-{$nonce}'; " .
            "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; " .
            "font-src 'self' https://cdnjs.cloudflare.com; " .
            "img-src 'self' data: blob:; " .
            "connect-src 'self' https://api.open-meteo.com; " .
            "frame-src 'self' https://www.youtube.com https://player.vimeo.com; " .
            "frame-ancestors 'self'; " .
            "object-src 'none';"
        );

        // Solo activo en producción con HTTPS — protege contra downgrade attacks
        if (app()->isProduction() && $request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        return $response;
    }
}
