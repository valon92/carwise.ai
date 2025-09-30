<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PerformanceMonitoring
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        
        // Count queries before request
        $queryCountBefore = $this->getQueryCount();
        
        // Execute request
        $response = $next($request);
        
        // Calculate metrics
        $executionTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds
        $memoryUsage = memory_get_usage(true) - $startMemory;
        $queryCount = $this->getQueryCount() - $queryCountBefore;
        $peakMemory = memory_get_peak_usage(true);
        
        // Log performance metrics
        $this->logPerformanceMetrics($request, $response, [
            'execution_time' => $executionTime,
            'memory_usage' => $memoryUsage,
            'peak_memory' => $peakMemory,
            'query_count' => $queryCount,
            'response_size' => strlen($response->getContent()),
        ]);
        
        // Add performance headers for debugging
        if (config('app.debug') || $request->header('X-Debug-Performance')) {
            $response->header('X-Execution-Time', round($executionTime, 2) . 'ms');
            $response->header('X-Memory-Usage', $this->formatBytes($memoryUsage));
            $response->header('X-Query-Count', $queryCount);
            $response->header('X-Response-Size', $this->formatBytes(strlen($response->getContent())));
        }
        
        // Alert on slow requests
        if ($executionTime > 2000) { // 2 seconds
            $this->alertSlowRequest($request, $executionTime, $queryCount);
        }
        
        // Alert on high memory usage
        if ($memoryUsage > 50 * 1024 * 1024) { // 50MB
            $this->alertHighMemoryUsage($request, $memoryUsage);
        }
        
        return $response;
    }

    /**
     * Get current query count
     */
    private function getQueryCount(): int
    {
        return count(DB::getQueryLog());
    }

    /**
     * Log performance metrics
     */
    private function logPerformanceMetrics(Request $request, Response $response, array $metrics): void
    {
        $logData = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'route' => $request->route()?->getName(),
            'status_code' => $response->getStatusCode(),
            'user_id' => $request->user()?->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'execution_time_ms' => round($metrics['execution_time'], 2),
            'memory_usage_mb' => round($metrics['memory_usage'] / 1024 / 1024, 2),
            'peak_memory_mb' => round($metrics['peak_memory'] / 1024 / 1024, 2),
            'query_count' => $metrics['query_count'],
            'response_size_kb' => round($metrics['response_size'] / 1024, 2),
            'timestamp' => now()->toISOString(),
        ];

        // Log at different levels based on performance
        if ($metrics['execution_time'] > 5000) { // > 5 seconds
            Log::error('Very slow request detected', $logData);
        } elseif ($metrics['execution_time'] > 2000) { // > 2 seconds
            Log::warning('Slow request detected', $logData);
        } elseif ($metrics['execution_time'] > 1000) { // > 1 second
            Log::info('Slow request', $logData);
        } else {
            Log::debug('Request performance', $logData);
        }

        // Store metrics in cache for dashboard
        $this->storeMetricsForDashboard($logData);
    }

    /**
     * Store performance metrics for dashboard display
     */
    private function storeMetricsForDashboard(array $metrics): void
    {
        try {
            $key = 'performance_metrics:' . date('Y-m-d-H'); // Group by hour
            
            // Get existing metrics
            $existingMetrics = cache()->get($key, []);
            
            // Add new metric
            $existingMetrics[] = [
                'timestamp' => $metrics['timestamp'],
                'execution_time' => $metrics['execution_time_ms'],
                'memory_usage' => $metrics['memory_usage_mb'],
                'query_count' => $metrics['query_count'],
                'route' => $metrics['route'],
                'status_code' => $metrics['status_code'],
            ];
            
            // Keep only last 1000 entries per hour
            if (count($existingMetrics) > 1000) {
                $existingMetrics = array_slice($existingMetrics, -1000);
            }
            
            // Store for 24 hours
            cache()->put($key, $existingMetrics, 60 * 24);
            
        } catch (\Exception $e) {
            Log::error('Failed to store performance metrics', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Alert on slow requests
     */
    private function alertSlowRequest(Request $request, float $executionTime, int $queryCount): void
    {
        $alertData = [
            'alert_type' => 'slow_request',
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'execution_time' => round($executionTime, 2) . 'ms',
            'query_count' => $queryCount,
            'user_id' => $request->user()?->id,
            'timestamp' => now()->toISOString(),
        ];

        Log::warning('ALERT: Slow request detected', $alertData);

        // Send to external monitoring service if configured
        $this->sendToMonitoringService('slow_request', $alertData);
    }

    /**
     * Alert on high memory usage
     */
    private function alertHighMemoryUsage(Request $request, int $memoryUsage): void
    {
        $alertData = [
            'alert_type' => 'high_memory',
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'memory_usage' => $this->formatBytes($memoryUsage),
            'user_id' => $request->user()?->id,
            'timestamp' => now()->toISOString(),
        ];

        Log::warning('ALERT: High memory usage detected', $alertData);
        
        $this->sendToMonitoringService('high_memory', $alertData);
    }

    /**
     * Send alert to external monitoring service
     */
    private function sendToMonitoringService(string $alertType, array $data): void
    {
        // Implement integration with services like:
        // - Sentry
        // - New Relic
        // - DataDog
        // - Custom webhook
        
        if (config('services.monitoring.enabled')) {
            try {
                // Example implementation
                $webhookUrl = config('services.monitoring.webhook_url');
                if ($webhookUrl) {
                    \Http::timeout(5)->post($webhookUrl, [
                        'type' => $alertType,
                        'data' => $data,
                        'service' => 'carwise-ai',
                        'environment' => config('app.env'),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send monitoring alert', [
                    'error' => $e->getMessage(),
                    'alert_type' => $alertType,
                ]);
            }
        }
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

