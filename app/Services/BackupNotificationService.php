<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class BackupNotificationService
{
    /**
     * Send backup notification
     */
    public static function sendNotification(string $event, array $data): void
    {
        if (!config('backup.notifications.enabled', false)) {
            return;
        }

        $eventConfig = config("backup.notifications.events.{$event}", false);
        if (!$eventConfig) {
            return;
        }

        try {
            $channels = config('backup.notifications.channels', []);

            if ($channels['mail'] ?? false) {
                self::sendEmailNotification($event, $data);
            }

            if ($channels['slack'] ?? false) {
                self::sendSlackNotification($event, $data);
            }

            if ($channels['webhook'] ?? false) {
                self::sendWebhookNotification($event, $data);
            }

        } catch (\Exception $e) {
            Log::error('Backup notification failed', [
                'event' => $event,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send email notification
     */
    private static function sendEmailNotification(string $event, array $data): void
    {
        $adminEmail = config('mail.admin_email', config('mail.from.address'));
        if (!$adminEmail) {
            return;
        }

        $subject = self::getEmailSubject($event);
        $body = self::getEmailBody($event, $data);

        try {
            Mail::raw($body, function ($message) use ($adminEmail, $subject) {
                $message->to($adminEmail)
                        ->subject($subject);
            });

            Log::info('Backup email notification sent', [
                'event' => $event,
                'recipient' => $adminEmail
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send backup email notification', [
                'event' => $event,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send Slack notification
     */
    private static function sendSlackNotification(string $event, array $data): void
    {
        $webhookUrl = config('backup.notifications.slack_webhook_url');
        if (!$webhookUrl) {
            Log::warning('Slack webhook URL not configured for backup notifications');
            return;
        }

        $message = self::getSlackMessage($event, $data);

        try {
            $response = Http::timeout(10)->post($webhookUrl, $message);

            if ($response->successful()) {
                Log::info('Backup Slack notification sent', ['event' => $event]);
            } else {
                Log::error('Slack notification failed', [
                    'event' => $event,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send Slack backup notification', [
                'event' => $event,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send webhook notification
     */
    private static function sendWebhookNotification(string $event, array $data): void
    {
        $webhookUrl = config('backup.notifications.webhook_url');
        if (!$webhookUrl) {
            Log::warning('Webhook URL not configured for backup notifications');
            return;
        }

        $payload = [
            'event' => $event,
            'timestamp' => now()->toISOString(),
            'data' => $data,
            'application' => config('app.name', 'CarWise.ai'),
            'environment' => config('app.env', 'production')
        ];

        try {
            $response = Http::timeout(10)->post($webhookUrl, $payload);

            if ($response->successful()) {
                Log::info('Backup webhook notification sent', ['event' => $event]);
            } else {
                Log::error('Webhook notification failed', [
                    'event' => $event,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send webhook backup notification', [
                'event' => $event,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get email subject for event
     */
    private static function getEmailSubject(string $event): string
    {
        $appName = config('app.name', 'CarWise.ai');
        $env = config('app.env', 'production');

        $subjects = [
            'backup_successful' => "✅ [{$appName}] Database Backup Completed Successfully",
            'backup_failed' => "❌ [{$appName}] Database Backup Failed - URGENT",
            'verification_failed' => "⚠️ [{$appName}] Backup Verification Failed",
            'old_backup_deleted' => "🗑️ [{$appName}] Old Backups Cleaned Up",
        ];

        $subject = $subjects[$event] ?? "📋 [{$appName}] Backup Notification";
        
        if ($env !== 'production') {
            $subject .= " ({$env})";
        }

        return $subject;
    }

    /**
     * Get email body for event
     */
    private static function getEmailBody(string $event, array $data): string
    {
        $appName = config('app.name', 'CarWise.ai');
        $env = config('app.env', 'production');
        $timestamp = now()->format('Y-m-d H:i:s T');

        $body = "CarWise.ai Database Backup Notification\n";
        $body .= "=========================================\n\n";
        $body .= "Application: {$appName}\n";
        $body .= "Environment: {$env}\n";
        $body .= "Timestamp: {$timestamp}\n";
        $body .= "Event: " . str_replace('_', ' ', ucwords($event, '_')) . "\n\n";

        switch ($event) {
            case 'backup_successful':
                $body .= "✅ Database backup completed successfully!\n\n";
                $body .= "Details:\n";
                $body .= "- Filename: " . ($data['filename'] ?? 'Unknown') . "\n";
                $body .= "- Type: " . ($data['type'] ?? 'Unknown') . "\n";
                $body .= "- Size: " . self::formatBytes($data['size'] ?? 0) . "\n";
                $body .= "- Duration: " . round($data['duration'] ?? 0, 2) . "ms\n";
                if (isset($data['verification'])) {
                    $body .= "- Verification: " . ($data['verification']['status'] === 'valid' ? 'PASSED' : 'FAILED') . "\n";
                }
                break;

            case 'backup_failed':
                $body .= "❌ Database backup FAILED!\n\n";
                $body .= "Error: " . ($data['error'] ?? 'Unknown error') . "\n";
                $body .= "Message: " . ($data['message'] ?? 'No additional details') . "\n\n";
                $body .= "IMMEDIATE ACTION REQUIRED:\n";
                $body .= "1. Check backup logs for details\n";
                $body .= "2. Verify database connectivity\n";
                $body .= "3. Ensure sufficient disk space\n";
                $body .= "4. Contact system administrator if needed\n";
                break;

            case 'verification_failed':
                $body .= "⚠️ Backup verification failed!\n\n";
                $body .= "Backup: " . ($data['filename'] ?? 'Unknown') . "\n";
                $body .= "Error: " . ($data['error'] ?? 'Verification checks failed') . "\n\n";
                $body .= "Recommended actions:\n";
                $body .= "1. Re-run backup verification\n";
                $body .= "2. Create a new backup if verification continues to fail\n";
                $body .= "3. Check backup file integrity\n";
                break;

            case 'old_backup_deleted':
                $body .= "🗑️ Old backups have been cleaned up\n\n";
                $body .= "Deleted backups: " . ($data['deleted_count'] ?? 0) . "\n";
                $body .= "Retention period: " . ($data['retention_days'] ?? 30) . " days\n";
                break;
        }

        $body .= "\n---\n";
        $body .= "This is an automated notification from CarWise.ai backup system.\n";
        $body .= "For support, contact: " . config('mail.from.address', 'admin@carwise.ai');

        return $body;
    }

    /**
     * Get Slack message for event
     */
    private static function getSlackMessage(string $event, array $data): array
    {
        $appName = config('app.name', 'CarWise.ai');
        $env = config('app.env', 'production');

        $color = match($event) {
            'backup_successful' => 'good',
            'backup_failed' => 'danger',
            'verification_failed' => 'warning',
            'old_backup_deleted' => '#439FE0',
            default => '#36a64f'
        };

        $emoji = match($event) {
            'backup_successful' => ':white_check_mark:',
            'backup_failed' => ':x:',
            'verification_failed' => ':warning:',
            'old_backup_deleted' => ':wastebasket:',
            default => ':information_source:'
        };

        $title = match($event) {
            'backup_successful' => 'Database Backup Successful',
            'backup_failed' => 'Database Backup Failed',
            'verification_failed' => 'Backup Verification Failed',
            'old_backup_deleted' => 'Old Backups Cleaned Up',
            default => 'Backup Notification'
        };

        $fields = [];

        switch ($event) {
            case 'backup_successful':
                $fields = [
                    [
                        'title' => 'Filename',
                        'value' => $data['filename'] ?? 'Unknown',
                        'short' => true
                    ],
                    [
                        'title' => 'Size',
                        'value' => self::formatBytes($data['size'] ?? 0),
                        'short' => true
                    ],
                    [
                        'title' => 'Type',
                        'value' => $data['type'] ?? 'Unknown',
                        'short' => true
                    ],
                    [
                        'title' => 'Duration',
                        'value' => round($data['duration'] ?? 0, 2) . 'ms',
                        'short' => true
                    ]
                ];

                if (isset($data['verification'])) {
                    $fields[] = [
                        'title' => 'Verification',
                        'value' => $data['verification']['status'] === 'valid' ? ':white_check_mark: Passed' : ':x: Failed',
                        'short' => true
                    ];
                }
                break;

            case 'backup_failed':
                $fields = [
                    [
                        'title' => 'Error',
                        'value' => $data['error'] ?? 'Unknown error',
                        'short' => false
                    ],
                    [
                        'title' => 'Message',
                        'value' => $data['message'] ?? 'No additional details',
                        'short' => false
                    ]
                ];
                break;

            case 'verification_failed':
                $fields = [
                    [
                        'title' => 'Backup File',
                        'value' => $data['filename'] ?? 'Unknown',
                        'short' => true
                    ],
                    [
                        'title' => 'Error',
                        'value' => $data['error'] ?? 'Verification checks failed',
                        'short' => false
                    ]
                ];
                break;

            case 'old_backup_deleted':
                $fields = [
                    [
                        'title' => 'Deleted Backups',
                        'value' => $data['deleted_count'] ?? 0,
                        'short' => true
                    ],
                    [
                        'title' => 'Retention Period',
                        'value' => ($data['retention_days'] ?? 30) . ' days',
                        'short' => true
                    ]
                ];
                break;
        }

        return [
            'username' => 'CarWise.ai Backup Bot',
            'icon_emoji' => ':floppy_disk:',
            'attachments' => [
                [
                    'color' => $color,
                    'title' => $emoji . ' ' . $title,
                    'fields' => $fields,
                    'footer' => $appName . ' | ' . ucfirst($env),
                    'ts' => now()->timestamp
                ]
            ]
        ];
    }

    /**
     * Format bytes to human readable format
     */
    private static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Send daily backup health report
     */
    public static function sendDailyHealthReport(): void
    {
        if (!config('backup.notifications.enabled', false)) {
            return;
        }

        try {
            $backups = DatabaseBackupService::listBackups();
            $statistics = DatabaseBackupService::getBackupStatistics();
            
            // Calculate health score using reflection to access private method
            $controller = new \App\Http\Controllers\Api\BackupController();
            $reflection = new \ReflectionClass($controller);
            $method = $reflection->getMethod('calculateBackupHealthScore');
            $method->setAccessible(true);
            $healthScore = $method->invoke($controller, $backups, $statistics);

            // Only send if there are issues or it's weekly summary (Sunday)
            $shouldSend = false;
            
            if ($healthScore['status'] !== 'excellent') {
                $shouldSend = true;
            } elseif (now()->dayOfWeek === 0) { // Sunday
                $shouldSend = true;
            }

            if ($shouldSend) {
                self::sendNotification('daily_health_report', [
                    'health_score' => $healthScore,
                    'statistics' => $statistics,
                    'recent_backups' => array_slice($backups, 0, 3)
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send daily backup health report', [
                'error' => $e->getMessage()
            ]);
        }
    }
}



