<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Backup Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the backup settings for your application.
    | These settings control how backups are created, stored, and managed.
    |
    */

    'enabled' => env('BACKUP_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Default Backup Settings
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'type' => env('BACKUP_DEFAULT_TYPE', 'full'),
        'compression' => env('BACKUP_DEFAULT_COMPRESSION', 'gzip'),
        'verify' => env('BACKUP_VERIFY_ENABLED', true),
        'cleanup_old' => env('BACKUP_CLEANUP_ENABLED', true),
        'retention_days' => env('BACKUP_RETENTION_DAYS', 30),
        'storage_disk' => env('BACKUP_STORAGE_DISK', 'local'),
        'include_logs' => env('BACKUP_INCLUDE_LOGS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup Schedule
    |--------------------------------------------------------------------------
    |
    | Configure when automated backups should run
    |
    */
    'schedule' => [
        'daily' => [
            'enabled' => env('BACKUP_DAILY_ENABLED', true),
            'time' => env('BACKUP_DAILY_TIME', '03:00'),
            'type' => 'full',
            'compression' => 'gzip',
            'retention_days' => 30,
        ],
        'weekly' => [
            'enabled' => env('BACKUP_WEEKLY_ENABLED', true),
            'day' => 'sunday',
            'time' => env('BACKUP_WEEKLY_TIME', '04:00'),
            'type' => 'structure',
            'compression' => 'gzip',
            'retention_days' => 90,
        ],
        'hourly' => [
            'enabled' => env('BACKUP_HOURLY_ENABLED', false),
            'business_hours_only' => true,
            'start_time' => '09:00',
            'end_time' => '18:00',
            'weekdays_only' => true,
            'type' => 'incremental',
            'compression' => 'gzip',
            'retention_days' => 7,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Verification Settings
    |--------------------------------------------------------------------------
    */
    'verification' => [
        'enabled' => env('BACKUP_VERIFICATION_ENABLED', true),
        'daily_check' => env('BACKUP_VERIFICATION_DAILY_CHECK', true),
        'check_time' => env('BACKUP_VERIFICATION_TIME', '06:00'),
        'alert_on_failure' => env('BACKUP_VERIFICATION_ALERT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Settings
    |--------------------------------------------------------------------------
    */
    'storage' => [
        'local' => [
            'path' => storage_path('backups/database'),
            'metadata_path' => storage_path('backups/metadata'),
        ],
        'cloud' => [
            'enabled' => env('BACKUP_CLOUD_ENABLED', false),
            'disk' => env('BACKUP_CLOUD_DISK', 's3'),
            'path' => env('BACKUP_CLOUD_PATH', 'database-backups'),
            'keep_local_copy' => env('BACKUP_KEEP_LOCAL_COPY', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'enabled' => env('BACKUP_NOTIFICATIONS_ENABLED', false),
        'channels' => [
            'mail' => env('BACKUP_NOTIFY_MAIL', false),
            'slack' => env('BACKUP_NOTIFY_SLACK', false),
            'webhook' => env('BACKUP_NOTIFY_WEBHOOK', false),
        ],
        'events' => [
            'backup_successful' => env('BACKUP_NOTIFY_SUCCESS', false),
            'backup_failed' => env('BACKUP_NOTIFY_FAILURE', true),
            'verification_failed' => env('BACKUP_NOTIFY_VERIFICATION_FAILURE', true),
            'old_backup_deleted' => env('BACKUP_NOTIFY_CLEANUP', false),
        ],
        'slack_webhook_url' => env('BACKUP_SLACK_WEBHOOK_URL'),
        'webhook_url' => env('BACKUP_WEBHOOK_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compression Settings
    |--------------------------------------------------------------------------
    */
    'compression' => [
        'gzip' => [
            'extension' => '.gz',
            'command' => 'gzip',
            'level' => 9, // Maximum compression
        ],
        'bzip2' => [
            'extension' => '.bz2',
            'command' => 'bzip2',
            'level' => 9, // Maximum compression
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Specific Settings
    |--------------------------------------------------------------------------
    */
    'database' => [
        'sqlite' => [
            'integrity_check' => true,
            'vacuum_before_backup' => env('BACKUP_SQLITE_VACUUM', false),
        ],
        'mysql' => [
            'single_transaction' => true,
            'routines' => true,
            'triggers' => true,
            'events' => true,
            'quick' => true,
            'lock_tables' => false,
            'incremental_supported' => true,
        ],
        'pgsql' => [
            'format' => 'custom',
            'no_owner' => true,
            'no_privileges' => true,
            'verbose' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */
    'security' => [
        'encrypt_backups' => env('BACKUP_ENCRYPT', false),
        'encryption_key' => env('BACKUP_ENCRYPTION_KEY'),
        'max_backup_size' => env('BACKUP_MAX_SIZE_MB', 1024), // MB
        'timeout' => env('BACKUP_TIMEOUT_SECONDS', 3600), // 1 hour
    ],

    /*
    |--------------------------------------------------------------------------
    | Health Check Settings
    |--------------------------------------------------------------------------
    */
    'health' => [
        'max_age_hours' => env('BACKUP_MAX_AGE_HOURS', 24),
        'min_backup_count' => env('BACKUP_MIN_COUNT', 3),
        'critical_error_threshold' => env('BACKUP_CRITICAL_ERROR_THRESHOLD', 10), // %
        'warning_error_threshold' => env('BACKUP_WARNING_ERROR_THRESHOLD', 5), // %
        'disk_usage_warning_threshold' => env('BACKUP_DISK_WARNING_THRESHOLD', 80), // %
        'disk_usage_critical_threshold' => env('BACKUP_DISK_CRITICAL_THRESHOLD', 90), // %
    ],
];

