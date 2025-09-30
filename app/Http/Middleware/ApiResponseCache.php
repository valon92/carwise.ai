<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseCache
{
    /**
     * Cacheable routes and their TTL (in seconds)
     */
    private const CACHEABLE_ROUTES = [
        'api/car-brands' => 3600 * 24, // 24 hours
        'api/car-brands/popular' => 3600 * 24, // 24 hours
        'api/car-models' => 3600 * 12, // 12 hours
        'api/currencies' => 3600 * 24, // 24 hours
        'api/languages' => 3600 * 24, // 24 hours
        'api/mechanics' => 3600, // 1 hour
        'api/dashboard/statistics' => 3600, // 1 hour
    ];

    /**
     * Routes that should not be cached
     */
    private const NON_CACHEABLE_PATTERNS = [
        'api/auth/',
        'api/login',
        'api/logout',
        'api/register',
        'api/diagnosis/start',
        'api/user',
        'api/cars' // User-specific data
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only cache GET requests
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        $route = $request->path();
        
        // Check if route should not be cached
        if ($this->shouldNotCache($route)) {
            return $next($request);
        }

        // Check if route is cacheable
        $ttl = $this->getCacheTtl($route);
        if (!$ttl) {
            return $next($request);
        }

        // Generate cache key
        $cacheKey = $this->generateCacheKey($request);

        try {
            // Try to get cached response
            $cachedResponse = Cache::get($cacheKey);
            
            if ($cachedResponse) {
                Log::debug('Cache hit', ['key' => $cacheKey, 'route' => $route]);
                
                return response($cachedResponse['content'], $cachedResponse['status'])
                    ->withHeaders($cachedResponse['headers'])
                    ->header('X-Cache', 'HIT')
                    ->header('X-Cache-Key', $cacheKey);
            }

            // Execute request
            $response = $next($request);

            // Cache successful responses
            if ($response->getStatusCode() === 200) {
                $this->cacheResponse($cacheKey, $response, $ttl);
                $response->header('X-Cache', 'MISS');
            }

            return $response->header('X-Cache-Key', $cacheKey);

        } catch (\Exception $e) {
            Log::warning('API cache error', [
                'key' => $cacheKey,
                'error' => $e->getMessage()
            ]);
            
            return $next($request);
        }
    }

    /**
     * Check if route should not be cached
     */
    private function shouldNotCache(string $route): bool
    {
        foreach (self::NON_CACHEABLE_PATTERNS as $pattern) {
            if (str_starts_with($route, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get cache TTL for route
     */
    private function getCacheTtl(string $route): ?int
    {
        // Exact match
        if (isset(self::CACHEABLE_ROUTES[$route])) {
            return self::CACHEABLE_ROUTES[$route];
        }

        // Pattern matching for dynamic routes
        foreach (self::CACHEABLE_ROUTES as $pattern => $ttl) {
            if (fnmatch($pattern, $route)) {
                return $ttl;
            }
        }

        return null;
    }

    /**
     * Generate cache key for request
     */
    private function generateCacheKey(Request $request): string
    {
        $key = 'api_cache:' . $request->path();
        
        // Include query parameters in cache key
        if ($request->query()) {
            $queryParams = $request->query();
            ksort($queryParams); // Sort for consistent keys
            $key .= ':' . md5(serialize($queryParams));
        }

        // Include user ID for user-specific cached data
        if ($request->user()) {
            $key .= ':user_' . $request->user()->id;
        }

        // Include language preference
        if ($request->header('Accept-Language')) {
            $key .= ':lang_' . substr($request->header('Accept-Language'), 0, 2);
        }

        return $key;
    }

    /**
     * Cache the response
     */
    private function cacheResponse(string $key, Response $response, int $ttl): void
    {
        try {
            $cachedData = [
                'content' => $response->getContent(),
                'status' => $response->getStatusCode(),
                'headers' => $this->getCacheableHeaders($response),
                'cached_at' => now()->toISOString()
            ];

            Cache::put($key, $cachedData, $ttl);
            
            Log::debug('Response cached', [
                'key' => $key,
                'ttl' => $ttl,
                'size' => strlen($response->getContent())
            ]);

        } catch (\Exception $e) {
            Log::warning('Failed to cache response', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get headers that should be cached
     */
    private function getCacheableHeaders(Response $response): array
    {
        $cacheableHeaders = [
            'Content-Type',
            'Content-Language',
            'X-Total-Count',
            'X-Page-Count'
        ];

        $headers = [];
        foreach ($cacheableHeaders as $header) {
            if ($response->headers->has($header)) {
                $headers[$header] = $response->headers->get($header);
            }
        }

        return $headers;
    }
}

