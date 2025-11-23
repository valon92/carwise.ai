<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LogMonitoringService
{
    const ERROR_PATTERNS = [
        'critical' => [
            'CRITICAL',
            'Fatal error',
            'Emergency',
            'Database connection failed',
            'Out of memory',
            'Maximum execution time exceeded'
        ],
        'error' => [
            'ERROR',
            'Exception',
            'Stack trace',
            'Failed to',
            'Unable to',
            'Connection refused'
        ],
        'warning' => [
            'WARNING',
            'WARN',
            'Deprecated',
            'Notice',
            'Slow query',
            'High memory usage'
        ]
    ];

    const LOG_LEVELS = [
        'emergency' => 0,
        'alert' => 1,
        'critical' => 2,
        'error' => 3,
        'warning' => 4,
        'notice' => 5,
        'info' => 6,
        'debug' => 7
    ];

    /**
     * Monitor recent logs for errors
     */
    public static function monitorRecentLogs(int $minutes = 60): array
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (!File::exists($logPath)) {
            return [
                'status' => 'no_logs',
                'message' => 'No log file found',
                'logs' => []
            ];
        }

        $recentLogs = self::getRecentLogEntries($logPath, $minutes);
        $analysis = self::analyzeLogEntries($recentLogs);

        return [
            'status' => 'success',
            'period' => $minutes . ' minutes',
            'total_entries' => count($recentLogs),
            'analysis' => $analysis,
            'recent_errors' => array_slice($analysis['errors'], 0, 10),
            'summary' => self::generateLogSummary($analysis)
        ];
    }

    /**
     * Get recent log entries from file
     */
    private static function getRecentLogEntries(string $logPath, int $minutes): array
    {
        $entries = [];
        $cutoffTime = Carbon::now()->subMinutes($minutes);
        
        try {
            $logContent = File::get($logPath);
            $lines = explode("\n", $logContent);
            
            // Reverse to get most recent first
            $lines = array_reverse($lines);
            
            foreach ($lines as $line) {
                if (empty(trim($line))) continue;
                
                $entry = self::parseLogEntry($line);
                if ($entry && isset($entry['timestamp'])) {
                    $entryTime = Carbon::parse($entry['timestamp']);
                    
                    if ($entryTime->gte($cutoffTime)) {
                        $entries[] = $entry;
                    } else {
                        // Since logs are in reverse chronological order,
                        // we can break when we hit older entries
                        break;
                    }
                }
                
                // Limit to prevent memory issues
                if (count($entries) >= 10000) break;
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to read log file', [
                'error' => $e->getMessage(),
                'file' => $logPath
            ]);
        }

        return $entries;
    }

    /**
     * Parse individual log entry
     */
    private static function parseLogEntry(string $line): ?array
    {
        // Laravel log format: [2024-01-01 12:00:00] local.ERROR: Message
        $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.+)/';
        
        if (preg_match($pattern, $line, $matches)) {
            return [
                'timestamp' => $matches[1],
                'environment' => $matches[2],
                'level' => strtolower($matches[3]),
                'message' => $matches[4],
                'raw_line' => $line
            ];
        }

        return null;
    }

    /**
     * Analyze log entries for patterns and issues
     */
    private static function analyzeLogEntries(array $entries): array
    {
        $analysis = [
            'by_level' => [],
            'errors' => [],
            'warnings' => [],
            'patterns' => [],
            'trending_issues' => [],
            'error_rate' => 0,
            'unique_errors' => 0,
            'top_errors' => []
        ];

        $errorMessages = [];
        $levelCounts = array_fill_keys(array_keys(self::LOG_LEVELS), 0);

        foreach ($entries as $entry) {
            $level = $entry['level'];
            $message = $entry['message'];

            // Count by level
            if (isset($levelCounts[$level])) {
                $levelCounts[$level]++;
            }

            // Collect errors and warnings
            if (in_array($level, ['error', 'critical', 'emergency', 'alert'])) {
                $analysis['errors'][] = $entry;
                
                // Track unique error messages
                $cleanMessage = self::cleanErrorMessage($message);
                $errorMessages[$cleanMessage] = ($errorMessages[$cleanMessage] ?? 0) + 1;
            }

            if ($level === 'warning') {
                $analysis['warnings'][] = $entry;
            }

            // Pattern matching
            foreach (self::ERROR_PATTERNS as $severity => $patterns) {
                foreach ($patterns as $pattern) {
                    if (stripos($message, $pattern) !== false) {
                        $analysis['patterns'][$severity][] = [
                            'pattern' => $pattern,
                            'entry' => $entry,
                            'severity' => $severity
                        ];
                    }
                }
            }
        }

        $analysis['by_level'] = $levelCounts;
        $analysis['error_rate'] = count($entries) > 0 ? 
            (($levelCounts['error'] + $levelCounts['critical'] + $levelCounts['emergency']) / count($entries)) * 100 : 0;
        $analysis['unique_errors'] = count($errorMessages);

        // Top errors by frequency
        arsort($errorMessages);
        $analysis['top_errors'] = array_slice($errorMessages, 0, 10, true);

        return $analysis;
    }

    /**
     * Clean error message for grouping
     */
    private static function cleanErrorMessage(string $message): string
    {
        // Remove dynamic parts like IDs, timestamps, paths
        $cleaned = preg_replace([
            '/\b\d+\b/',           // Numbers
            '/\/[^\s]+/',          // File paths
            '/\b[a-f0-9]{8,}\b/',  // Hashes
            '/\d{4}-\d{2}-\d{2}/', // Dates
            '/\d{2}:\d{2}:\d{2}/'  // Times
        ], '[DYNAMIC]', $message);

        return substr($cleaned, 0, 200); // Truncate for grouping
    }

    /**
     * Generate log summary
     */
    private static function generateLogSummary(array $analysis): array
    {
        $errorCount = $analysis['by_level']['error'] + 
                     $analysis['by_level']['critical'] + 
                     $analysis['by_level']['emergency'];

        $status = 'healthy';
        if ($errorCount > 10) {
            $status = 'critical';
        } elseif ($errorCount > 5 || $analysis['error_rate'] > 5) {
            $status = 'warning';
        } elseif ($errorCount > 0) {
            $status = 'issues';
        }

        return [
            'status' => $status,
            'total_errors' => $errorCount,
            'error_rate' => round($analysis['error_rate'], 2),
            'unique_errors' => $analysis['unique_errors'],
            'warning_count' => $analysis['by_level']['warning'],
            'recommendations' => self::generateRecommendations($analysis)
        ];
    }

    /**
     * Generate recommendations based on log analysis
     */
    private static function generateRecommendations(array $analysis): array
    {
        $recommendations = [];

        if ($analysis['error_rate'] > 10) {
            $recommendations[] = 'URGENT: High error rate detected. Immediate investigation required.';
        }

        if (isset($analysis['patterns']['critical']) && count($analysis['patterns']['critical']) > 0) {
            $recommendations[] = 'Critical errors detected. Check database connections and memory usage.';
        }

        if ($analysis['by_level']['warning'] > 20) {
            $recommendations[] = 'High number of warnings. Consider reviewing deprecated code usage.';
        }

        if ($analysis['unique_errors'] > 5) {
            $recommendations[] = 'Multiple unique errors detected. Review recent code changes.';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'System appears stable. Continue regular monitoring.';
        }

        return $recommendations;
    }

    /**
     * Get error trends over time
     */
    public static function getErrorTrends(int $hours = 24): array
    {
        $trends = [];
        $now = Carbon::now();

        for ($i = $hours; $i >= 0; $i--) {
            $hourStart = $now->copy()->subHours($i);
            $hourEnd = $hourStart->copy()->addHour();
            
            // Skip caching for now due to Redis issues, get data directly
            $hourlyStats = self::getErrorStatsForPeriod($hourStart, $hourEnd);

            $trends[] = [
                'hour' => $hourStart->format('Y-m-d H:00'),
                'timestamp' => $hourStart->timestamp,
                'errors' => $hourlyStats['errors'],
                'warnings' => $hourlyStats['warnings'],
                'total' => $hourlyStats['total']
            ];
        }

        return $trends;
    }

    /**
     * Get error statistics for specific time period
     */
    private static function getErrorStatsForPeriod(Carbon $start, Carbon $end): array
    {
        $logPath = storage_path('logs/laravel.log');
        $stats = ['errors' => 0, 'warnings' => 0, 'total' => 0];

        if (!File::exists($logPath)) {
            return $stats;
        }

        try {
            $logContent = File::get($logPath);
            $lines = explode("\n", $logContent);

            foreach ($lines as $line) {
                $entry = self::parseLogEntry($line);
                if (!$entry) continue;

                $entryTime = Carbon::parse($entry['timestamp']);
                
                if ($entryTime->between($start, $end)) {
                    $stats['total']++;
                    
                    if (in_array($entry['level'], ['error', 'critical', 'emergency', 'alert'])) {
                        $stats['errors']++;
                    } elseif ($entry['level'] === 'warning') {
                        $stats['warnings']++;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to analyze log trends', ['error' => $e->getMessage()]);
        }

        return $stats;
    }

    /**
     * Monitor specific error patterns
     */
    public static function monitorCriticalPatterns(): array
    {
        $criticalPatterns = [
            'database_errors' => ['database connection', 'sql error', 'query failed'],
            'memory_errors' => ['out of memory', 'memory limit', 'allowed memory size'],
            'timeout_errors' => ['execution time', 'timeout', 'connection timed out'],
            'auth_errors' => ['authentication failed', 'unauthorized', 'access denied'],
            'api_errors' => ['api error', 'external service', 'http error 5']
        ];

        $results = [];
        $recentLogs = self::getRecentLogEntries(storage_path('logs/laravel.log'), 60);

        foreach ($criticalPatterns as $category => $patterns) {
            $matches = [];
            
            foreach ($recentLogs as $entry) {
                foreach ($patterns as $pattern) {
                    if (stripos($entry['message'], $pattern) !== false) {
                        $matches[] = $entry;
                        break; // Avoid duplicate matches for same entry
                    }
                }
            }

            $results[$category] = [
                'count' => count($matches),
                'recent_entries' => array_slice($matches, 0, 5)
            ];
        }

        return $results;
    }

    /**
     * Archive old logs
     */
    public static function archiveLogs(int $daysToKeep = 30): array
    {
        $logPath = storage_path('logs');
        $archivePath = storage_path('logs/archive');

        if (!File::exists($archivePath)) {
            File::makeDirectory($archivePath, 0755, true);
        }

        $archivedFiles = [];
        $cutoffDate = Carbon::now()->subDays($daysToKeep);

        try {
            $files = File::glob($logPath . '/laravel-*.log');
            
            foreach ($files as $file) {
                $fileDate = Carbon::createFromTimestamp(File::lastModified($file));
                
                if ($fileDate->lt($cutoffDate)) {
                    $filename = basename($file);
                    $archiveFile = $archivePath . '/' . $filename . '.gz';
                    
                    // Compress and move to archive
                    $content = File::get($file);
                    File::put($archiveFile, gzencode($content));
                    File::delete($file);
                    
                    $archivedFiles[] = $filename;
                }
            }

        } catch (\Exception $e) {
            Log::error('Log archiving failed', ['error' => $e->getMessage()]);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }

        return [
            'status' => 'success',
            'archived_files' => $archivedFiles,
            'archive_path' => $archivePath
        ];
    }

    /**
     * Get system health based on logs
     */
    public static function getSystemHealth(): array
    {
        $recentLogs = self::monitorRecentLogs(60);
        $criticalPatterns = self::monitorCriticalPatterns();
        
        $health = [
            'status' => 'healthy',
            'score' => 100,
            'issues' => [],
            'recommendations' => []
        ];

        // Check error rates
        if ($recentLogs['analysis']['error_rate'] > 10) {
            $health['status'] = 'critical';
            $health['score'] -= 50;
            $health['issues'][] = 'High error rate: ' . $recentLogs['analysis']['error_rate'] . '%';
        } elseif ($recentLogs['analysis']['error_rate'] > 5) {
            $health['status'] = 'warning';
            $health['score'] -= 25;
            $health['issues'][] = 'Elevated error rate: ' . $recentLogs['analysis']['error_rate'] . '%';
        }

        // Check critical patterns
        foreach ($criticalPatterns as $category => $data) {
            if ($data['count'] > 0) {
                $health['score'] -= min(20, $data['count'] * 5);
                $health['issues'][] = ucfirst(str_replace('_', ' ', $category)) . ': ' . $data['count'] . ' occurrences';
            }
        }

        // Determine final status
        if ($health['score'] < 60) {
            $health['status'] = 'critical';
        } elseif ($health['score'] < 80) {
            $health['status'] = 'warning';
        }

        $health['score'] = max(0, $health['score']);
        $health['recommendations'] = $recentLogs['summary']['recommendations'];

        return $health;
    }

    /**
     * Clear cache related to log monitoring
     */
    public static function clearLogCache(): void
    {
        try {
            $keys = Cache::get('log_monitoring_keys', []);
            foreach ($keys as $key) {
                Cache::forget($key);
            }
            Cache::forget('log_monitoring_keys');
        } catch (\Exception $e) {
            Log::warning('Failed to clear log cache', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Export logs for external analysis
     */
    public static function exportLogs(int $hours = 24, string $format = 'json'): array
    {
        $recentLogs = self::getRecentLogEntries(storage_path('logs/laravel.log'), $hours * 60);
        
        $exportData = [
            'export_time' => Carbon::now()->toISOString(),
            'period_hours' => $hours,
            'total_entries' => count($recentLogs),
            'entries' => $recentLogs
        ];

        $filename = 'log_export_' . Carbon::now()->format('Y-m-d_H-i-s') . '.' . $format;
        $filepath = storage_path('logs/exports/' . $filename);

        if (!File::exists(dirname($filepath))) {
            File::makeDirectory(dirname($filepath), 0755, true);
        }

        try {
            switch ($format) {
                case 'json':
                    File::put($filepath, json_encode($exportData, JSON_PRETTY_PRINT));
                    break;
                case 'csv':
                    $csv = self::convertToCsv($recentLogs);
                    File::put($filepath, $csv);
                    break;
                default:
                    throw new \InvalidArgumentException('Unsupported format: ' . $format);
            }

            return [
                'status' => 'success',
                'filename' => $filename,
                'filepath' => $filepath,
                'size' => File::size($filepath)
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Convert logs to CSV format
     */
    private static function convertToCsv(array $logs): string
    {
        $csv = "Timestamp,Environment,Level,Message\n";
        
        foreach ($logs as $log) {
            $csv .= sprintf(
                "%s,%s,%s,\"%s\"\n",
                $log['timestamp'],
                $log['environment'],
                $log['level'],
                str_replace('"', '""', $log['message'])
            );
        }

        return $csv;
    }
}
