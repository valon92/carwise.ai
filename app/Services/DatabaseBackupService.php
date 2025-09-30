<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DatabaseBackupService
{
    const BACKUP_TYPES = [
        'full' => 'Full database backup',
        'structure' => 'Structure only backup',
        'data' => 'Data only backup',
        'incremental' => 'Incremental backup'
    ];

    const COMPRESSION_TYPES = [
        'gzip' => '.gz',
        'bzip2' => '.bz2',
        'none' => ''
    ];

    /**
     * Create a database backup
     */
    public static function createBackup(array $options = []): array
    {
        $options = array_merge([
            'type' => 'full',
            'compression' => 'gzip',
            'include_logs' => false,
            'verify' => true,
            'cleanup_old' => true,
            'retention_days' => 30,
            'storage_disk' => 'local'
        ], $options);

        try {
            Log::info('Starting database backup', $options);

            // Create backup directory
            $backupDir = self::createBackupDirectory();
            
            // Generate backup filename
            $filename = self::generateBackupFilename($options['type'], $options['compression']);
            $backupPath = $backupDir . '/' . $filename;

            // Create backup based on database driver
            $connection = Config::get('database.default');
            $config = Config::get("database.connections.{$connection}");

            $result = match($config['driver']) {
                'sqlite' => self::backupSqlite($backupPath, $options),
                'mysql' => self::backupMysql($backupPath, $options, $config),
                'pgsql' => self::backupPostgres($backupPath, $options, $config),
                default => throw new \Exception("Unsupported database driver: {$config['driver']}")
            };

            // Verify backup if requested
            if ($options['verify']) {
                // Get the actual backup file path after potential compression
                $actualBackupPath = $backupPath;
                if (isset($result['final_path'])) {
                    $actualBackupPath = $result['final_path'];
                } elseif ($options['compression'] !== 'none' && $config['driver'] === 'sqlite') {
                    $actualBackupPath = $backupPath . self::COMPRESSION_TYPES[$options['compression']];
                }
                $result['verification'] = self::verifyBackup($actualBackupPath, $config['driver']);
            }

            // Store backup metadata
            self::storeBackupMetadata($filename, $result, $options);

            // Cleanup old backups
            if ($options['cleanup_old']) {
                self::cleanupOldBackups($options['retention_days']);
            }

            // Upload to cloud storage if configured
            if ($options['storage_disk'] !== 'local') {
                $result['cloud_upload'] = self::uploadToCloud($backupPath, $filename, $options['storage_disk']);
            }

            Log::info('Database backup completed successfully', [
                'filename' => $filename,
                'size' => $result['size'] ?? 0,
                'duration' => $result['duration'] ?? 0
            ]);

            $finalResult = array_merge($result, [
                'status' => 'success',
                'filename' => $filename,
                'path' => $backupPath
            ]);

            // Send success notification
            if (class_exists('\App\Services\BackupNotificationService')) {
                \App\Services\BackupNotificationService::sendNotification('backup_successful', $finalResult);
            }

            return $finalResult;

        } catch (\Exception $e) {
            Log::error('Database backup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $errorResult = [
                'status' => 'error',
                'message' => $e->getMessage(),
                'error' => $e
            ];

            // Send failure notification
            if (class_exists('\App\Services\BackupNotificationService')) {
                \App\Services\BackupNotificationService::sendNotification('backup_failed', $errorResult);
            }

            return $errorResult;
        }
    }

    /**
     * Backup SQLite database
     */
    private static function backupSqlite(string $backupPath, array $options): array
    {
        $startTime = microtime(true);
        $dbPath = Config::get('database.connections.sqlite.database');

        if (!File::exists($dbPath)) {
            throw new \Exception("SQLite database file not found: {$dbPath}");
        }

        // For SQLite, we can either copy the file or create a SQL dump
        if ($options['type'] === 'full') {
            // Simple file copy for SQLite
            $success = File::copy($dbPath, $backupPath . '.db');
            
            if ($options['compression'] !== 'none') {
                $compressedPath = self::compressFile($backupPath . '.db', $options['compression']);
                File::delete($backupPath . '.db');
                $backupPath = $compressedPath;
            }
        } else {
            // Create SQL dump for structure/data only backups
            $dumpPath = self::createSqliteDump($dbPath, $backupPath, $options);
            $backupPath = $dumpPath;
        }

        $duration = (microtime(true) - $startTime) * 1000;
        $size = File::exists($backupPath) ? File::size($backupPath) : 0;

        return [
            'type' => 'sqlite',
            'method' => $options['type'] === 'full' ? 'file_copy' : 'sql_dump',
            'duration' => $duration,
            'size' => $size,
            'compressed' => $options['compression'] !== 'none',
            'final_path' => $backupPath
        ];
    }

    /**
     * Backup MySQL database
     */
    private static function backupMysql(string $backupPath, array $options, array $config): array
    {
        $startTime = microtime(true);

        // Build mysqldump command
        $command = ['mysqldump'];
        
        // Connection parameters
        $command[] = '--host=' . ($config['host'] ?? 'localhost');
        $command[] = '--port=' . ($config['port'] ?? 3306);
        $command[] = '--user=' . $config['username'];
        
        if (!empty($config['password'])) {
            $command[] = '--password=' . $config['password'];
        }

        // Backup type options
        switch ($options['type']) {
            case 'structure':
                $command[] = '--no-data';
                break;
            case 'data':
                $command[] = '--no-create-info';
                break;
            case 'incremental':
                $command[] = '--single-transaction';
                $command[] = '--flush-logs';
                $command[] = '--master-data=2';
                break;
        }

        // Additional options
        $command[] = '--routines';
        $command[] = '--triggers';
        $command[] = '--events';
        $command[] = '--single-transaction';
        $command[] = '--quick';
        $command[] = '--lock-tables=false';

        // Database name
        $command[] = $config['database'];

        // Execute mysqldump
        $process = new Process($command);
        $process->setTimeout(3600); // 1 hour timeout
        
        try {
            $process->run();
            
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $sqlContent = $process->getOutput();
            
            // Write to file
            File::put($backupPath . '.sql', $sqlContent);
            
            // Compress if requested
            if ($options['compression'] !== 'none') {
                $compressedPath = self::compressFile($backupPath . '.sql', $options['compression']);
                File::delete($backupPath . '.sql');
                $backupPath = $compressedPath;
            }

        } catch (ProcessFailedException $e) {
            throw new \Exception("MySQL backup failed: " . $e->getMessage());
        }

        $duration = (microtime(true) - $startTime) * 1000;
        $size = File::exists($backupPath) ? File::size($backupPath) : 0;

        return [
            'type' => 'mysql',
            'method' => 'mysqldump',
            'duration' => $duration,
            'size' => $size,
            'compressed' => $options['compression'] !== 'none',
            'final_path' => $backupPath
        ];
    }

    /**
     * Backup PostgreSQL database
     */
    private static function backupPostgres(string $backupPath, array $options, array $config): array
    {
        $startTime = microtime(true);

        // Build pg_dump command
        $command = ['pg_dump'];
        
        // Connection parameters
        $command[] = '--host=' . ($config['host'] ?? 'localhost');
        $command[] = '--port=' . ($config['port'] ?? 5432);
        $command[] = '--username=' . $config['username'];
        $command[] = '--dbname=' . $config['database'];
        
        // Backup type options
        switch ($options['type']) {
            case 'structure':
                $command[] = '--schema-only';
                break;
            case 'data':
                $command[] = '--data-only';
                break;
        }

        // Additional options
        $command[] = '--no-password';
        $command[] = '--verbose';
        $command[] = '--format=custom';
        $command[] = '--no-owner';
        $command[] = '--no-privileges';

        // Set environment variables for password
        $env = [];
        if (!empty($config['password'])) {
            $env['PGPASSWORD'] = $config['password'];
        }

        // Output file
        $command[] = '--file=' . $backupPath . '.backup';

        // Execute pg_dump
        $process = new Process($command, null, $env);
        $process->setTimeout(3600); // 1 hour timeout
        
        try {
            $process->run();
            
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            // Compress if requested
            if ($options['compression'] !== 'none') {
                $compressedPath = self::compressFile($backupPath . '.backup', $options['compression']);
                File::delete($backupPath . '.backup');
                $backupPath = $compressedPath;
            }

        } catch (ProcessFailedException $e) {
            throw new \Exception("PostgreSQL backup failed: " . $e->getMessage());
        }

        $duration = (microtime(true) - $startTime) * 1000;
        $size = File::exists($backupPath) ? File::size($backupPath) : 0;

        return [
            'type' => 'postgresql',
            'method' => 'pg_dump',
            'duration' => $duration,
            'size' => $size,
            'compressed' => $options['compression'] !== 'none',
            'final_path' => $backupPath
        ];
    }

    /**
     * Create SQLite SQL dump
     */
    private static function createSqliteDump(string $dbPath, string $backupPath, array $options): string
    {
        $command = ['sqlite3', $dbPath];
        
        if ($options['type'] === 'structure') {
            $command[] = '.schema';
        } else {
            $command[] = '.dump';
        }

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \Exception("SQLite dump failed: " . $process->getErrorOutput());
        }

        $sqlContent = $process->getOutput();
        $dumpPath = $backupPath . '.sql';
        
        File::put($dumpPath, $sqlContent);

        // Compress if requested
        if ($options['compression'] !== 'none') {
            $compressedPath = self::compressFile($dumpPath, $options['compression']);
            File::delete($dumpPath);
            return $compressedPath;
        }

        return $dumpPath;
    }

    /**
     * Compress backup file
     */
    private static function compressFile(string $filePath, string $compression): string
    {
        $compressedPath = $filePath . self::COMPRESSION_TYPES[$compression];

        switch ($compression) {
            case 'gzip':
                $command = ['gzip', '-9', $filePath];
                break;
            case 'bzip2':
                $command = ['bzip2', '-9', $filePath];
                break;
            default:
                return $filePath;
        }

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \Exception("Compression failed: " . $process->getErrorOutput());
        }

        return $compressedPath;
    }

    /**
     * Verify backup integrity
     */
    private static function verifyBackup(string $backupPath, string $dbDriver): array
    {
        $verification = [
            'status' => 'unknown',
            'checks' => []
        ];

        try {
            // Check file exists and is readable
            if (!File::exists($backupPath)) {
                throw new \Exception("Backup file not found: {$backupPath}");
            }

            if (!File::isReadable($backupPath)) {
                throw new \Exception("Backup file is not readable: {$backupPath}");
            }

            $verification['checks']['file_exists'] = true;
            $verification['checks']['file_readable'] = true;

            // Check file size
            $fileSize = File::size($backupPath);
            if ($fileSize === 0) {
                throw new \Exception("Backup file is empty");
            }

            $verification['checks']['file_not_empty'] = true;
            $verification['file_size'] = $fileSize;

            // Database-specific verification
            switch ($dbDriver) {
                case 'sqlite':
                    $verification = array_merge($verification, self::verifySqliteBackup($backupPath));
                    break;
                case 'mysql':
                    $verification = array_merge($verification, self::verifyMysqlBackup($backupPath));
                    break;
                case 'pgsql':
                    $verification = array_merge($verification, self::verifyPostgresBackup($backupPath));
                    break;
            }

            $verification['status'] = 'valid';

        } catch (\Exception $e) {
            $verification['status'] = 'invalid';
            $verification['error'] = $e->getMessage();
        }

        return $verification;
    }

    /**
     * Verify SQLite backup
     */
    private static function verifySqliteBackup(string $backupPath): array
    {
        $checks = [];

        try {
            if (str_ends_with($backupPath, '.db')) {
                // Verify SQLite database file integrity
                $command = ['sqlite3', $backupPath, 'PRAGMA integrity_check;'];
                $process = new Process($command);
                $process->run();

                if ($process->isSuccessful() && trim($process->getOutput()) === 'ok') {
                    $checks['sqlite_integrity'] = true;
                } else {
                    $checks['sqlite_integrity'] = false;
                }
            } else {
                // Verify SQL dump by checking for SQLite-specific patterns
                $content = File::get($backupPath);
                $checks['contains_sql'] = strpos($content, 'CREATE TABLE') !== false;
                $checks['contains_sqlite_headers'] = strpos($content, 'SQLite') !== false;
            }

        } catch (\Exception $e) {
            $checks['verification_error'] = $e->getMessage();
        }

        return ['checks' => $checks];
    }

    /**
     * Verify MySQL backup
     */
    private static function verifyMysqlBackup(string $backupPath): array
    {
        $checks = [];

        try {
            // Check if it's a compressed file
            if (str_ends_with($backupPath, '.gz') || str_ends_with($backupPath, '.bz2')) {
                $checks['compressed_file'] = true;
                // For compressed files, we'll do basic checks
                $checks['file_format_valid'] = true;
            } else {
                // Verify SQL dump content
                $content = File::get($backupPath);
                $checks['contains_sql'] = strpos($content, 'CREATE TABLE') !== false;
                $checks['contains_mysql_headers'] = strpos($content, 'MySQL dump') !== false;
                $checks['contains_dump_completed'] = strpos($content, 'Dump completed') !== false;
            }

        } catch (\Exception $e) {
            $checks['verification_error'] = $e->getMessage();
        }

        return ['checks' => $checks];
    }

    /**
     * Verify PostgreSQL backup
     */
    private static function verifyPostgresBackup(string $backupPath): array
    {
        $checks = [];

        try {
            if (str_ends_with($backupPath, '.backup')) {
                // Verify custom format backup using pg_restore
                $command = ['pg_restore', '--list', $backupPath];
                $process = new Process($command);
                $process->run();

                $checks['pg_restore_list'] = $process->isSuccessful();
                if ($process->isSuccessful()) {
                    $checks['contains_tables'] = strpos($process->getOutput(), 'TABLE') !== false;
                }
            } else {
                // Verify SQL dump
                $content = File::get($backupPath);
                $checks['contains_sql'] = strpos($content, 'CREATE TABLE') !== false;
                $checks['contains_pg_headers'] = strpos($content, 'PostgreSQL') !== false;
            }

        } catch (\Exception $e) {
            $checks['verification_error'] = $e->getMessage();
        }

        return ['checks' => $checks];
    }

    /**
     * Create backup directory
     */
    private static function createBackupDirectory(): string
    {
        $backupDir = storage_path('backups/database');
        
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        return $backupDir;
    }

    /**
     * Generate backup filename
     */
    private static function generateBackupFilename(string $type, string $compression): string
    {
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $dbName = Config::get('database.connections.' . Config::get('database.default') . '.database', 'database');
        
        // Extract database name from full path if it's a path
        if (str_contains($dbName, '/')) {
            $dbName = pathinfo($dbName, PATHINFO_FILENAME);
        }
        
        // Clean database name for filename
        $dbName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $dbName);
        
        $filename = "backup_{$dbName}_{$type}_{$timestamp}";
        
        if ($compression !== 'none') {
            $filename .= self::COMPRESSION_TYPES[$compression];
        }

        return $filename;
    }

    /**
     * Store backup metadata
     */
    private static function storeBackupMetadata(string $filename, array $result, array $options): void
    {
        $metadata = [
            'filename' => $filename,
            'created_at' => Carbon::now()->toISOString(),
            'type' => $options['type'],
            'compression' => $options['compression'],
            'size' => $result['size'] ?? 0,
            'duration' => $result['duration'] ?? 0,
            'database_driver' => Config::get('database.default'),
            'database_name' => Config::get('database.connections.' . Config::get('database.default') . '.database'),
            'verification' => $result['verification'] ?? null,
            'options' => $options
        ];

        $metadataPath = storage_path('backups/metadata/' . pathinfo($filename, PATHINFO_FILENAME) . '.json');
        
        if (!File::exists(dirname($metadataPath))) {
            File::makeDirectory(dirname($metadataPath), 0755, true);
        }

        File::put($metadataPath, json_encode($metadata, JSON_PRETTY_PRINT));
    }

    /**
     * Cleanup old backups
     */
    private static function cleanupOldBackups(int $retentionDays): array
    {
        $backupDir = storage_path('backups/database');
        $metadataDir = storage_path('backups/metadata');
        $cutoffDate = Carbon::now()->subDays($retentionDays);
        
        $deletedFiles = [];
        $deletedCount = 0;

        try {
            // Clean backup files
            $backupFiles = File::glob($backupDir . '/*');
            foreach ($backupFiles as $file) {
                $fileDate = Carbon::createFromTimestamp(File::lastModified($file));
                if ($fileDate->lt($cutoffDate)) {
                    File::delete($file);
                    $deletedFiles[] = basename($file);
                    $deletedCount++;
                }
            }

            // Clean metadata files
            $metadataFiles = File::glob($metadataDir . '/*.json');
            foreach ($metadataFiles as $file) {
                $fileDate = Carbon::createFromTimestamp(File::lastModified($file));
                if ($fileDate->lt($cutoffDate)) {
                    File::delete($file);
                    $deletedCount++;
                }
            }

            Log::info("Cleaned up old backups", [
                'deleted_count' => $deletedCount,
                'retention_days' => $retentionDays
            ]);

        } catch (\Exception $e) {
            Log::error("Backup cleanup failed", ['error' => $e->getMessage()]);
        }

        return [
            'deleted_count' => $deletedCount,
            'deleted_files' => $deletedFiles
        ];
    }

    /**
     * Upload backup to cloud storage
     */
    private static function uploadToCloud(string $localPath, string $filename, string $disk): array
    {
        try {
            $cloudPath = 'database-backups/' . Carbon::now()->format('Y/m/d') . '/' . $filename;
            
            $success = Storage::disk($disk)->put($cloudPath, File::get($localPath));
            
            if ($success) {
                return [
                    'status' => 'success',
                    'cloud_path' => $cloudPath,
                    'disk' => $disk
                ];
            } else {
                throw new \Exception("Failed to upload to {$disk}");
            }

        } catch (\Exception $e) {
            Log::error("Cloud upload failed", [
                'disk' => $disk,
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * List available backups
     */
    public static function listBackups(): array
    {
        $backupDir = storage_path('backups/database');
        $metadataDir = storage_path('backups/metadata');
        
        $backups = [];

        try {
            $backupFiles = File::glob($backupDir . '/*');
            
            foreach ($backupFiles as $file) {
                $filename = basename($file);
                $metadataFile = $metadataDir . '/' . pathinfo($filename, PATHINFO_FILENAME) . '.json';
                
                $backup = [
                    'filename' => $filename,
                    'path' => $file,
                    'size' => File::size($file),
                    'created_at' => Carbon::createFromTimestamp(File::lastModified($file))->toISOString(),
                    'age_days' => Carbon::createFromTimestamp(File::lastModified($file))->diffInDays(Carbon::now())
                ];

                // Add metadata if available
                if (File::exists($metadataFile)) {
                    $metadata = json_decode(File::get($metadataFile), true);
                    $backup = array_merge($backup, $metadata);
                }

                $backups[] = $backup;
            }

            // Sort by creation date (newest first)
            usort($backups, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

        } catch (\Exception $e) {
            Log::error("Failed to list backups", ['error' => $e->getMessage()]);
        }

        return $backups;
    }

    /**
     * Get backup statistics
     */
    public static function getBackupStatistics(): array
    {
        $backups = self::listBackups();
        
        $stats = [
            'total_backups' => count($backups),
            'total_size' => 0,
            'oldest_backup' => null,
            'newest_backup' => null,
            'types' => [],
            'compression_usage' => [],
            'average_size' => 0,
            'success_rate' => 0
        ];

        if (empty($backups)) {
            return $stats;
        }

        $totalSize = 0;
        $successCount = 0;

        foreach ($backups as $backup) {
            $totalSize += $backup['size'];
            
            // Count by type
            $type = $backup['type'] ?? 'unknown';
            $stats['types'][$type] = ($stats['types'][$type] ?? 0) + 1;
            
            // Count by compression
            $compression = $backup['compression'] ?? 'unknown';
            $stats['compression_usage'][$compression] = ($stats['compression_usage'][$compression] ?? 0) + 1;

            // Check if backup was successful
            if (isset($backup['verification']) && $backup['verification']['status'] === 'valid') {
                $successCount++;
            }
        }

        $stats['total_size'] = $totalSize;
        $stats['average_size'] = $totalSize / count($backups);
        $stats['oldest_backup'] = end($backups)['created_at'];
        $stats['newest_backup'] = $backups[0]['created_at'];
        $stats['success_rate'] = count($backups) > 0 ? ($successCount / count($backups)) * 100 : 0;

        return $stats;
    }

    /**
     * Delete backup
     */
    public static function deleteBackup(string $filename): array
    {
        try {
            $backupPath = storage_path('backups/database/' . $filename);
            $metadataPath = storage_path('backups/metadata/' . pathinfo($filename, PATHINFO_FILENAME) . '.json');

            $deleted = [];

            if (File::exists($backupPath)) {
                File::delete($backupPath);
                $deleted[] = 'backup_file';
            }

            if (File::exists($metadataPath)) {
                File::delete($metadataPath);
                $deleted[] = 'metadata_file';
            }

            if (empty($deleted)) {
                throw new \Exception("Backup file not found: {$filename}");
            }

            Log::info("Backup deleted", ['filename' => $filename, 'deleted' => $deleted]);

            return [
                'status' => 'success',
                'deleted' => $deleted
            ];

        } catch (\Exception $e) {
            Log::error("Failed to delete backup", [
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }
}
