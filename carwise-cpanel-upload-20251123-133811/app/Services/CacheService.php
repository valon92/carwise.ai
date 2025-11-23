<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    // Cache keys
    const CAR_BRANDS_POPULAR = 'car_brands_popular';
    const CAR_BRANDS_ALL = 'car_brands_all';
    const CAR_MODELS_BY_BRAND = 'car_models_brand_';
    const USER_CARS = 'user_cars_';
    const USER_STATISTICS = 'user_statistics_';
    const DIAGNOSIS_HISTORY = 'diagnosis_history_';
    const TRANSLATIONS = 'translations_';
    const DASHBOARD_STATS = 'dashboard_stats_';

    // Cache TTL (in seconds)
    const LONG_TTL = 3600 * 24; // 24 hours
    const MEDIUM_TTL = 3600 * 4; // 4 hours  
    const SHORT_TTL = 3600; // 1 hour
    const VERY_SHORT_TTL = 300; // 5 minutes

    /**
     * Get cached data or execute callback and cache result
     */
    public static function remember(string $key, callable $callback, int $ttl = self::MEDIUM_TTL)
    {
        try {
            // Use file cache if Redis is not available
            if (config('cache.default') === 'redis' && !self::isRedisAvailable()) {
                config(['cache.default' => 'file']);
            }
            
            return Cache::remember($key, $ttl, $callback);
        } catch (\Exception $e) {
            Log::warning("Cache operation failed for key: {$key}", [
                'error' => $e->getMessage()
            ]);
            
            // Fallback to direct execution if cache fails
            return $callback();
        }
    }

    /**
     * Check if Redis is available
     */
    private static function isRedisAvailable(): bool
    {
        try {
            if (!extension_loaded('redis')) {
                return false;
            }
            
            $redis = new \Redis();
            $connected = $redis->connect(
                config('database.redis.default.host', '127.0.0.1'),
                config('database.redis.default.port', 6379),
                1 // 1 second timeout
            );
            
            if ($connected) {
                $redis->close();
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get popular car brands with caching
     */
    public static function getPopularCarBrands()
    {
        return self::remember(self::CAR_BRANDS_POPULAR, function () {
            return \App\Models\CarBrand::where('is_popular', true)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'logo_url']);
        }, self::LONG_TTL);
    }

    /**
     * Get all car brands with caching
     */
    public static function getAllCarBrands()
    {
        return self::remember(self::CAR_BRANDS_ALL, function () {
            return \App\Models\CarBrand::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'logo_url', 'is_popular']);
        }, self::LONG_TTL);
    }

    /**
     * Get car models by brand with caching
     */
    public static function getCarModelsByBrand(int $brandId)
    {
        $key = self::CAR_MODELS_BY_BRAND . $brandId;
        
        return self::remember($key, function () use ($brandId) {
            return \App\Models\CarModel::where('car_brand_id', $brandId)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'body_type', 'start_year', 'end_year']);
        }, self::LONG_TTL);
    }

    /**
     * Get user's cars with caching
     */
    public static function getUserCars(int $userId)
    {
        $key = self::USER_CARS . $userId;
        
        return self::remember($key, function () use ($userId) {
            return \App\Models\Car::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();
        }, self::SHORT_TTL);
    }

    /**
     * Get user statistics with caching
     */
    public static function getUserStatistics(int $userId)
    {
        $key = self::USER_STATISTICS . $userId;
        
        return self::remember($key, function () use ($userId) {
            $totalCars = \App\Models\Car::where('user_id', $userId)->count();
            $totalDiagnoses = \App\Models\DiagnosisSession::where('user_id', $userId)->count();
            $recentDiagnoses = \App\Models\DiagnosisSession::where('user_id', $userId)
                ->where('created_at', '>=', now()->subDays(30))
                ->count();

            return [
                'total_cars' => $totalCars,
                'total_diagnoses' => $totalDiagnoses,
                'recent_diagnoses' => $recentDiagnoses,
                'avg_mileage' => \App\Models\Car::where('user_id', $userId)->avg('mileage') ?? 0
            ];
        }, self::MEDIUM_TTL);
    }


    /**
     * Get user's diagnosis history with caching
     */
    public static function getUserDiagnosisHistory(int $userId, int $limit = 10)
    {
        $key = self::DIAGNOSIS_HISTORY . $userId . '_' . $limit;
        
        return self::remember($key, function () use ($userId, $limit) {
            return \App\Models\DiagnosisSession::where('user_id', $userId)
                ->with(['result'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        }, self::SHORT_TTL);
    }

    /**
     * Get translations for a language with caching
     */
    public static function getTranslations(string $language = 'en')
    {
        $key = self::TRANSLATIONS . $language;
        
        return self::remember($key, function () use ($language) {
            $translationPath = resource_path("lang/{$language}/messages.php");
            
            if (file_exists($translationPath)) {
                return require $translationPath;
            }
            
            // Fallback to English
            return require resource_path('lang/en/messages.php');
        }, self::LONG_TTL);
    }

    /**
     * Get dashboard statistics with caching
     */
    public static function getDashboardStatistics()
    {
        return self::remember(self::DASHBOARD_STATS . 'global', function () {
            return [
                'total_users' => \App\Models\User::where('role', 'customer')->count(),
                'total_mechanics' => \App\Models\Mechanic::where('is_verified', true)->count(),
                'total_diagnoses' => \App\Models\DiagnosisSession::where('status', 'completed')->count(),
                'total_cars' => \App\Models\Car::count(),
                'recent_diagnoses' => \App\Models\DiagnosisSession::where('created_at', '>=', now()->subDays(7))->count()
            ];
        }, self::MEDIUM_TTL);
    }

    /**
     * Clear user-specific cache
     */
    public static function clearUserCache(int $userId): void
    {
        $keys = [
            self::USER_CARS . $userId,
            self::USER_STATISTICS . $userId,
            self::DIAGNOSIS_HISTORY . $userId . '_10',
            self::DIAGNOSIS_HISTORY . $userId . '_20',
            self::DASHBOARD_STATS . $userId
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Log::info("Cleared cache for user: {$userId}");
    }

    /**
     * Clear car-related cache
     */
    public static function clearCarCache(): void
    {
        $keys = [
            self::CAR_BRANDS_POPULAR,
            self::CAR_BRANDS_ALL
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        // Clear car models cache for all brands (pattern-based)
        Cache::flush(); // For simplicity, or implement pattern-based deletion

        Log::info("Cleared car-related cache");
    }

    /**
     * Clear mechanic cache
     */
    public static function clearMechanicCache(): void
    {
        $keys = [];

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Log::info("Cleared mechanic cache");
    }

    /**
     * Warm up frequently accessed cache
     */
    public static function warmUpCache(): void
    {
        Log::info("Starting cache warm-up");

        // Warm up popular car brands
        self::getPopularCarBrands();
        
        // Warm up all car brands
        self::getAllCarBrands();
        
        // Warm up featured mechanics
        self::getFeaturedMechanics();
        
        // Warm up dashboard statistics
        self::getDashboardStatistics();

        // Warm up car models for popular brands
        $popularBrands = self::getPopularCarBrands();
        foreach ($popularBrands as $brand) {
            self::getCarModelsByBrand($brand->id);
        }

        Log::info("Cache warm-up completed");
    }

    /**
     * Get cache statistics
     */
    public static function getCacheStatistics(): array
    {
        try {
            // This is a basic implementation - extend based on your cache driver
            return [
                'driver' => config('cache.default'),
                'status' => 'active',
                'keys_count' => 'N/A', // Implement if using Redis
                'memory_usage' => 'N/A', // Implement if using Redis
                'hit_rate' => 'N/A' // Implement with custom metrics
            ];
        } catch (\Exception $e) {
            return [
                'driver' => config('cache.default'),
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Tag-based cache clearing (if using Redis or Memcached)
     */
    public static function clearByTag(string $tag): void
    {
        try {
            if (method_exists(Cache::store(), 'tags')) {
                Cache::tags($tag)->flush();
                Log::info("Cleared cache for tag: {$tag}");
            } else {
                Log::warning("Tag-based cache clearing not supported for current driver");
            }
        } catch (\Exception $e) {
            Log::error("Failed to clear cache by tag: {$tag}", ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get cache key for a specific pattern
     */
    public static function getKey(string $pattern, ...$params): string
    {
        return $pattern . implode('_', $params);
    }
}
