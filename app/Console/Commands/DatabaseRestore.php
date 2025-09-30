<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DatabaseBackupService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class DatabaseRestore extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db:restore 
                            {backup? : Backup filename to restore}
                            {--list : List available backups}
                            {--force : Force restore without confirmation}
                            {--test : Test restore to temporary database}
                            {--decompress : Automatically decompress if needed}';

    /**
     * The console command description.
     */
    protected $description = 'Restore database from backup';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("🔄 CarWise.ai Database Restore");
        $this->info("===============================");
        $this->newLine();

        // List backups if requested
        if ($this->option('list')) {
            return $this->listBackups();
        }

        $backupFile = $this->argument('backup');
        
        // If no backup specified, show available backups
        if (!$backupFile) {
            return $this->selectBackup();
        }

        return $this->restoreBackup($backupFile);
    }

    /**
     * List available backups
     */
    private function listBackups(): int
    {
        $this->info("📋 Available Backups:");
        $this->newLine();

        try {
            $backups = DatabaseBackupService::listBackups();

            if (empty($backups)) {
                $this->warn("No backups found.");
                return self::SUCCESS;
            }

            $this->table(
                ['Filename', 'Type', 'Size', 'Created', 'Age (days)', 'Verified'],
                array_map(function($backup) {
                    return [
                        $backup['filename'],
                        $backup['type'] ?? 'unknown',
                        $this->formatBytes($backup['size']),
                        \Carbon\Carbon::parse($backup['created_at'])->format('Y-m-d H:i:s'),
                        $backup['age_days'],
                        isset($backup['verification']) && $backup['verification']['status'] === 'valid' ? '✅' : '❌'
                    ];
                }, $backups)
            );

            $stats = DatabaseBackupService::getBackupStatistics();
            $this->newLine();
            $this->info("📊 Statistics:");
            $this->line("  Total backups: {$stats['total_backups']}");
            $this->line("  Total size: " . $this->formatBytes($stats['total_size']));
            $this->line("  Success rate: " . round($stats['success_rate'], 1) . "%");

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Failed to list backups: " . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Select backup interactively
     */
    private function selectBackup(): int
    {
        try {
            $backups = DatabaseBackupService::listBackups();

            if (empty($backups)) {
                $this->error("No backups available for restore.");
                return self::FAILURE;
            }

            $this->info("Available backups:");
            
            $choices = [];
            foreach ($backups as $index => $backup) {
                $status = isset($backup['verification']) && $backup['verification']['status'] === 'valid' ? '✅' : '❌';
                $size = $this->formatBytes($backup['size']);
                $age = $backup['age_days'];
                
                $choice = "{$backup['filename']} ({$backup['type']}, {$size}, {$age} days old) {$status}";
                $choices[] = $choice;
                
                $this->line("  " . ($index + 1) . ". {$choice}");
            }

            $selection = $this->ask("Select backup number (1-" . count($backups) . ") or 'q' to quit");

            if ($selection === 'q' || $selection === 'quit') {
                $this->info("Restore cancelled.");
                return self::SUCCESS;
            }

            $selectedIndex = (int) $selection - 1;
            
            if ($selectedIndex < 0 || $selectedIndex >= count($backups)) {
                $this->error("Invalid selection.");
                return self::FAILURE;
            }

            $selectedBackup = $backups[$selectedIndex]['filename'];
            return $this->restoreBackup($selectedBackup);

        } catch (\Exception $e) {
            $this->error("Failed to select backup: " . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Restore specific backup
     */
    private function restoreBackup(string $backupFile): int
    {
        try {
            $backupPath = storage_path('backups/database/' . $backupFile);
            
            if (!File::exists($backupPath)) {
                $this->error("Backup file not found: {$backupFile}");
                return self::FAILURE;
            }

            // Get backup info
            $backupInfo = $this->getBackupInfo($backupFile);
            $this->displayBackupInfo($backupInfo);

            // Safety confirmation
            if (!$this->option('force') && !$this->option('test')) {
                $warning = "⚠️  WARNING: This will REPLACE your current database!";
                $this->warn($warning);
                
                if (!$this->confirm("Are you sure you want to continue?")) {
                    $this->info("Restore cancelled.");
                    return self::SUCCESS;
                }
            }

            // Perform restore
            $this->info("🚀 Starting restore process...");
            $startTime = microtime(true);

            if ($this->option('test')) {
                $result = $this->testRestore($backupPath, $backupInfo);
            } else {
                $result = $this->performRestore($backupPath, $backupInfo);
            }

            $duration = (microtime(true) - $startTime) * 1000;

            if ($result['status'] === 'success') {
                $this->displayRestoreSuccess($result, $duration);
                return self::SUCCESS;
            } else {
                $this->displayRestoreError($result);
                return self::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error("❌ Restore failed: " . $e->getMessage());
            Log::error('Database restore failed', [
                'backup_file' => $backupFile,
                'error' => $e->getMessage()
            ]);
            return self::FAILURE;
        }
    }

    /**
     * Get backup information
     */
    private function getBackupInfo(string $backupFile): array
    {
        $backups = DatabaseBackupService::listBackups();
        
        foreach ($backups as $backup) {
            if ($backup['filename'] === $backupFile) {
                return $backup;
            }
        }

        // If no metadata found, create basic info
        $backupPath = storage_path('backups/database/' . $backupFile);
        return [
            'filename' => $backupFile,
            'size' => File::size($backupPath),
            'created_at' => \Carbon\Carbon::createFromTimestamp(File::lastModified($backupPath))->toISOString(),
            'type' => 'unknown'
        ];
    }

    /**
     * Display backup information
     */
    private function displayBackupInfo(array $info): void
    {
        $this->info("📋 Backup Information:");
        $this->line("  Filename: {$info['filename']}");
        $this->line("  Type: " . ($info['type'] ?? 'unknown'));
        $this->line("  Size: " . $this->formatBytes($info['size']));
        $this->line("  Created: " . \Carbon\Carbon::parse($info['created_at'])->format('Y-m-d H:i:s'));
        
        if (isset($info['database_name'])) {
            $this->line("  Database: {$info['database_name']}");
        }
        
        if (isset($info['verification'])) {
            $verified = $info['verification']['status'] === 'valid' ? 'Yes' : 'No';
            $this->line("  Verified: {$verified}");
        }
        
        $this->newLine();
    }

    /**
     * Test restore to temporary database
     */
    private function testRestore(string $backupPath, array $backupInfo): array
    {
        $this->info("🧪 Performing test restore...");

        try {
            $connection = Config::get('database.default');
            $config = Config::get("database.connections.{$connection}");
            
            // Create temporary database name
            $testDbName = $config['database'] . '_test_restore_' . time();
            
            switch ($config['driver']) {
                case 'sqlite':
                    return $this->testRestoreSqlite($backupPath, $testDbName);
                case 'mysql':
                    return $this->testRestoreMysql($backupPath, $config, $testDbName);
                case 'pgsql':
                    return $this->testRestorePostgres($backupPath, $config, $testDbName);
                default:
                    throw new \Exception("Unsupported database driver: {$config['driver']}");
            }

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Perform actual restore
     */
    private function performRestore(string $backupPath, array $backupInfo): array
    {
        try {
            $connection = Config::get('database.default');
            $config = Config::get("database.connections.{$connection}");

            // Create backup of current database before restore
            $this->info("📦 Creating safety backup of current database...");
            $safetyBackup = DatabaseBackupService::createBackup([
                'type' => 'full',
                'compression' => 'gzip',
                'verify' => false,
                'cleanup_old' => false
            ]);

            if ($safetyBackup['status'] !== 'success') {
                $this->warn("Failed to create safety backup, continuing anyway...");
            } else {
                $this->info("✅ Safety backup created: " . $safetyBackup['filename']);
            }

            // Perform restore based on database type
            switch ($config['driver']) {
                case 'sqlite':
                    return $this->restoreSqlite($backupPath, $config);
                case 'mysql':
                    return $this->restoreMysql($backupPath, $config);
                case 'pgsql':
                    return $this->restorePostgres($backupPath, $config);
                default:
                    throw new \Exception("Unsupported database driver: {$config['driver']}");
            }

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Restore SQLite database
     */
    private function restoreSqlite(string $backupPath, array $config): array
    {
        $dbPath = $config['database'];
        
        // Backup current database
        $currentBackup = $dbPath . '.restore_backup_' . time();
        if (File::exists($dbPath)) {
            File::copy($dbPath, $currentBackup);
        }

        try {
            if (str_ends_with($backupPath, '.db')) {
                // Direct file copy
                File::copy($backupPath, $dbPath);
            } else {
                // SQL dump restore
                $this->restoreSqliteDump($backupPath, $dbPath);
            }

            // Test the restored database
            $testQuery = DB::connection()->select('SELECT COUNT(*) as count FROM sqlite_master WHERE type="table"');
            $tableCount = $testQuery[0]->count ?? 0;

            return [
                'status' => 'success',
                'method' => str_ends_with($backupPath, '.db') ? 'file_copy' : 'sql_restore',
                'tables_restored' => $tableCount
            ];

        } catch (\Exception $e) {
            // Restore from backup on failure
            if (File::exists($currentBackup)) {
                File::copy($currentBackup, $dbPath);
                File::delete($currentBackup);
            }
            throw $e;
        }
    }

    /**
     * Restore MySQL database
     */
    private function restoreMysql(string $backupPath, array $config): array
    {
        // Prepare mysql command
        $command = ['mysql'];
        $command[] = '--host=' . ($config['host'] ?? 'localhost');
        $command[] = '--port=' . ($config['port'] ?? 3306);
        $command[] = '--user=' . $config['username'];
        
        if (!empty($config['password'])) {
            $command[] = '--password=' . $config['password'];
        }
        
        $command[] = $config['database'];

        // Handle compressed files
        if (str_ends_with($backupPath, '.gz')) {
            $process = new Process(['gunzip', '-c', $backupPath]);
            $process->run();
            $sqlContent = $process->getOutput();
        } elseif (str_ends_with($backupPath, '.bz2')) {
            $process = new Process(['bunzip2', '-c', $backupPath]);
            $process->run();
            $sqlContent = $process->getOutput();
        } else {
            $sqlContent = File::get($backupPath);
        }

        // Execute restore
        $process = new Process($command, null, null, $sqlContent);
        $process->setTimeout(3600);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \Exception("MySQL restore failed: " . $process->getErrorOutput());
        }

        return [
            'status' => 'success',
            'method' => 'mysql_import'
        ];
    }

    /**
     * Restore PostgreSQL database
     */
    private function restorePostgres(string $backupPath, array $config): array
    {
        $command = ['pg_restore'];
        $command[] = '--host=' . ($config['host'] ?? 'localhost');
        $command[] = '--port=' . ($config['port'] ?? 5432);
        $command[] = '--username=' . $config['username'];
        $command[] = '--dbname=' . $config['database'];
        $command[] = '--clean';
        $command[] = '--if-exists';
        $command[] = '--no-owner';
        $command[] = '--no-privileges';
        $command[] = '--verbose';
        $command[] = $backupPath;

        $env = [];
        if (!empty($config['password'])) {
            $env['PGPASSWORD'] = $config['password'];
        }

        $process = new Process($command, null, $env);
        $process->setTimeout(3600);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \Exception("PostgreSQL restore failed: " . $process->getErrorOutput());
        }

        return [
            'status' => 'success',
            'method' => 'pg_restore'
        ];
    }

    /**
     * Display restore success
     */
    private function displayRestoreSuccess(array $result, float $duration): void
    {
        $this->info("✅ Database restore completed successfully!");
        $this->newLine();

        $this->info("📊 Restore Details:");
        $this->line("  Method: " . ($result['method'] ?? 'unknown'));
        $this->line("  Duration: " . round($duration, 2) . "ms");
        
        if (isset($result['tables_restored'])) {
            $this->line("  Tables restored: {$result['tables_restored']}");
        }

        if ($this->option('test')) {
            $this->warn("⚠️  This was a test restore. Your production database was not affected.");
        } else {
            $this->warn("⚠️  Database has been restored. Please verify your application is working correctly.");
        }
    }

    /**
     * Display restore error
     */
    private function displayRestoreError(array $result): void
    {
        $this->error("❌ Database restore failed!");
        
        if (isset($result['message'])) {
            $this->line("Error: {$result['message']}");
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}

