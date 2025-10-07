<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DatabaseBackupService;
use Illuminate\Support\Facades\Log;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db:backup 
                            {--type=full : Backup type (full, structure, data, incremental)}
                            {--compression=gzip : Compression type (gzip, bzip2, none)}
                            {--no-verify : Skip backup verification}
                            {--no-cleanup : Skip cleanup of old backups}
                            {--retention=30 : Days to keep backups}
                            {--storage=local : Storage disk (local, s3, etc.)}
                            {--include-logs : Include application logs in backup}';

    /**
     * The console command description.
     */
    protected $description = 'Create a database backup with various options';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("🗄️  CarWise.ai Database Backup");
        $this->info("===============================");
        $this->newLine();

        $options = [
            'type' => $this->option('type'),
            'compression' => $this->option('compression'),
            'verify' => !$this->option('no-verify'),
            'cleanup_old' => !$this->option('no-cleanup'),
            'retention_days' => (int) $this->option('retention'),
            'storage_disk' => $this->option('storage'),
            'include_logs' => $this->option('include-logs')
        ];

        // Validate options
        if (!$this->validateOptions($options)) {
            return self::FAILURE;
        }

        $this->displayBackupInfo($options);

        try {
            $this->info("🚀 Starting backup process...");
            $startTime = microtime(true);

            $result = DatabaseBackupService::createBackup($options);

            $duration = (microtime(true) - $startTime) * 1000;

            if ($result['status'] === 'success') {
                $this->displaySuccessResult($result, $duration);
                return self::SUCCESS;
            } else {
                $this->displayErrorResult($result);
                return self::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error("❌ Backup failed: " . $e->getMessage());
            Log::error('Database backup command failed', [
                'error' => $e->getMessage(),
                'options' => $options
            ]);
            return self::FAILURE;
        }
    }

    /**
     * Validate backup options
     */
    private function validateOptions(array $options): bool
    {
        $validTypes = ['full', 'structure', 'data', 'incremental'];
        if (!in_array($options['type'], $validTypes)) {
            $this->error("Invalid backup type: {$options['type']}");
            $this->line("Valid types: " . implode(', ', $validTypes));
            return false;
        }

        $validCompressions = ['gzip', 'bzip2', 'none'];
        if (!in_array($options['compression'], $validCompressions)) {
            $this->error("Invalid compression type: {$options['compression']}");
            $this->line("Valid compressions: " . implode(', ', $validCompressions));
            return false;
        }

        if ($options['retention_days'] < 1) {
            $this->error("Retention days must be at least 1");
            return false;
        }

        return true;
    }

    /**
     * Display backup information
     */
    private function displayBackupInfo(array $options): void
    {
        $this->info("📋 Backup Configuration:");
        $this->line("  Type: {$options['type']}");
        $this->line("  Compression: {$options['compression']}");
        $this->line("  Verification: " . ($options['verify'] ? 'enabled' : 'disabled'));
        $this->line("  Cleanup: " . ($options['cleanup_old'] ? 'enabled' : 'disabled'));
        $this->line("  Retention: {$options['retention_days']} days");
        $this->line("  Storage: {$options['storage_disk']}");
        $this->line("  Include logs: " . ($options['include_logs'] ? 'yes' : 'no'));
        $this->newLine();
    }

    /**
     * Display success result
     */
    private function displaySuccessResult(array $result, float $duration): void
    {
        $this->info("✅ Backup completed successfully!");
        $this->newLine();

        $this->info("📊 Backup Details:");
        $this->line("  Filename: {$result['filename']}");
        $this->line("  Path: {$result['path']}");
        
        if (isset($result['size'])) {
            $this->line("  Size: " . $this->formatBytes($result['size']));
        }
        
        $this->line("  Duration: " . round($duration, 2) . "ms");
        
        if (isset($result['type'])) {
            $this->line("  Database: " . strtoupper($result['type']));
        }

        if (isset($result['compressed']) && $result['compressed']) {
            $this->line("  Compressed: Yes");
        }

        // Display verification results
        if (isset($result['verification'])) {
            $this->newLine();
            $this->displayVerificationResult($result['verification']);
        }

        // Display cloud upload results
        if (isset($result['cloud_upload'])) {
            $this->newLine();
            $this->displayCloudUploadResult($result['cloud_upload']);
        }
    }

    /**
     * Display error result
     */
    private function displayErrorResult(array $result): void
    {
        $this->error("❌ Backup failed!");
        
        if (isset($result['message'])) {
            $this->line("Error: {$result['message']}");
        }

        if (isset($result['error']) && $result['error'] instanceof \Exception) {
            $this->line("Details: " . $result['error']->getMessage());
        }
    }

    /**
     * Display verification result
     */
    private function displayVerificationResult(array $verification): void
    {
        $this->info("🔍 Verification Results:");
        
        if ($verification['status'] === 'valid') {
            $this->line("  Status: <fg=green>VALID</>");
        } else {
            $this->line("  Status: <fg=red>INVALID</>");
            if (isset($verification['error'])) {
                $this->line("  Error: {$verification['error']}");
            }
        }

        if (isset($verification['checks'])) {
            $this->line("  Checks performed:");
            foreach ($verification['checks'] as $check => $result) {
                $status = $result ? '<fg=green>PASS</>' : '<fg=red>FAIL</>';
                $this->line("    • " . str_replace('_', ' ', ucfirst($check)) . ": {$status}");
            }
        }

        if (isset($verification['file_size'])) {
            $this->line("  File size: " . $this->formatBytes($verification['file_size']));
        }
    }

    /**
     * Display cloud upload result
     */
    private function displayCloudUploadResult(array $upload): void
    {
        $this->info("☁️  Cloud Upload:");
        
        if ($upload['status'] === 'success') {
            $this->line("  Status: <fg=green>SUCCESS</>");
            $this->line("  Location: {$upload['cloud_path']}");
            $this->line("  Storage: {$upload['disk']}");
        } else {
            $this->line("  Status: <fg=red>FAILED</>");
            $this->line("  Error: {$upload['error']}");
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



