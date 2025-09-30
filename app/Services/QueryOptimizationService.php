<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QueryOptimizationService
{
    /**
     * Optimize user cars query with eager loading
     */
    public static function getUserCarsOptimized(int $userId)
    {
        return \App\Models\Car::where('user_id', $userId)
            ->with([
                'brand:id,name,slug,logo_url',
                'model:id,name,slug,body_type',
                'diagnosisSessions' => function($query) {
                    $query->select('id', 'car_id', 'status', 'severity', 'created_at')
                          ->latest()
                          ->limit(5);
                },
                'maintenanceHistory' => function($query) {
                    $query->select('id', 'car_id', 'service_type', 'service_date', 'next_service_due')
                          ->latest()
                          ->limit(3);
                }
            ])
            ->select([
                'id', 'user_id', 'brand_id', 'model_id', 'year', 'license_plate', 
                'color', 'mileage', 'is_primary', 'status', 'created_at'
            ])
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Optimize mechanics query with proper indexing and eager loading
     */
    public static function getMechanicsOptimized(array $filters = [])
    {
        $query = \App\Models\Mechanic::query()
            ->select([
                'id', 'user_id', 'business_name', 'city', 'country', 
                'rating', 'total_reviews', 'is_verified', 'is_available',
                'specializations', 'hourly_rate', 'created_at'
            ])
            ->with(['user:id,name,avatar'])
            ->where('is_verified', true)
            ->where('is_available', true);

        // Apply filters efficiently using indexes
        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        if (!empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        if (!empty($filters['min_rating'])) {
            $query->where('rating', '>=', $filters['min_rating']);
        }

        if (!empty($filters['specialization'])) {
            $query->whereJsonContains('specializations', $filters['specialization']);
        }

        return $query->orderBy('rating', 'desc')
                    ->orderBy('total_reviews', 'desc')
                    ->paginate(20);
    }

    /**
     * Optimize diagnosis sessions query
     */
    public static function getUserDiagnosisHistoryOptimized(int $userId, int $limit = 10)
    {
        return \App\Models\DiagnosisSession::where('user_id', $userId)
            ->select([
                'id', 'user_id', 'car_id', 'session_id', 'make', 'model', 'year',
                'description', 'status', 'severity', 'confidence_score', 'created_at'
            ])
            ->with([
                'result:id,diagnosis_session_id,problem_title,severity,requires_immediate_attention',
                'car:id,license_plate,year'
            ])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Optimize car brands with models count
     */
    public static function getCarBrandsWithModelCount()
    {
        return \App\Models\CarBrand::select([
                'id', 'name', 'slug', 'logo_url', 'country', 'is_popular', 'is_active'
            ])
            ->withCount(['models' => function($query) {
                $query->where('is_active', true);
            }])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Optimize statistics queries using database functions
     */
    public static function getDashboardStatistics()
    {
        // Use raw SQL for better performance on large datasets
        $stats = DB::select("
            SELECT 
                (SELECT COUNT(*) FROM users WHERE role = 'customer') as total_users,
                (SELECT COUNT(*) FROM mechanics WHERE is_verified = 1) as total_mechanics,
                (SELECT COUNT(*) FROM diagnosis_sessions WHERE status = 'completed') as total_diagnoses,
                (SELECT COUNT(*) FROM cars) as total_cars,
                (SELECT COUNT(*) FROM diagnosis_sessions WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as recent_diagnoses,
                (SELECT AVG(rating) FROM mechanics WHERE is_verified = 1) as avg_mechanic_rating,
                (SELECT COUNT(*) FROM appointments WHERE status = 'scheduled') as scheduled_appointments
        ");

        return $stats[0] ?? [];
    }

    /**
     * Optimize search queries with full-text search
     */
    public static function searchMechanics(string $query, array $filters = [])
    {
        $mechanicQuery = \App\Models\Mechanic::query()
            ->select([
                'id', 'user_id', 'business_name', 'city', 'country',
                'rating', 'total_reviews', 'specializations', 'hourly_rate'
            ])
            ->with(['user:id,name'])
            ->where('is_verified', true)
            ->where('is_available', true);

        // Use LIKE for simple search (can be replaced with full-text search)
        if (!empty($query)) {
            $mechanicQuery->where(function($q) use ($query) {
                $q->where('business_name', 'LIKE', "%{$query}%")
                  ->orWhere('city', 'LIKE', "%{$query}%")
                  ->orWhere('specializations', 'LIKE', "%{$query}%");
            });
        }

        // Location filter
        if (!empty($filters['location'])) {
            $mechanicQuery->where(function($q) use ($filters) {
                $q->where('city', 'LIKE', "%{$filters['location']}%")
                  ->orWhere('country', 'LIKE', "%{$filters['location']}%");
            });
        }

        return $mechanicQuery->orderByRaw("
            CASE 
                WHEN business_name LIKE ? THEN 1
                WHEN city LIKE ? THEN 2
                ELSE 3
            END, rating DESC
        ", ["%{$query}%", "%{$query}%"])
        ->paginate(20);
    }

    /**
     * Batch load related data to avoid N+1 queries
     */
    public static function batchLoadCarRelations($cars)
    {
        // Pre-load diagnosis sessions for all cars
        $carIds = $cars->pluck('id');
        
        $diagnosisSessions = \App\Models\DiagnosisSession::whereIn('car_id', $carIds)
            ->select('id', 'car_id', 'status', 'severity', 'created_at')
            ->with('result:id,diagnosis_session_id,problem_title')
            ->latest()
            ->get()
            ->groupBy('car_id');

        $maintenanceHistory = \App\Models\CarMaintenanceHistory::whereIn('car_id', $carIds)
            ->select('id', 'car_id', 'service_type', 'service_date', 'next_service_due')
            ->latest()
            ->get()
            ->groupBy('car_id');

        // Attach loaded relations to cars
        return $cars->map(function($car) use ($diagnosisSessions, $maintenanceHistory) {
            $car->setRelation('diagnosisSessions', $diagnosisSessions->get($car->id, collect()));
            $car->setRelation('maintenanceHistory', $maintenanceHistory->get($car->id, collect()));
            return $car;
        });
    }

    /**
     * Optimize pagination for large datasets
     */
    public static function optimizedPagination(Builder $query, int $page = 1, int $perPage = 20)
    {
        // Use cursor pagination for better performance on large datasets
        if ($page > 100) { // Switch to cursor pagination for deep pages
            return $query->cursorPaginate($perPage);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get optimized count query
     */
    public static function getOptimizedCount(Builder $query): int
    {
        // Use explain to check if count is expensive
        $bindings = $query->getBindings();
        $sql = $query->toSql();
        
        try {
            // For simple counts, use direct count
            if (stripos($sql, 'join') === false && stripos($sql, 'group by') === false) {
                return $query->count();
            }
            
            // For complex queries, use estimated count or cache result
            return CacheService::remember(
                'count_' . md5($sql . serialize($bindings)),
                fn() => $query->count(),
                CacheService::SHORT_TTL
            );
        } catch (\Exception $e) {
            Log::warning('Count optimization failed', ['error' => $e->getMessage()]);
            return $query->count();
        }
    }

    /**
     * Log slow queries for monitoring
     */
    public static function logSlowQuery(string $sql, array $bindings, float $time): void
    {
        if ($time > 1000) { // Log queries taking more than 1 second
            Log::warning('Slow query detected', [
                'sql' => $sql,
                'bindings' => $bindings,
                'time' => $time . 'ms'
            ]);
        }
    }

    /**
     * Analyze query performance
     */
    public static function analyzeQuery(Builder $query): array
    {
        $sql = $query->toSql();
        $bindings = $query->getBindings();
        
        try {
            // Get query explanation (MySQL specific)
            $explain = DB::select("EXPLAIN {$sql}", $bindings);
            
            return [
                'sql' => $sql,
                'bindings' => $bindings,
                'explain' => $explain,
                'recommendations' => self::getQueryRecommendations($explain)
            ];
        } catch (\Exception $e) {
            return [
                'sql' => $sql,
                'bindings' => $bindings,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get query optimization recommendations
     */
    private static function getQueryRecommendations(array $explain): array
    {
        $recommendations = [];
        
        foreach ($explain as $row) {
            if (isset($row->type) && $row->type === 'ALL') {
                $recommendations[] = "Full table scan detected on {$row->table}. Consider adding an index.";
            }
            
            if (isset($row->rows) && $row->rows > 10000) {
                $recommendations[] = "Large number of rows examined ({$row->rows}). Consider adding more selective WHERE clauses.";
            }
            
            if (isset($row->Extra) && stripos($row->Extra, 'Using filesort') !== false) {
                $recommendations[] = "Filesort detected. Consider adding an index for ORDER BY clause.";
            }
        }
        
        return $recommendations;
    }
}

