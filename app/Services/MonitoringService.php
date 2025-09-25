<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\DiagnosisSession;
use App\Models\Car;

class MonitoringService
{
    /**
     * Get system metrics
     */
    public function getSystemMetrics(): array
    {
        return [
            'users' => $this->getUserMetrics(),
            'diagnoses' => $this->getDiagnosisMetrics(),
            'cars' => $this->getCarMetrics(),
            'system' => $this->getSystemInfo(),
            'performance' => $this->getPerformanceMetrics(),
        ];
    }

    /**
     * Get user metrics
     */
    private function getUserMetrics(): array
    {
        $totalUsers = User::count();
        $activeUsers = User::where('last_login_at', '>=', now()->subDays(30))->count();
        $newUsersToday = User::whereDate('created_at', today())->count();
        $newUsersThisWeek = User::where('created_at', '>=', now()->subWeek())->count();

        return [
            'total' => $totalUsers,
            'active_30_days' => $activeUsers,
            'new_today' => $newUsersToday,
            'new_this_week' => $newUsersThisWeek,
            'growth_rate' => $this->calculateGrowthRate('users'),
        ];
    }

    /**
     * Get diagnosis metrics
     */
    private function getDiagnosisMetrics(): array
    {
        $totalDiagnoses = DiagnosisSession::count();
        $completedDiagnoses = DiagnosisSession::where('status', 'completed')->count();
        $diagnosesToday = DiagnosisSession::whereDate('created_at', today())->count();
        $diagnosesThisWeek = DiagnosisSession::where('created_at', '>=', now()->subWeek())->count();
        $averageResponseTime = $this->getAverageResponseTime();

        return [
            'total' => $totalDiagnoses,
            'completed' => $completedDiagnoses,
            'today' => $diagnosesToday,
            'this_week' => $diagnosesThisWeek,
            'completion_rate' => $totalDiagnoses > 0 ? round(($completedDiagnoses / $totalDiagnoses) * 100, 2) : 0,
            'average_response_time' => $averageResponseTime,
        ];
    }

    /**
     * Get car metrics
     */
    private function getCarMetrics(): array
    {
        $totalCars = Car::count();
        $carsToday = Car::whereDate('created_at', today())->count();
        $carsThisWeek = Car::where('created_at', '>=', now()->subWeek())->count();
        $popularBrands = $this->getPopularBrands();

        return [
            'total' => $totalCars,
            'today' => $carsToday,
            'this_week' => $carsThisWeek,
            'popular_brands' => $popularBrands,
        ];
    }

    /**
     * Get system info
     */
    private function getSystemInfo(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'memory_usage' => $this->getMemoryUsage(),
            'disk_usage' => $this->getDiskUsage(),
            'uptime' => $this->getUptime(),
        ];
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics(): array
    {
        return [
            'cache_hit_rate' => $this->getCacheHitRate(),
            'database_connections' => $this->getDatabaseConnections(),
            'queue_size' => $this->getQueueSize(),
            'error_rate' => $this->getErrorRate(),
        ];
    }

    /**
     * Log system event
     */
    public function logEvent(string $event, array $data = []): void
    {
        Log::info("System Event: {$event}", array_merge([
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ], $data));
    }

    /**
     * Get popular brands
     */
    private function getPopularBrands(): array
    {
        return Car::select('brand')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('brand')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    /**
     * Calculate growth rate
     */
    private function calculateGrowthRate(string $type): float
    {
        $current = Cache::remember("metrics_{$type}_current", 3600, function () use ($type) {
            return match($type) {
                'users' => User::where('created_at', '>=', now()->subWeek())->count(),
                'diagnoses' => DiagnosisSession::where('created_at', '>=', now()->subWeek())->count(),
                'cars' => Car::where('created_at', '>=', now()->subWeek())->count(),
                default => 0,
            };
        });

        $previous = Cache::remember("metrics_{$type}_previous", 3600, function () use ($type) {
            return match($type) {
                'users' => User::whereBetween('created_at', [now()->subWeeks(2), now()->subWeek()])->count(),
                'diagnoses' => DiagnosisSession::whereBetween('created_at', [now()->subWeeks(2), now()->subWeek()])->count(),
                'cars' => Car::whereBetween('created_at', [now()->subWeeks(2), now()->subWeek()])->count(),
                default => 0,
            };
        });

        if ($previous == 0) return 0;
        return round((($current - $previous) / $previous) * 100, 2);
    }

    /**
     * Get average response time
     */
    private function getAverageResponseTime(): float
    {
        return Cache::remember('average_response_time', 300, function () {
            $sessions = DiagnosisSession::where('status', 'completed')
                ->where('created_at', '>=', now()->subDays(7))
                ->get();

            if ($sessions->isEmpty()) return 0;

            $totalTime = $sessions->sum(function ($session) {
                return $session->updated_at->diffInSeconds($session->created_at);
            });

            return round($totalTime / $sessions->count(), 2);
        });
    }

    /**
     * Get memory usage
     */
    private function getMemoryUsage(): string
    {
        $bytes = memory_get_usage(true);
        return $this->formatBytes($bytes);
    }

    /**
     * Get disk usage
     */
    private function getDiskUsage(): array
    {
        $total = disk_total_space('/');
        $free = disk_free_space('/');
        $used = $total - $free;

        return [
            'total' => $this->formatBytes($total),
            'used' => $this->formatBytes($used),
            'free' => $this->formatBytes($free),
            'percentage' => round(($used / $total) * 100, 2),
        ];
    }

    /**
     * Get uptime
     */
    private function getUptime(): string
    {
        $uptime = shell_exec('uptime -p');
        return trim($uptime) ?: 'Unknown';
    }

    /**
     * Get cache hit rate
     */
    private function getCacheHitRate(): float
    {
        // This would need to be implemented based on your cache driver
        return 0.0;
    }

    /**
     * Get database connections
     */
    private function getDatabaseConnections(): int
    {
        try {
            $result = DB::select('SHOW STATUS LIKE "Threads_connected"');
            return $result[0]->Value ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get queue size
     */
    private function getQueueSize(): int
    {
        // This would need to be implemented based on your queue driver
        return 0;
    }

    /**
     * Get error rate
     */
    private function getErrorRate(): float
    {
        // This would need to be implemented based on your logging system
        return 0.0;
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
