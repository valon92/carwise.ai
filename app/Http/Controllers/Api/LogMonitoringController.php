<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LogMonitoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogMonitoringController extends Controller
{
    /**
     * Get recent error monitoring dashboard
     */
    public function dashboard(): JsonResponse
    {
        try {
            $recentLogs = LogMonitoringService::monitorRecentLogs(60);
            $systemHealth = LogMonitoringService::getSystemHealth();
            $criticalPatterns = LogMonitoringService::monitorCriticalPatterns();
            $errorTrends = LogMonitoringService::getErrorTrends(24);

            return response()->json([
                'success' => true,
                'data' => [
                    'system_health' => $systemHealth,
                    'recent_logs' => $recentLogs,
                    'critical_patterns' => $criticalPatterns,
                    'error_trends' => $errorTrends,
                    'updated_at' => now()->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Log monitoring dashboard failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load monitoring dashboard',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get recent logs with filtering
     */
    public function recentLogs(Request $request): JsonResponse
    {
        try {
            $minutes = $request->get('minutes', 60);
            $level = $request->get('level'); // filter by log level
            
            $logs = LogMonitoringService::monitorRecentLogs($minutes);
            
            // Apply level filter if specified
            if ($level) {
                $logs['recent_errors'] = array_filter($logs['recent_errors'], function($entry) use ($level) {
                    return $entry['level'] === $level;
                });
            }

            return response()->json([
                'success' => true,
                'data' => $logs,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch recent logs',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get error trends over time
     */
    public function errorTrends(Request $request): JsonResponse
    {
        try {
            $hours = $request->get('hours', 24);
            $trends = LogMonitoringService::getErrorTrends($hours);

            return response()->json([
                'success' => true,
                'data' => [
                    'trends' => $trends,
                    'period_hours' => $hours,
                    'generated_at' => now()->toISOString()
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch error trends',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get critical error patterns
     */
    public function criticalPatterns(): JsonResponse
    {
        try {
            $patterns = LogMonitoringService::monitorCriticalPatterns();

            return response()->json([
                'success' => true,
                'data' => $patterns,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze critical patterns',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get system health status
     */
    public function systemHealth(): JsonResponse
    {
        try {
            $health = LogMonitoringService::getSystemHealth();

            return response()->json([
                'success' => true,
                'data' => $health,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check system health',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Archive old logs
     */
    public function archiveLogs(Request $request): JsonResponse
    {
        try {
            $daysToKeep = $request->get('days', 30);
            $result = LogMonitoringService::archiveLogs($daysToKeep);

            if ($result['status'] === 'error') {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 500);
            }

            Log::info('Logs archived by user', [
                'user_id' => auth()->id(),
                'days_kept' => $daysToKeep,
                'archived_files' => count($result['archived_files'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Logs archived successfully',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to archive logs',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export logs for analysis
     */
    public function exportLogs(Request $request): JsonResponse
    {
        try {
            $hours = $request->get('hours', 24);
            $format = $request->get('format', 'json');

            $result = LogMonitoringService::exportLogs($hours, $format);

            if ($result['status'] === 'error') {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 500);
            }

            Log::info('Logs exported by user', [
                'user_id' => auth()->id(),
                'hours' => $hours,
                'format' => $format,
                'filename' => $result['filename']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Logs exported successfully',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export logs',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear log monitoring cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            LogMonitoringService::clearLogCache();

            Log::info('Log monitoring cache cleared by user', [
                'user_id' => auth()->id(),
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Log monitoring cache cleared successfully',
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
     * Search logs with specific criteria
     */
    public function searchLogs(Request $request): JsonResponse
    {
        try {
            $query = $request->get('query');
            $level = $request->get('level');
            $hours = $request->get('hours', 24);

            if (!$query) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search query is required',
                ], 400);
            }

            $recentLogs = LogMonitoringService::monitorRecentLogs($hours * 60);
            $results = [];

            foreach ($recentLogs['analysis']['errors'] as $entry) {
                $matchesQuery = stripos($entry['message'], $query) !== false;
                $matchesLevel = !$level || $entry['level'] === $level;

                if ($matchesQuery && $matchesLevel) {
                    $results[] = $entry;
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'query' => $query,
                    'level' => $level,
                    'period_hours' => $hours,
                    'total_matches' => count($results),
                    'results' => array_slice($results, 0, 100), // Limit results
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search logs',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get log statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $hours = $request->get('hours', 24);
            $recentLogs = LogMonitoringService::monitorRecentLogs($hours * 60);
            
            $stats = [
                'period_hours' => $hours,
                'total_entries' => $recentLogs['total_entries'],
                'by_level' => $recentLogs['analysis']['by_level'],
                'error_rate' => $recentLogs['analysis']['error_rate'],
                'unique_errors' => $recentLogs['analysis']['unique_errors'],
                'top_errors' => $recentLogs['analysis']['top_errors'],
                'status' => $recentLogs['summary']['status']
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get log statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get real-time log alerts
     */
    public function alerts(): JsonResponse
    {
        try {
            $recentLogs = LogMonitoringService::monitorRecentLogs(15); // Last 15 minutes
            $alerts = [];

            // Check for critical issues
            if ($recentLogs['analysis']['error_rate'] > 10) {
                $alerts[] = [
                    'level' => 'critical',
                    'message' => 'High error rate detected: ' . $recentLogs['analysis']['error_rate'] . '%',
                    'timestamp' => now()->toISOString(),
                    'action_required' => true
                ];
            }

            // Check for new error patterns
            foreach ($recentLogs['analysis']['errors'] as $error) {
                if (stripos($error['message'], 'CRITICAL') !== false || 
                    stripos($error['message'], 'Fatal') !== false) {
                    $alerts[] = [
                        'level' => 'critical',
                        'message' => 'Critical error detected: ' . substr($error['message'], 0, 100),
                        'timestamp' => $error['timestamp'],
                        'action_required' => true
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'alerts' => $alerts,
                    'alert_count' => count($alerts),
                    'last_check' => now()->toISOString()
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch alerts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}



