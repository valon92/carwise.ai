<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LogMonitoringService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class LogHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'logs:health-check 
                            {--detailed : Show detailed analysis}
                            {--json : Output as JSON}';

    /**
     * The console command description.
     */
    protected $description = 'Perform a comprehensive health check of application logs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $detailed = $this->option('detailed');
        $jsonOutput = $this->option('json');

        try {
            // Perform comprehensive health check
            $healthData = $this->performHealthCheck();
            
            if ($jsonOutput) {
                $this->line(json_encode($healthData, JSON_PRETTY_PRINT));
                return self::SUCCESS;
            }

            $this->displayHealthReport($healthData, $detailed);
            
            // Return appropriate exit code based on health
            return $healthData['overall_status'] === 'critical' ? self::FAILURE : self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Health check failed: " . $e->getMessage());
            
            Log::error('Log health check failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return self::FAILURE;
        }
    }

    /**
     * Perform comprehensive health check
     */
    private function performHealthCheck(): array
    {
        $healthData = [
            'timestamp' => now()->toISOString(),
            'checks' => [],
            'overall_status' => 'healthy',
            'overall_score' => 100,
            'recommendations' => []
        ];

        // Check 1: Log file accessibility
        $healthData['checks']['file_access'] = $this->checkLogFileAccess();
        
        // Check 2: Recent error rates
        $healthData['checks']['error_rates'] = $this->checkErrorRates();
        
        // Check 3: Critical patterns
        $healthData['checks']['critical_patterns'] = $this->checkCriticalPatterns();
        
        // Check 4: Log file sizes
        $healthData['checks']['file_sizes'] = $this->checkLogFileSizes();
        
        // Check 5: Disk space
        $healthData['checks']['disk_space'] = $this->checkDiskSpace();
        
        // Check 6: Error trends
        $healthData['checks']['error_trends'] = $this->checkErrorTrends();

        // Calculate overall status
        $this->calculateOverallStatus($healthData);

        return $healthData;
    }

    /**
     * Check log file accessibility
     */
    private function checkLogFileAccess(): array
    {
        $logPath = storage_path('logs/laravel.log');
        
        $check = [
            'name' => 'Log File Access',
            'status' => 'healthy',
            'score' => 100,
            'details' => []
        ];

        if (!File::exists($logPath)) {
            $check['status'] = 'warning';
            $check['score'] = 50;
            $check['details'][] = 'Main log file does not exist';
        } else {
            if (!File::isReadable($logPath)) {
                $check['status'] = 'critical';
                $check['score'] = 0;
                $check['details'][] = 'Log file is not readable';
            } else {
                $check['details'][] = 'Log file is accessible';
            }

            // Check file age
            $lastModified = File::lastModified($logPath);
            $hoursSinceModified = (time() - $lastModified) / 3600;
            
            if ($hoursSinceModified > 24) {
                $check['status'] = 'warning';
                $check['score'] = 70;
                $check['details'][] = 'Log file not updated in ' . round($hoursSinceModified) . ' hours';
            }
        }

        return $check;
    }

    /**
     * Check recent error rates
     */
    private function checkErrorRates(): array
    {
        $check = [
            'name' => 'Error Rates',
            'status' => 'healthy',
            'score' => 100,
            'details' => []
        ];

        try {
            $recentLogs = LogMonitoringService::monitorRecentLogs(60);
            $errorRate = $recentLogs['analysis']['error_rate'];

            $check['details'][] = "Error rate: {$errorRate}%";

            if ($errorRate > 20) {
                $check['status'] = 'critical';
                $check['score'] = 20;
                $check['details'][] = 'Extremely high error rate';
            } elseif ($errorRate > 10) {
                $check['status'] = 'critical';
                $check['score'] = 40;
                $check['details'][] = 'High error rate';
            } elseif ($errorRate > 5) {
                $check['status'] = 'warning';
                $check['score'] = 70;
                $check['details'][] = 'Elevated error rate';
            } else {
                $check['details'][] = 'Error rate within acceptable range';
            }

        } catch (\Exception $e) {
            $check['status'] = 'critical';
            $check['score'] = 0;
            $check['details'][] = 'Failed to analyze error rates: ' . $e->getMessage();
        }

        return $check;
    }

    /**
     * Check for critical error patterns
     */
    private function checkCriticalPatterns(): array
    {
        $check = [
            'name' => 'Critical Error Patterns',
            'status' => 'healthy',
            'score' => 100,
            'details' => []
        ];

        try {
            $patterns = LogMonitoringService::monitorCriticalPatterns();
            $totalCritical = 0;

            foreach ($patterns as $category => $data) {
                if ($data['count'] > 0) {
                    $totalCritical += $data['count'];
                    $check['details'][] = ucfirst(str_replace('_', ' ', $category)) . ": {$data['count']} occurrences";
                }
            }

            if ($totalCritical > 10) {
                $check['status'] = 'critical';
                $check['score'] = 30;
                $check['details'][] = 'Multiple critical patterns detected';
            } elseif ($totalCritical > 5) {
                $check['status'] = 'warning';
                $check['score'] = 60;
                $check['details'][] = 'Some critical patterns detected';
            } elseif ($totalCritical === 0) {
                $check['details'][] = 'No critical patterns detected';
            }

        } catch (\Exception $e) {
            $check['status'] = 'warning';
            $check['score'] = 50;
            $check['details'][] = 'Failed to analyze critical patterns: ' . $e->getMessage();
        }

        return $check;
    }

    /**
     * Check log file sizes
     */
    private function checkLogFileSizes(): array
    {
        $check = [
            'name' => 'Log File Sizes',
            'status' => 'healthy',
            'score' => 100,
            'details' => []
        ];

        try {
            $logPath = storage_path('logs');
            $logFiles = File::glob($logPath . '/*.log');
            $totalSize = 0;

            foreach ($logFiles as $file) {
                $size = File::size($file);
                $totalSize += $size;
                
                $sizeFormatted = $this->formatBytes($size);
                $fileName = basename($file);
                
                if ($size > 100 * 1024 * 1024) { // 100MB
                    $check['status'] = 'warning';
                    $check['score'] = min($check['score'], 70);
                    $check['details'][] = "{$fileName}: {$sizeFormatted} (large)";
                } else {
                    $check['details'][] = "{$fileName}: {$sizeFormatted}";
                }
            }

            $totalSizeFormatted = $this->formatBytes($totalSize);
            $check['details'][] = "Total log size: {$totalSizeFormatted}";

            if ($totalSize > 500 * 1024 * 1024) { // 500MB
                $check['status'] = 'critical';
                $check['score'] = 40;
                $check['details'][] = 'Log files are consuming excessive disk space';
            } elseif ($totalSize > 200 * 1024 * 1024) { // 200MB
                $check['status'] = 'warning';
                $check['score'] = 70;
                $check['details'][] = 'Log files are getting large, consider archiving';
            }

        } catch (\Exception $e) {
            $check['status'] = 'warning';
            $check['score'] = 50;
            $check['details'][] = 'Failed to check log file sizes: ' . $e->getMessage();
        }

        return $check;
    }

    /**
     * Check available disk space
     */
    private function checkDiskSpace(): array
    {
        $check = [
            'name' => 'Disk Space',
            'status' => 'healthy',
            'score' => 100,
            'details' => []
        ];

        try {
            $logPath = storage_path('logs');
            $freeBytes = disk_free_space($logPath);
            $totalBytes = disk_total_space($logPath);
            
            if ($freeBytes !== false && $totalBytes !== false) {
                $usedBytes = $totalBytes - $freeBytes;
                $usagePercent = ($usedBytes / $totalBytes) * 100;
                
                $check['details'][] = "Disk usage: " . round($usagePercent, 1) . "%";
                $check['details'][] = "Free space: " . $this->formatBytes($freeBytes);

                if ($usagePercent > 95) {
                    $check['status'] = 'critical';
                    $check['score'] = 10;
                    $check['details'][] = 'Critical: Very low disk space';
                } elseif ($usagePercent > 90) {
                    $check['status'] = 'warning';
                    $check['score'] = 50;
                    $check['details'][] = 'Warning: Low disk space';
                } elseif ($usagePercent > 80) {
                    $check['status'] = 'warning';
                    $check['score'] = 80;
                    $check['details'][] = 'Monitor: Disk space getting low';
                }
            } else {
                $check['status'] = 'warning';
                $check['score'] = 50;
                $check['details'][] = 'Unable to determine disk space';
            }

        } catch (\Exception $e) {
            $check['status'] = 'warning';
            $check['score'] = 50;
            $check['details'][] = 'Failed to check disk space: ' . $e->getMessage();
        }

        return $check;
    }

    /**
     * Check error trends
     */
    private function checkErrorTrends(): array
    {
        $check = [
            'name' => 'Error Trends',
            'status' => 'healthy',
            'score' => 100,
            'details' => []
        ];

        try {
            $trends = LogMonitoringService::getErrorTrends(6); // Last 6 hours
            
            if (count($trends) >= 2) {
                $recent = array_slice($trends, -2); // Last 2 hours
                $increase = $recent[1]['errors'] - $recent[0]['errors'];
                
                $check['details'][] = "Recent trend: " . ($increase > 0 ? "+{$increase}" : $increase) . " errors";

                if ($increase > 10) {
                    $check['status'] = 'warning';
                    $check['score'] = 60;
                    $check['details'][] = 'Error rate is increasing';
                } elseif ($increase > 20) {
                    $check['status'] = 'critical';
                    $check['score'] = 30;
                    $check['details'][] = 'Sharp increase in error rate';
                } else {
                    $check['details'][] = 'Error trend is stable';
                }
            } else {
                $check['details'][] = 'Insufficient data for trend analysis';
            }

        } catch (\Exception $e) {
            $check['status'] = 'warning';
            $check['score'] = 50;
            $check['details'][] = 'Failed to analyze error trends: ' . $e->getMessage();
        }

        return $check;
    }

    /**
     * Calculate overall status
     */
    private function calculateOverallStatus(array &$healthData): void
    {
        $totalScore = 0;
        $checkCount = 0;
        $criticalCount = 0;
        $warningCount = 0;

        foreach ($healthData['checks'] as $check) {
            $totalScore += $check['score'];
            $checkCount++;

            if ($check['status'] === 'critical') {
                $criticalCount++;
            } elseif ($check['status'] === 'warning') {
                $warningCount++;
            }
        }

        $healthData['overall_score'] = $checkCount > 0 ? round($totalScore / $checkCount) : 100;

        if ($criticalCount > 0) {
            $healthData['overall_status'] = 'critical';
        } elseif ($warningCount > 0) {
            $healthData['overall_status'] = 'warning';
        } else {
            $healthData['overall_status'] = 'healthy';
        }

        // Generate recommendations
        if ($criticalCount > 0) {
            $healthData['recommendations'][] = 'Immediate attention required for critical issues';
        }
        if ($warningCount > 0) {
            $healthData['recommendations'][] = 'Review and address warning conditions';
        }
        if ($healthData['overall_score'] < 80) {
            $healthData['recommendations'][] = 'Consider implementing log rotation and monitoring improvements';
        }
    }

    /**
     * Display health report
     */
    private function displayHealthReport(array $healthData, bool $detailed): void
    {
        $this->info("🏥 Log Health Check Report");
        $this->info("==========================");
        $this->line("Timestamp: " . $healthData['timestamp']);
        $this->newLine();

        // Overall status
        $statusColor = $this->getStatusColor($healthData['overall_status']);
        $this->line("<{$statusColor}>Overall Status: " . strtoupper($healthData['overall_status']) . "</{$statusColor}>");
        $this->line("Overall Score: {$healthData['overall_score']}/100");
        $this->newLine();

        // Individual checks
        foreach ($healthData['checks'] as $check) {
            $statusColor = $this->getStatusColor($check['status']);
            $this->line("<{$statusColor}>• {$check['name']}: {$check['status']} ({$check['score']}/100)</{$statusColor}>");
            
            if ($detailed) {
                foreach ($check['details'] as $detail) {
                    $this->line("    {$detail}");
                }
                $this->newLine();
            }
        }

        // Recommendations
        if (!empty($healthData['recommendations'])) {
            $this->newLine();
            $this->info("💡 Recommendations:");
            foreach ($healthData['recommendations'] as $recommendation) {
                $this->line("  • {$recommendation}");
            }
        }
    }

    /**
     * Get color for status
     */
    private function getStatusColor(string $status): string
    {
        return match($status) {
            'critical' => 'fg=red',
            'warning' => 'fg=yellow',
            'healthy' => 'fg=green',
            default => 'fg=white'
        };
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}

