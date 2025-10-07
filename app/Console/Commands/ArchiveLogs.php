<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LogMonitoringService;
use Illuminate\Support\Facades\Log;

class ArchiveLogs extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'logs:archive 
                            {--days=30 : Number of days to keep logs}
                            {--force : Force archiving without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Archive old log files to save disk space';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $daysToKeep = (int) $this->option('days');
        $force = $this->option('force');

        $this->info("📦 Log Archiving Tool");
        $this->info("===================");
        $this->line("Days to keep: {$daysToKeep}");
        $this->newLine();

        if (!$force) {
            if (!$this->confirm("This will archive logs older than {$daysToKeep} days. Continue?")) {
                $this->info("Operation cancelled.");
                return self::SUCCESS;
            }
        }

        try {
            $this->info("🔍 Scanning for old log files...");
            
            $result = LogMonitoringService::archiveLogs($daysToKeep);
            
            if ($result['status'] === 'error') {
                $this->error("❌ Archive failed: " . $result['message']);
                return self::FAILURE;
            }

            $archivedCount = count($result['archived_files']);
            
            if ($archivedCount === 0) {
                $this->info("✅ No old log files found to archive.");
            } else {
                $this->info("✅ Successfully archived {$archivedCount} log files:");
                
                foreach ($result['archived_files'] as $file) {
                    $this->line("  📁 {$file}");
                }
                
                $this->newLine();
                $this->info("Archive location: " . $result['archive_path']);
            }

            // Log the archiving operation
            Log::info('Log archiving completed', [
                'days_kept' => $daysToKeep,
                'archived_files' => $archivedCount,
                'operator' => 'console_command',
                'timestamp' => now()->toISOString()
            ]);

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Archive operation failed: " . $e->getMessage());
            
            Log::error('Log archiving failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return self::FAILURE;
        }
    }
}



