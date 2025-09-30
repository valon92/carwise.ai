<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Log monitoring and cleanup tasks
        $schedule->command('logs:monitor --alert')
                 ->everyFiveMinutes()
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/cron.log'));

        $schedule->command('logs:health-check')
                 ->hourly()
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/cron.log'));

        $schedule->command('logs:archive --days=30')
                 ->daily()
                 ->at('02:00')
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/cron.log'));

        // Performance monitoring
        $schedule->call(function () {
            \App\Services\LogMonitoringService::clearLogCache();
        })->daily()->at('01:00');

        // Clear old performance metrics cache
        $schedule->call(function () {
            $keys = \Illuminate\Support\Facades\Cache::get('log_monitoring_keys', []);
            foreach ($keys as $key) {
                if (str_contains($key, 'performance_metrics') || str_contains($key, 'error_trend')) {
                    $parts = explode(':', $key);
                    if (count($parts) >= 2) {
                        $date = end($parts);
                        if (\Carbon\Carbon::createFromFormat('Y-m-d-H', $date)->lt(now()->subDays(2))) {
                            \Illuminate\Support\Facades\Cache::forget($key);
                        }
                    }
                }
            }
        })->daily()->at('03:00');

        // System health check with alerts
        $schedule->command('logs:health-check --json')
                 ->everyThirtyMinutes()
                 ->withoutOverlapping()
                 ->when(function () {
                     return app()->environment('production');
                 });

        // Export logs for external analysis (if configured)
        if (env('LOG_EXPORT_ENABLED', false)) {
            $schedule->call(function () {
                \App\Services\LogMonitoringService::exportLogs(24, 'json');
            })->daily()->at('04:00');
        }

        // Database backup scheduling
        if (env('BACKUP_ENABLED', true)) {
            // Daily full backup at 3 AM
            $schedule->command('db:backup --type=full --compression=gzip')
                     ->daily()
                     ->at('03:00')
                     ->withoutOverlapping()
                     ->appendOutputTo(storage_path('logs/backup.log'));

            // Weekly structure backup on Sundays at 4 AM
            $schedule->command('db:backup --type=structure --compression=gzip --retention=90')
                     ->weekly()
                     ->sundays()
                     ->at('04:00')
                     ->withoutOverlapping()
                     ->appendOutputTo(storage_path('logs/backup.log'));

            // Hourly incremental backups during business hours (if supported)
            if (config('database.default') === 'mysql') {
                $schedule->command('db:backup --type=incremental --compression=gzip --retention=7')
                         ->hourly()
                         ->between('09:00', '18:00')
                         ->weekdays()
                         ->withoutOverlapping()
                         ->appendOutputTo(storage_path('logs/backup.log'));
            }

            // Cleanup old backups weekly
            $schedule->call(function () {
                \App\Services\DatabaseBackupService::cleanupOldBackups(env('BACKUP_RETENTION_DAYS', 30));
            })->weekly()->at('05:00');
        }

        // Backup verification (daily check of latest backup)
        if (env('BACKUP_VERIFICATION_ENABLED', true)) {
            $schedule->call(function () {
                $backups = \App\Services\DatabaseBackupService::listBackups();
                if (!empty($backups)) {
                    $latestBackup = $backups[0];
                    if (!isset($latestBackup['verification']) || $latestBackup['verification']['status'] !== 'valid') {
                        \Illuminate\Support\Facades\Log::warning('Latest backup verification failed or missing', [
                            'backup' => $latestBackup['filename']
                        ]);
                    }
                }
            })->daily()->at('06:00');
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
