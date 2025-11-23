<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductionSecurityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply in production
        if (app()->environment('production')) {
            // Security headers
            $response = $next($request);
            
            // Add security headers
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'DENY');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
            
            // Content Security Policy
            $csp = "default-src 'self'; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; " .
                   "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
                   "font-src 'self' https://fonts.gstatic.com; " .
                   "img-src 'self' data: https:; " .
                   "connect-src 'self' https://api.openai.com https://api.anthropic.com https://api.cohere.ai https://api.mistral.ai; " .
                   "frame-ancestors 'none';";
            
            $response->headers->set('Content-Security-Policy', $csp);
            
            // Rate limiting headers
            $response->headers->set('X-RateLimit-Limit', '1000');
            $response->headers->set('X-RateLimit-Remaining', '999');
            
            return $response;
        }
        
        return $next($request);
    }
}
