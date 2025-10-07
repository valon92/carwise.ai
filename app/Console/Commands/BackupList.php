<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DatabaseBackupService;

class BackupList extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'backup:list 
                            {--stats : Show backup statistics}
                            {--detailed : Show detailed information}
                            {--json : Output as JSON}';

    /**
     * The console command description.
     */
    protected $description = 'List all available database backups';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $backups = DatabaseBackupService::listBackups();
            $showStats = $this->option('stats');
            $detailed = $this->option('detailed');
            $jsonOutput = $this->option('json');

            if ($jsonOutput) {
                $output = [
                    'backups' => $backups,
                    'count' => count($backups)
                ];

                if ($showStats) {
                    $output['statistics'] = DatabaseBackupService::getBackupStatistics();
                }

                $this->line(json_encode($output, JSON_PRETTY_PRINT));
                return self::SUCCESS;
            }

            $this->displayBackups($backups, $detailed);

            if ($showStats) {
                $this->displayStatistics();
            }

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Failed to list backups: " . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Display backups in table format
     */
    private function displayBackups(array $backups, bool $detailed): void
    {
        $this->info("📋 Database Backups");
        $this->info("==================");
        $this->newLine();

        if (empty($backups)) {
            $this->warn("No backups found.");
            return;
        }

        if ($detailed) {
            $this->displayDetailedBackups($backups);
        } else {
            $this->displaySimpleBackups($backups);
        }
    }

    /**
     * Display simple backup list
     */
    private function displaySimpleBackups(array $backups): void
    {
        $headers = ['#', 'Filename', 'Type', 'Size', 'Age', 'Status'];
        $rows = [];

        foreach ($backups as $index => $backup) {
            $status = '❓';
            if (isset($backup['verification'])) {
                $status = $backup['verification']['status'] === 'valid' ? '✅' : '❌';
            }

            $rows[] = [
                $index + 1,
                $backup['filename'],
                $backup['type'] ?? 'unknown',
                $this->formatBytes($backup['size']),
                $backup['age_days'] . ' days',
                $status
            ];
        }

        $this->table($headers, $rows);
    }

    /**
     * Display detailed backup information
     */
    private function displayDetailedBackups(array $backups): void
    {
        foreach ($backups as $index => $backup) {
            $this->info("Backup #" . ($index + 1));
            $this->line("  Filename: {$backup['filename']}");
            $this->line("  Type: " . ($backup['type'] ?? 'unknown'));
            $this->line("  Size: " . $this->formatBytes($backup['size']));
            $this->line("  Created: " . \Carbon\Carbon::parse($backup['created_at'])->format('Y-m-d H:i:s'));
            $this->line("  Age: {$backup['age_days']} days");

            if (isset($backup['compression'])) {
                $this->line("  Compression: {$backup['compression']}");
            }

            if (isset($backup['database_name'])) {
                $this->line("  Database: {$backup['database_name']}");
            }

            if (isset($backup['database_driver'])) {
                $this->line("  Driver: {$backup['database_driver']}");
            }

            if (isset($backup['verification'])) {
                $status = $backup['verification']['status'];
                $color = $status === 'valid' ? 'green' : 'red';
                $this->line("  Verified: <fg={$color}>" . strtoupper($status) . "</>");

                if (isset($backup['verification']['checks'])) {
                    $this->line("  Checks:");
                    foreach ($backup['verification']['checks'] as $check => $result) {
                        $checkStatus = $result ? '✅' : '❌';
                        $this->line("    • " . str_replace('_', ' ', ucfirst($check)) . ": {$checkStatus}");
                    }
                }
            }

            if (isset($backup['duration'])) {
                $this->line("  Creation time: " . round($backup['duration'], 2) . "ms");
            }

            $this->newLine();
        }
    }

    /**
     * Display backup statistics
     */
    private function displayStatistics(): void
    {
        $stats = DatabaseBackupService::getBackupStatistics();

        $this->info("📊 Backup Statistics");
        $this->info("===================");
        $this->newLine();

        $this->line("Total backups: {$stats['total_backups']}");
        $this->line("Total size: " . $this->formatBytes($stats['total_size']));
        $this->line("Average size: " . $this->formatBytes($stats['average_size']));
        $this->line("Success rate: " . round($stats['success_rate'], 1) . "%");

        if ($stats['oldest_backup']) {
            $oldestAge = \Carbon\Carbon::parse($stats['oldest_backup'])->diffInDays(\Carbon\Carbon::now());
            $this->line("Oldest backup: {$oldestAge} days ago");
        }

        if ($stats['newest_backup']) {
            $newestAge = \Carbon\Carbon::parse($stats['newest_backup'])->diffInHours(\Carbon\Carbon::now());
            $this->line("Newest backup: " . round($newestAge, 1) . " hours ago");
        }

        // Display breakdown by type
        if (!empty($stats['types'])) {
            $this->newLine();
            $this->line("Breakdown by type:");
            foreach ($stats['types'] as $type => $count) {
                $percentage = round(($count / $stats['total_backups']) * 100, 1);
                $this->line("  • {$type}: {$count} ({$percentage}%)");
            }
        }

        // Display compression usage
        if (!empty($stats['compression_usage'])) {
            $this->newLine();
            $this->line("Compression usage:");
            foreach ($stats['compression_usage'] as $compression => $count) {
                $percentage = round(($count / $stats['total_backups']) * 100, 1);
                $this->line("  • {$compression}: {$count} ({$percentage}%)");
            }
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



