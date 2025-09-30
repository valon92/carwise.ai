<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DatabaseBackupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('can:admin'); // Only admin can access backup functions
    }

    /**
     * Get backup dashboard data
     */
    public function dashboard(): JsonResponse
    {
        try {
            $backups = DatabaseBackupService::listBackups();
            $statistics = DatabaseBackupService::getBackupStatistics();
            
            $recentBackups = array_slice($backups, 0, 5); // Last 5 backups
            
            $healthScore = $this->calculateBackupHealthScore($backups, $statistics);

            return response()->json([
                'success' => true,
                'data' => [
                    'statistics' => $statistics,
                    'recent_backups' => $recentBackups,
                    'health_score' => $healthScore,
                    'recommendations' => $this->getRecommendations($backups, $statistics)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch backup dashboard data', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch backup data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * List all backups
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $backups = DatabaseBackupService::listBackups();
            
            // Apply filters if provided
            if ($request->has('type')) {
                $backups = array_filter($backups, function($backup) use ($request) {
                    return ($backup['type'] ?? 'unknown') === $request->input('type');
                });
            }

            if ($request->has('days')) {
                $maxAge = (int) $request->input('days');
                $backups = array_filter($backups, function($backup) use ($maxAge) {
                    return $backup['age_days'] <= $maxAge;
                });
            }

            // Paginate results
            $page = (int) $request->input('page', 1);
            $perPage = (int) $request->input('per_page', 10);
            $offset = ($page - 1) * $perPage;
            
            $total = count($backups);
            $paginatedBackups = array_slice($backups, $offset, $perPage);

            return response()->json([
                'success' => true,
                'data' => $paginatedBackups,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => ceil($total / $perPage)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to list backups', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to list backups',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new backup
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'string|in:full,structure,data,incremental',
            'compression' => 'string|in:gzip,bzip2,none',
            'verify' => 'boolean',
            'cleanup_old' => 'boolean',
            'retention_days' => 'integer|min:1|max:365',
            'storage_disk' => 'string',
            'include_logs' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid backup parameters',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $options = array_merge([
                'type' => 'full',
                'compression' => 'gzip',
                'verify' => true,
                'cleanup_old' => true,
                'retention_days' => 30,
                'storage_disk' => 'local',
                'include_logs' => false
            ], $request->all());

            Log::info('Manual backup initiated via API', [
                'user_id' => auth()->id(),
                'options' => $options
            ]);

            $result = DatabaseBackupService::createBackup($options);

            if ($result['status'] === 'success') {
                return response()->json([
                    'success' => true,
                    'message' => 'Backup created successfully',
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup failed',
                    'error' => $result['message'] ?? 'Unknown error'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Backup creation failed via API', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Backup creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get backup statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $statistics = DatabaseBackupService::getBackupStatistics();
            
            // Add additional analytics
            $statistics['health_score'] = $this->calculateBackupHealthScore(
                DatabaseBackupService::listBackups(),
                $statistics
            );

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch backup statistics', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a backup
     */
    public function destroy(string $filename): JsonResponse
    {
        try {
            // Validate filename to prevent path traversal
            if (str_contains($filename, '..') || str_contains($filename, '/')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid filename'
                ], 400);
            }

            $result = DatabaseBackupService::deleteBackup($filename);

            if ($result['status'] === 'success') {
                Log::info('Backup deleted via API', [
                    'user_id' => auth()->id(),
                    'filename' => $filename
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Backup deleted successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete backup',
                    'error' => $result['error']
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Backup deletion failed via API', [
                'user_id' => auth()->id(),
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Backup deletion failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download a backup file
     */
    public function download(string $filename): \Symfony\Component\HttpFoundation\BinaryFileResponse|JsonResponse
    {
        try {
            // Validate filename
            if (str_contains($filename, '..') || str_contains($filename, '/')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid filename'
                ], 400);
            }

            $backupPath = storage_path('backups/database/' . $filename);

            if (!file_exists($backupPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup file not found'
                ], 404);
            }

            Log::info('Backup downloaded via API', [
                'user_id' => auth()->id(),
                'filename' => $filename
            ]);

            return response()->download($backupPath, $filename, [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);

        } catch (\Exception $e) {
            Log::error('Backup download failed via API', [
                'user_id' => auth()->id(),
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Backup download failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify backup integrity
     */
    public function verify(string $filename): JsonResponse
    {
        try {
            // Validate filename
            if (str_contains($filename, '..') || str_contains($filename, '/')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid filename'
                ], 400);
            }

            $backupPath = storage_path('backups/database/' . $filename);

            if (!file_exists($backupPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup file not found'
                ], 404);
            }

            // Get database driver for verification
            $connection = config('database.default');
            $dbDriver = config("database.connections.{$connection}.driver");

            // Use reflection to call the private verification method
            $service = new \ReflectionClass(DatabaseBackupService::class);
            $method = $service->getMethod('verifyBackup');
            $method->setAccessible(true);

            $verification = $method->invoke(null, $backupPath, $dbDriver);

            Log::info('Backup verification requested via API', [
                'user_id' => auth()->id(),
                'filename' => $filename,
                'result' => $verification['status']
            ]);

            return response()->json([
                'success' => true,
                'data' => $verification
            ]);

        } catch (\Exception $e) {
            Log::error('Backup verification failed via API', [
                'user_id' => auth()->id(),
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Backup verification failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate backup health score
     */
    private function calculateBackupHealthScore(array $backups, array $statistics): array
    {
        $score = 100;
        $issues = [];
        $recommendations = [];

        // Check if we have any backups
        if (empty($backups)) {
            $score = 0;
            $issues[] = 'No backups found';
            $recommendations[] = 'Create your first backup immediately';
            return [
                'score' => $score,
                'status' => 'critical',
                'issues' => $issues,
                'recommendations' => $recommendations
            ];
        }

        // Check recent backup (last 24 hours)
        $latestBackup = $backups[0];
        $latestAge = $latestBackup['age_days'];
        
        if ($latestAge > 1) {
            $score -= 30;
            $issues[] = "Latest backup is {$latestAge} days old";
            $recommendations[] = 'Schedule more frequent backups';
        }

        // Check backup verification
        $verifiedCount = 0;
        foreach ($backups as $backup) {
            if (isset($backup['verification']) && $backup['verification']['status'] === 'valid') {
                $verifiedCount++;
            }
        }

        if ($verifiedCount === 0) {
            $score -= 25;
            $issues[] = 'No verified backups found';
            $recommendations[] = 'Enable backup verification';
        } elseif ($verifiedCount / count($backups) < 0.8) {
            $score -= 15;
            $issues[] = 'Low backup verification rate';
            $recommendations[] = 'Check backup verification process';
        }

        // Check backup diversity (types)
        $uniqueTypes = count($statistics['types'] ?? []);
        if ($uniqueTypes === 1) {
            $score -= 10;
            $issues[] = 'Only one backup type used';
            $recommendations[] = 'Consider using multiple backup types (full, incremental)';
        }

        // Check retention
        if (count($backups) < 7) {
            $score -= 10;
            $issues[] = 'Few backup copies available';
            $recommendations[] = 'Increase backup retention period';
        }

        // Check compression usage
        $compressionUsage = $statistics['compression_usage'] ?? [];
        if (isset($compressionUsage['none']) && $compressionUsage['none'] > 0) {
            $score -= 5;
            $issues[] = 'Some backups are not compressed';
            $recommendations[] = 'Enable compression to save storage space';
        }

        // Determine status based on score
        $status = 'excellent';
        if ($score < 60) {
            $status = 'critical';
        } elseif ($score < 80) {
            $status = 'warning';
        } elseif ($score < 95) {
            $status = 'good';
        }

        return [
            'score' => max(0, $score),
            'status' => $status,
            'issues' => $issues,
            'recommendations' => $recommendations
        ];
    }

    /**
     * Get backup recommendations
     */
    private function getRecommendations(array $backups, array $statistics): array
    {
        $recommendations = [];

        if (empty($backups)) {
            $recommendations[] = [
                'type' => 'critical',
                'title' => 'No Backups Found',
                'message' => 'Create your first database backup immediately',
                'action' => 'Create Backup'
            ];
            return $recommendations;
        }

        // Check backup frequency
        $latestAge = $backups[0]['age_days'];
        if ($latestAge > 1) {
            $recommendations[] = [
                'type' => 'warning',
                'title' => 'Outdated Backup',
                'message' => "Latest backup is {$latestAge} days old. Consider daily backups.",
                'action' => 'Schedule Daily Backups'
            ];
        }

        // Check backup verification
        $unverifiedBackups = array_filter($backups, function($backup) {
            return !isset($backup['verification']) || $backup['verification']['status'] !== 'valid';
        });

        if (!empty($unverifiedBackups)) {
            $count = count($unverifiedBackups);
            $recommendations[] = [
                'type' => 'info',
                'title' => 'Unverified Backups',
                'message' => "{$count} backup(s) need verification",
                'action' => 'Verify Backups'
            ];
        }

        // Check storage usage
        if (($statistics['total_size'] ?? 0) > 1024 * 1024 * 1024) { // > 1GB
            $recommendations[] = [
                'type' => 'info',
                'title' => 'Large Backup Size',
                'message' => 'Consider enabling compression or archiving old backups',
                'action' => 'Optimize Storage'
            ];
        }

        return $recommendations;
    }
}

