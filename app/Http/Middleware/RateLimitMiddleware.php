<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    /**
     * Rate limits for different endpoints (requests per minute)
     */
    private const RATE_LIMITS = [
        // Authentication endpoints - stricter limits
        'api/login' => 5,
        'api/register' => 3,
        'api/password/reset' => 3,
        
        // AI diagnosis endpoints - moderate limits
        'api/diagnosis/start' => 10,
        'api/diagnosis/result' => 30,
        
        // Search endpoints - moderate limits
        'api/mechanics/search' => 20,
        'api/car-brands/search' => 30,
        
        // General API endpoints - higher limits
        'api/cars' => 60,
        'api/appointments' => 30,
        'api/notifications' => 60,
        
        // Public endpoints - higher limits
        'api/car-brands' => 100,
        'api/car-models' => 100,
        'api/languages' => 100,
    ];

    /**
     * Default rate limit (requests per minute)
     */
    private const DEFAULT_LIMIT = 60;

    /**
     * Rate limit for authenticated users multiplier
     */
    private const AUTHENTICATED_MULTIPLIER = 2;

    /**
     * Rate limit for premium users multiplier
     */
    private const PREMIUM_MULTIPLIER = 5;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->path();
        $identifier = $this->getIdentifier($request);
        
        // Get rate limit for this endpoint
        $limit = $this->getRateLimit($route, $request);
        
        // Check rate limit
        $cacheKey = "rate_limit:{$route}:{$identifier}";
        $attempts = Cache::get($cacheKey, 0);
        
        if ($attempts >= $limit) {
            Log::warning('Rate limit exceeded', [
                'route' => $route,
                'identifier' => $identifier,
                'attempts' => $attempts,
                'limit' => $limit
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Rate limit exceeded. Please try again later.',
                'retry_after' => 60
            ], 429)
            ->header('X-RateLimit-Limit', $limit)
            ->header('X-RateLimit-Remaining', 0)
            ->header('X-RateLimit-Reset', now()->addMinute()->timestamp)
            ->header('Retry-After', 60);
        }
        
        // Increment attempts
        Cache::put($cacheKey, $attempts + 1, 60); // 1 minute window
        
        $response = $next($request);
        
        // Add rate limit headers
        $remaining = max(0, $limit - $attempts - 1);
        
        return $response
            ->header('X-RateLimit-Limit', $limit)
            ->header('X-RateLimit-Remaining', $remaining)
            ->header('X-RateLimit-Reset', now()->addMinute()->timestamp);
    }

    /**
     * Get unique identifier for rate limiting
     */
    private function getIdentifier(Request $request): string
    {
        // Use user ID if authenticated
        if ($request->user()) {
            return 'user:' . $request->user()->id;
        }
        
        // Use IP address for unauthenticated requests
        return 'ip:' . $request->ip();
    }

    /**
     * Get rate limit for route and user
     */
    private function getRateLimit(string $route, Request $request): int
    {
        // Get base limit
        $baseLimit = $this->getBaseLimitForRoute($route);
        
        // Apply multipliers for authenticated users
        if ($request->user()) {
            $baseLimit *= self::AUTHENTICATED_MULTIPLIER;
            
            // Apply premium multiplier if user has premium subscription
            if ($this->isPremiumUser($request->user())) {
                $baseLimit *= self::PREMIUM_MULTIPLIER;
            }
        }
        
        return (int) $baseLimit;
    }

    /**
     * Get base rate limit for route
     */
    private function getBaseLimitForRoute(string $route): int
    {
        // Exact match
        if (isset(self::RATE_LIMITS[$route])) {
            return self::RATE_LIMITS[$route];
        }
        
        // Pattern matching for dynamic routes
        foreach (self::RATE_LIMITS as $pattern => $limit) {
            if (fnmatch($pattern, $route)) {
                return $limit;
            }
        }
        
        return self::DEFAULT_LIMIT;
    }

    /**
     * Check if user has premium subscription
     */
    private function isPremiumUser($user): bool
    {
        // Check if user has active subscription
        return $user->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->exists();
    }
}

