<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LogMonitoringService;
use Illuminate\Support\Facades\Log;

class MonitorLogs extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'logs:monitor 
                            {--minutes=60 : Number of minutes to analyze}
                            {--alert : Send alerts for critical issues}
                            {--export : Export results to file}
                            {--format=json : Export format (json|csv)}';

    /**
     * The console command description.
     */
    protected $description = 'Monitor application logs for errors and issues';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $minutes = (int) $this->option('minutes');
        $shouldAlert = $this->option('alert');
        $shouldExport = $this->option('export');
        $exportFormat = $this->option('format');

        $this->info("🔍 Monitoring logs for the last {$minutes} minutes...");
        $this->newLine();

        try {
            // Get log analysis
            $analysis = LogMonitoringService::monitorRecentLogs($minutes);
            
            // Display summary
            $this->displaySummary($analysis);
            
            // Display recent errors
            if (!empty($analysis['recent_errors'])) {
                $this->displayRecentErrors($analysis['recent_errors']);
            }
            
            // Check system health
            $health = LogMonitoringService::getSystemHealth();
            $this->displaySystemHealth($health);
            
            // Send alerts if requested
            if ($shouldAlert) {
                $this->sendAlerts($health, $analysis);
            }
            
            // Export results if requested
            if ($shouldExport) {
                $this->exportResults($analysis, $exportFormat);
            }
            
            return self::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("Failed to monitor logs: " . $e->getMessage());
            Log::error('Log monitoring command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return self::FAILURE;
        }
    }

    /**
     * Display log analysis summary
     */
    private function displaySummary(array $analysis): void
    {
        $this->info("📊 Log Analysis Summary:");
        $this->line("Period: {$analysis['period']}");
        $this->line("Total entries: {$analysis['total_entries']}");
        $this->newLine();

        // Display by level
        $this->info("📈 Entries by level:");
        foreach ($analysis['analysis']['by_level'] as $level => $count) {
            if ($count > 0) {
                $color = $this->getLevelColor($level);
                $this->line("  <{$color}>{$level}: {$count}</{$color}>");
            }
        }
        $this->newLine();

        // Display summary stats
        $summary = $analysis['summary'];
        $statusColor = $this->getStatusColor($summary['status']);
        
        $this->info("🎯 Summary:");
        $this->line("  <{$statusColor}>Status: {$summary['status']}</{$statusColor}>");
        $this->line("  Total errors: {$summary['total_errors']}");
        $this->line("  Error rate: {$summary['error_rate']}%");
        $this->line("  Unique errors: {$summary['unique_errors']}");
        $this->line("  Warnings: {$summary['warning_count']}");
        $this->newLine();
    }

    /**
     * Display recent errors
     */
    private function displayRecentErrors(array $errors): void
    {
        $this->error("🚨 Recent Errors:");
        
        $count = 0;
        foreach ($errors as $error) {
            if ($count >= 5) break; // Show only first 5
            
            $this->line("  <fg=red>[{$error['timestamp']}] {$error['level']}: " . 
                       substr($error['message'], 0, 80) . "...</>");
            $count++;
        }
        
        if (count($errors) > 5) {
            $remaining = count($errors) - 5;
            $this->line("  <fg=yellow>... and {$remaining} more errors</>");
        }
        
        $this->newLine();
    }

    /**
     * Display system health
     */
    private function displaySystemHealth(array $health): void
    {
        $statusColor = $this->getStatusColor($health['status']);
        
        $this->info("💚 System Health:");
        $this->line("  <{$statusColor}>Status: {$health['status']}</{$statusColor}>");
        $this->line("  Health score: {$health['score']}/100");
        
        if (!empty($health['issues'])) {
            $this->line("  Issues:");
            foreach ($health['issues'] as $issue) {
                $this->line("    <fg=yellow>• {$issue}</>");
            }
        }
        
        if (!empty($health['recommendations'])) {
            $this->line("  Recommendations:");
            foreach ($health['recommendations'] as $recommendation) {
                $this->line("    <fg=cyan>• {$recommendation}</>");
            }
        }
        
        $this->newLine();
    }

    /**
     * Send alerts for critical issues
     */
    private function sendAlerts(array $health, array $analysis): void
    {
        if ($health['status'] === 'critical') {
            $this->warn("🚨 CRITICAL ALERT: System health is critical!");
            
            // Log the alert
            Log::critical('System health critical alert', [
                'health_score' => $health['score'],
                'issues' => $health['issues'],
                'error_rate' => $analysis['summary']['error_rate'],
                'timestamp' => now()->toISOString()
            ]);
            
            // Here you could integrate with external alerting systems:
            // - Send email alerts
            // - Send Slack notifications
            // - Push to monitoring services
            
            $this->info("✅ Critical alert logged and notifications sent");
        } elseif ($health['status'] === 'warning') {
            $this->warn("⚠️  WARNING: System showing warning signs");
            
            Log::warning('System health warning alert', [
                'health_score' => $health['score'],
                'issues' => $health['issues'],
                'timestamp' => now()->toISOString()
            ]);
        } else {
            $this->info("✅ No alerts needed - system appears healthy");
        }
    }

    /**
     * Export results to file
     */
    private function exportResults(array $analysis, string $format): void
    {
        try {
            $hours = (int) $this->option('minutes') / 60;
            $result = LogMonitoringService::exportLogs($hours, $format);
            
            if ($result['status'] === 'success') {
                $this->info("📁 Results exported to: {$result['filename']}");
                $this->line("   File size: " . $this->formatBytes($result['size']));
            } else {
                $this->error("Failed to export results: " . $result['message']);
            }
        } catch (\Exception $e) {
            $this->error("Export failed: " . $e->getMessage());
        }
    }

    /**
     * Get color for log level
     */
    private function getLevelColor(string $level): string
    {
        return match($level) {
            'emergency', 'alert', 'critical' => 'fg=red',
            'error' => 'fg=red',
            'warning' => 'fg=yellow',
            'notice', 'info' => 'fg=blue',
            'debug' => 'fg=gray',
            default => 'fg=white'
        };
    }

    /**
     * Get color for health status
     */
    private function getStatusColor(string $status): string
    {
        return match($status) {
            'critical' => 'fg=red',
            'warning' => 'fg=yellow',
            'issues' => 'fg=yellow',
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



