<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerformanceController extends Controller
{
    /**
     * Get performance dashboard data
     */
    public function dashboard(): JsonResponse
    {
        try {
            $metrics = $this->getPerformanceMetrics();
            $cacheStats = $this->getCacheStatistics();
            $databaseStats = $this->getDatabaseStatistics();
            $systemStats = $this->getSystemStatistics();

            return response()->json([
                'success' => true,
                'data' => [
                    'metrics' => $metrics,
                    'cache' => $cacheStats,
                    'database' => $databaseStats,
                    'system' => $systemStats,
                    'updated_at' => now()->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch performance data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get recent performance metrics
     */
    public function metrics(Request $request): JsonResponse
    {
        try {
            $hours = $request->get('hours', 24);
            $metrics = $this->getHourlyMetrics($hours);

            return response()->json([
                'success' => true,
                'data' => $metrics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch performance metrics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get slow queries
     */
    public function slowQueries(): JsonResponse
    {
        try {
            // Enable query logging temporarily if not enabled
            $wasLogging = DB::logging();
            if (!$wasLogging) {
                DB::enableQueryLog();
            }

            // Get slow queries from logs or monitoring
            $slowQueries = $this->getSlowQueriesFromLogs();

            return response()->json([
                'success' => true,
                'data' => $slowQueries,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch slow queries',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear performance cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            // Clear application cache
            Cache::flush();
            
            // Clear specific performance metrics cache
            $this->clearPerformanceCache();

            Log::info('Performance cache cleared by user', [
                'user_id' => auth()->id(),
                'timestamp' => now()->toISOString(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Performance cache cleared successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Warm up cache
     */
    public function warmupCache(): JsonResponse
    {
        try {
            CacheService::warmUpCache();

            Log::info('Cache warming initiated by user', [
                'user_id' => auth()->id(),
                'timestamp' => now()->toISOString(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cache warmup initiated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to warm up cache',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get API endpoint performance
     */
    public function endpoints(): JsonResponse
    {
        try {
            $endpoints = $this->getEndpointPerformance();

            return response()->json([
                'success' => true,
                'data' => $endpoints,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch endpoint performance',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics(): array
    {
        $currentHour = date('Y-m-d-H');
        $metrics = cache()->get("performance_metrics:{$currentHour}", []);

        if (empty($metrics)) {
            return [
                'avg_response_time' => 0,
                'total_requests' => 0,
                'error_rate' => 0,
                'avg_memory_usage' => 0,
                'avg_query_count' => 0,
            ];
        }

        $responseTimes = array_column($metrics, 'execution_time');
        $memoryUsage = array_column($metrics, 'memory_usage');
        $queryCounts = array_column($metrics, 'query_count');
        $errorCount = count(array_filter($metrics, fn($m) => $m['status_code'] >= 400));

        return [
            'avg_response_time' => round(array_sum($responseTimes) / count($responseTimes), 2),
            'total_requests' => count($metrics),
            'error_rate' => round(($errorCount / count($metrics)) * 100, 2),
            'avg_memory_usage' => round(array_sum($memoryUsage) / count($memoryUsage), 2),
            'avg_query_count' => round(array_sum($queryCounts) / count($queryCounts), 2),
            'slow_requests' => count(array_filter($responseTimes, fn($t) => $t > 1000)),
        ];
    }

    /**
     * Get cache statistics
     */
    private function getCacheStatistics(): array
    {
        return CacheService::getCacheStatistics();
    }

    /**
     * Get database statistics
     */
    private function getDatabaseStatistics(): array
    {
        try {
            // Get database size and statistics
            $dbStats = DB::select("
                SELECT 
                    table_name,
                    round(((data_length + index_length) / 1024 / 1024), 2) AS size_mb,
                    table_rows
                FROM information_schema.tables 
                WHERE table_schema = ?
                ORDER BY (data_length + index_length) DESC
                LIMIT 10
            ", [config('database.connections.mysql.database')]);

            $totalSize = array_sum(array_column($dbStats, 'size_mb'));
            $totalRows = array_sum(array_column($dbStats, 'table_rows'));

            return [
                'total_size_mb' => round($totalSize, 2),
                'total_rows' => $totalRows,
                'tables' => $dbStats,
                'connection_count' => $this->getActiveConnections(),
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Unable to fetch database statistics',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get system statistics
     */
    private function getSystemStatistics(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'current_memory_usage' => $this->formatBytes(memory_get_usage(true)),
            'peak_memory_usage' => $this->formatBytes(memory_get_peak_usage(true)),
            'server_load' => $this->getServerLoad(),
            'disk_space' => $this->getDiskSpace(),
        ];
    }

    /**
     * Get hourly metrics
     */
    private function getHourlyMetrics(int $hours): array
    {
        $metrics = [];
        
        for ($i = $hours; $i >= 0; $i--) {
            $hour = now()->subHours($i)->format('Y-m-d-H');
            $hourlyData = cache()->get("performance_metrics:{$hour}", []);
            
            if (!empty($hourlyData)) {
                $responseTimes = array_column($hourlyData, 'execution_time');
                $metrics[] = [
                    'hour' => $hour,
                    'avg_response_time' => round(array_sum($responseTimes) / count($responseTimes), 2),
                    'request_count' => count($hourlyData),
                    'error_count' => count(array_filter($hourlyData, fn($m) => $m['status_code'] >= 400)),
                ];
            } else {
                $metrics[] = [
                    'hour' => $hour,
                    'avg_response_time' => 0,
                    'request_count' => 0,
                    'error_count' => 0,
                ];
            }
        }

        return $metrics;
    }

    /**
     * Get slow queries from logs
     */
    private function getSlowQueriesFromLogs(): array
    {
        // This would typically read from MySQL slow query log
        // For now, return sample data
        return [
            [
                'query' => 'SELECT * FROM cars WHERE user_id = ? ORDER BY created_at DESC',
                'time' => 2.5,
                'rows_examined' => 15000,
                'timestamp' => now()->subMinutes(30)->toISOString(),
            ],
            [
                'query' => 'SELECT COUNT(*) FROM diagnosis_sessions WHERE status = ?',
                'time' => 1.8,
                'rows_examined' => 8500,
                'timestamp' => now()->subHours(1)->toISOString(),
            ],
        ];
    }

    /**
     * Get endpoint performance
     */
    private function getEndpointPerformance(): array
    {
        $currentHour = date('Y-m-d-H');
        $metrics = cache()->get("performance_metrics:{$currentHour}", []);

        if (empty($metrics)) {
            return [];
        }

        // Group by route
        $routeMetrics = [];
        foreach ($metrics as $metric) {
            $route = $metric['route'] ?? 'unknown';
            if (!isset($routeMetrics[$route])) {
                $routeMetrics[$route] = [];
            }
            $routeMetrics[$route][] = $metric;
        }

        $endpoints = [];
        foreach ($routeMetrics as $route => $routeData) {
            $responseTimes = array_column($routeData, 'execution_time');
            $endpoints[] = [
                'route' => $route,
                'avg_response_time' => round(array_sum($responseTimes) / count($responseTimes), 2),
                'min_response_time' => min($responseTimes),
                'max_response_time' => max($responseTimes),
                'request_count' => count($routeData),
                'error_count' => count(array_filter($routeData, fn($m) => $m['status_code'] >= 400)),
            ];
        }

        // Sort by average response time
        usort($endpoints, fn($a, $b) => $b['avg_response_time'] <=> $a['avg_response_time']);

        return array_slice($endpoints, 0, 20); // Top 20 slowest endpoints
    }

    /**
     * Clear performance cache
     */
    private function clearPerformanceCache(): void
    {
        // Clear hourly performance metrics
        for ($i = 0; $i < 24; $i++) {
            $hour = now()->subHours($i)->format('Y-m-d-H');
            cache()->forget("performance_metrics:{$hour}");
        }
    }

    /**
     * Get active database connections
     */
    private function getActiveConnections(): int
    {
        try {
            $result = DB::select("SHOW STATUS LIKE 'Threads_connected'");
            return (int) $result[0]->Value ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get server load
     */
    private function getServerLoad(): ?string
    {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return implode(', ', array_map(fn($l) => round($l, 2), $load));
        }
        return null;
    }

    /**
     * Get disk space info
     */
    private function getDiskSpace(): array
    {
        $totalBytes = disk_total_space('/');
        $freeBytes = disk_free_space('/');
        $usedBytes = $totalBytes - $freeBytes;

        return [
            'total' => $this->formatBytes($totalBytes),
            'used' => $this->formatBytes($usedBytes),
            'free' => $this->formatBytes($freeBytes),
            'usage_percentage' => round(($usedBytes / $totalBytes) * 100, 2),
        ];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

