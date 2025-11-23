<?php

namespace App\Services;

use Sentry\State\Scope;
use Sentry\Laravel\Facade as Sentry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SentryService
{
    /**
     * Initialize Sentry with user context
     */
    public static function initializeUserContext($user = null): void
    {
        if (!$user && Auth::check()) {
            $user = Auth::user();
        }

        if ($user) {
            Sentry::configureScope(function (Scope $scope) use ($user): void {
                $scope->setUser([
                    'id' => $user->id,
                    'email' => $user->email,
                    'username' => $user->name ?? $user->first_name,
                    'role' => $user->role ?? 'customer',
                    'created_at' => $user->created_at?->toISOString(),
                ]);

                $scope->setTag('user_type', $user->role ?? 'customer');
                $scope->setTag('platform', 'carwise-ai');
                $scope->setContext('user', [
                    'id' => $user->id,
                    'name' => $user->name ?? $user->first_name,
                    'email' => $user->email,
                    'role' => $user->role ?? 'customer',
                    'created_at' => $user->created_at?->toISOString(),
                ]);
            });
        }
    }

    /**
     * Capture exception with context
     */
    public static function captureException(\Throwable $exception, array $context = []): void
    {
        Sentry::withScope(function (Scope $scope) use ($exception, $context): void {
            // Add custom context
            foreach ($context as $key => $value) {
                $scope->setContext($key, $value);
            }

            // Add request context
            $scope->setContext('request', [
                'url' => request()->url(),
                'method' => request()->method(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'headers' => request()->headers->all(),
            ]);

            // Add session context
            if (session()->isStarted()) {
                $scope->setContext('session', [
                    'id' => session()->getId(),
                    'data' => session()->all(),
                ]);
            }

            Sentry::captureException($exception);
        });
    }

    /**
     * Capture message with context
     */
    public static function captureMessage(string $message, string $level = 'info', array $context = []): void
    {
        Sentry::withScope(function (Scope $scope) use ($message, $level, $context): void {
            // Add custom context
            foreach ($context as $key => $value) {
                $scope->setContext($key, $value);
            }

            $scope->setLevel($level);
            Sentry::captureMessage($message);
        });
    }

    /**
     * Track AI diagnosis errors
     */
    public static function trackAIDiagnosisError(\Throwable $exception, array $diagnosisData = []): void
    {
        self::captureException($exception, [
            'diagnosis' => [
                'car_brand' => $diagnosisData['brand'] ?? 'unknown',
                'car_model' => $diagnosisData['model'] ?? 'unknown',
                'car_year' => $diagnosisData['year'] ?? 'unknown',
                'symptoms' => $diagnosisData['symptoms'] ?? [],
                'ai_provider' => $diagnosisData['ai_provider'] ?? 'unknown',
                'session_id' => $diagnosisData['session_id'] ?? null,
            ],
            'error_type' => 'ai_diagnosis',
        ]);
    }

    /**
     * Track car API errors
     */
    public static function trackCarAPIError(\Throwable $exception, array $apiData = []): void
    {
        self::captureException($exception, [
            'car_api' => [
                'endpoint' => $apiData['endpoint'] ?? 'unknown',
                'method' => $apiData['method'] ?? 'GET',
                'response_code' => $apiData['response_code'] ?? null,
                'response_body' => $apiData['response_body'] ?? null,
                'request_data' => $apiData['request_data'] ?? [],
            ],
            'error_type' => 'car_api',
        ]);
    }

    /**
     * Track authentication errors
     */
    public static function trackAuthError(\Throwable $exception, array $authData = []): void
    {
        self::captureException($exception, [
            'authentication' => [
                'action' => $authData['action'] ?? 'unknown',
                'user_id' => $authData['user_id'] ?? null,
                'email' => $authData['email'] ?? null,
                'ip_address' => $authData['ip_address'] ?? request()->ip(),
                'user_agent' => $authData['user_agent'] ?? request()->userAgent(),
            ],
            'error_type' => 'authentication',
        ]);
    }

    /**
     * Track database errors
     */
    public static function trackDatabaseError(\Throwable $exception, array $dbData = []): void
    {
        self::captureException($exception, [
            'database' => [
                'query' => $dbData['query'] ?? 'unknown',
                'table' => $dbData['table'] ?? 'unknown',
                'operation' => $dbData['operation'] ?? 'unknown',
                'connection' => $dbData['connection'] ?? 'default',
            ],
            'error_type' => 'database',
        ]);
    }

    /**
     * Track file upload errors
     */
    public static function trackFileUploadError(\Throwable $exception, array $fileData = []): void
    {
        self::captureException($exception, [
            'file_upload' => [
                'file_name' => $fileData['file_name'] ?? 'unknown',
                'file_size' => $fileData['file_size'] ?? 0,
                'file_type' => $fileData['file_type'] ?? 'unknown',
                'upload_type' => $fileData['upload_type'] ?? 'unknown',
                'user_id' => $fileData['user_id'] ?? null,
            ],
            'error_type' => 'file_upload',
        ]);
    }

    /**
     * Track performance issues
     */
    public static function trackPerformanceIssue(string $message, array $performanceData = []): void
    {
        self::captureMessage($message, 'warning', [
            'performance' => [
                'execution_time' => $performanceData['execution_time'] ?? 0,
                'memory_usage' => $performanceData['memory_usage'] ?? 0,
                'query_count' => $performanceData['query_count'] ?? 0,
                'endpoint' => $performanceData['endpoint'] ?? 'unknown',
                'user_id' => $performanceData['user_id'] ?? null,
            ],
            'issue_type' => 'performance',
        ]);
    }

    /**
     * Track business logic errors
     */
    public static function trackBusinessLogicError(\Throwable $exception, array $businessData = []): void
    {
        self::captureException($exception, [
            'business_logic' => [
                'operation' => $businessData['operation'] ?? 'unknown',
                'entity_type' => $businessData['entity_type'] ?? 'unknown',
                'entity_id' => $businessData['entity_id'] ?? null,
                'user_id' => $businessData['user_id'] ?? null,
                'additional_data' => $businessData['additional_data'] ?? [],
            ],
            'error_type' => 'business_logic',
        ]);
    }

    /**
     * Add breadcrumb for user actions
     */
    public static function addUserActionBreadcrumb(string $action, array $data = []): void
    {
        Sentry::addBreadcrumb(
            new \Sentry\Breadcrumb(
                \Sentry\Breadcrumb::LEVEL_INFO,
                \Sentry\Breadcrumb::TYPE_USER,
                'user_action',
                $action,
                $data
            )
        );
    }

    /**
     * Add breadcrumb for API calls
     */
    public static function addAPIBreadcrumb(string $endpoint, string $method, int $statusCode, float $duration = null): void
    {
        Sentry::addBreadcrumb(
            new \Sentry\Breadcrumb(
                $statusCode >= 400 ? \Sentry\Breadcrumb::LEVEL_ERROR : \Sentry\Breadcrumb::LEVEL_INFO,
                \Sentry\Breadcrumb::TYPE_HTTP,
                'api_call',
                "{$method} {$endpoint} - {$statusCode}",
                [
                    'endpoint' => $endpoint,
                    'method' => $method,
                    'status_code' => $statusCode,
                    'duration' => $duration,
                ]
            )
        );
    }

    /**
     * Add breadcrumb for database operations
     */
    public static function addDatabaseBreadcrumb(string $query, string $table, float $duration = null): void
    {
        Sentry::addBreadcrumb(
            new \Sentry\Breadcrumb(
                \Sentry\Breadcrumb::LEVEL_INFO,
                \Sentry\Breadcrumb::TYPE_QUERY,
                'database',
                "Query on {$table}",
                [
                    'query' => $query,
                    'table' => $table,
                    'duration' => $duration,
                ]
            )
        );
    }

    /**
     * Get Sentry configuration status
     */
    public static function getStatus(): array
    {
        return [
            'enabled' => config('sentry.enabled', true),
            'dsn_configured' => !empty(config('sentry.dsn')),
            'environment' => config('sentry.environment'),
            'release' => config('sentry.release'),
            'sample_rate' => config('sentry.sample_rate'),
            'traces_sample_rate' => config('sentry.traces_sample_rate'),
        ];
    }

    /**
     * Test Sentry integration
     */
    public static function testIntegration(): bool
    {
        try {
            self::captureMessage('Sentry integration test', 'info', [
                'test' => true,
                'timestamp' => now()->toISOString(),
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Sentry test failed: ' . $e->getMessage());
            return false;
        }
    }
}

namespace App\Services;

use Sentry\State\Scope;
use Sentry\Laravel\Facade as Sentry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SentryService
{
    /**
     * Initialize Sentry with user context
     */
    public static function initializeUserContext($user = null): void
    {
        if (!$user && Auth::check()) {
            $user = Auth::user();
        }

        if ($user) {
            Sentry::configureScope(function (Scope $scope) use ($user): void {
                $scope->setUser([
                    'id' => $user->id,
                    'email' => $user->email,
                    'username' => $user->name ?? $user->first_name,
                    'role' => $user->role ?? 'customer',
                    'created_at' => $user->created_at?->toISOString(),
                ]);

                $scope->setTag('user_type', $user->role ?? 'customer');
                $scope->setTag('platform', 'carwise-ai');
                $scope->setContext('user', [
                    'id' => $user->id,
                    'name' => $user->name ?? $user->first_name,
                    'email' => $user->email,
                    'role' => $user->role ?? 'customer',
                    'created_at' => $user->created_at?->toISOString(),
                ]);
            });
        }
    }

    /**
     * Capture exception with context
     */
    public static function captureException(\Throwable $exception, array $context = []): void
    {
        Sentry::withScope(function (Scope $scope) use ($exception, $context): void {
            // Add custom context
            foreach ($context as $key => $value) {
                $scope->setContext($key, $value);
            }

            // Add request context
            $scope->setContext('request', [
                'url' => request()->url(),
                'method' => request()->method(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'headers' => request()->headers->all(),
            ]);

            // Add session context
            if (session()->isStarted()) {
                $scope->setContext('session', [
                    'id' => session()->getId(),
                    'data' => session()->all(),
                ]);
            }

            Sentry::captureException($exception);
        });
    }

    /**
     * Capture message with context
     */
    public static function captureMessage(string $message, string $level = 'info', array $context = []): void
    {
        Sentry::withScope(function (Scope $scope) use ($message, $level, $context): void {
            // Add custom context
            foreach ($context as $key => $value) {
                $scope->setContext($key, $value);
            }

            $scope->setLevel($level);
            Sentry::captureMessage($message);
        });
    }

    /**
     * Track AI diagnosis errors
     */
    public static function trackAIDiagnosisError(\Throwable $exception, array $diagnosisData = []): void
    {
        self::captureException($exception, [
            'diagnosis' => [
                'car_brand' => $diagnosisData['brand'] ?? 'unknown',
                'car_model' => $diagnosisData['model'] ?? 'unknown',
                'car_year' => $diagnosisData['year'] ?? 'unknown',
                'symptoms' => $diagnosisData['symptoms'] ?? [],
                'ai_provider' => $diagnosisData['ai_provider'] ?? 'unknown',
                'session_id' => $diagnosisData['session_id'] ?? null,
            ],
            'error_type' => 'ai_diagnosis',
        ]);
    }

    /**
     * Track car API errors
     */
    public static function trackCarAPIError(\Throwable $exception, array $apiData = []): void
    {
        self::captureException($exception, [
            'car_api' => [
                'endpoint' => $apiData['endpoint'] ?? 'unknown',
                'method' => $apiData['method'] ?? 'GET',
                'response_code' => $apiData['response_code'] ?? null,
                'response_body' => $apiData['response_body'] ?? null,
                'request_data' => $apiData['request_data'] ?? [],
            ],
            'error_type' => 'car_api',
        ]);
    }

    /**
     * Track authentication errors
     */
    public static function trackAuthError(\Throwable $exception, array $authData = []): void
    {
        self::captureException($exception, [
            'authentication' => [
                'action' => $authData['action'] ?? 'unknown',
                'user_id' => $authData['user_id'] ?? null,
                'email' => $authData['email'] ?? null,
                'ip_address' => $authData['ip_address'] ?? request()->ip(),
                'user_agent' => $authData['user_agent'] ?? request()->userAgent(),
            ],
            'error_type' => 'authentication',
        ]);
    }

    /**
     * Track database errors
     */
    public static function trackDatabaseError(\Throwable $exception, array $dbData = []): void
    {
        self::captureException($exception, [
            'database' => [
                'query' => $dbData['query'] ?? 'unknown',
                'table' => $dbData['table'] ?? 'unknown',
                'operation' => $dbData['operation'] ?? 'unknown',
                'connection' => $dbData['connection'] ?? 'default',
            ],
            'error_type' => 'database',
        ]);
    }

    /**
     * Track file upload errors
     */
    public static function trackFileUploadError(\Throwable $exception, array $fileData = []): void
    {
        self::captureException($exception, [
            'file_upload' => [
                'file_name' => $fileData['file_name'] ?? 'unknown',
                'file_size' => $fileData['file_size'] ?? 0,
                'file_type' => $fileData['file_type'] ?? 'unknown',
                'upload_type' => $fileData['upload_type'] ?? 'unknown',
                'user_id' => $fileData['user_id'] ?? null,
            ],
            'error_type' => 'file_upload',
        ]);
    }

    /**
     * Track performance issues
     */
    public static function trackPerformanceIssue(string $message, array $performanceData = []): void
    {
        self::captureMessage($message, 'warning', [
            'performance' => [
                'execution_time' => $performanceData['execution_time'] ?? 0,
                'memory_usage' => $performanceData['memory_usage'] ?? 0,
                'query_count' => $performanceData['query_count'] ?? 0,
                'endpoint' => $performanceData['endpoint'] ?? 'unknown',
                'user_id' => $performanceData['user_id'] ?? null,
            ],
            'issue_type' => 'performance',
        ]);
    }

    /**
     * Track business logic errors
     */
    public static function trackBusinessLogicError(\Throwable $exception, array $businessData = []): void
    {
        self::captureException($exception, [
            'business_logic' => [
                'operation' => $businessData['operation'] ?? 'unknown',
                'entity_type' => $businessData['entity_type'] ?? 'unknown',
                'entity_id' => $businessData['entity_id'] ?? null,
                'user_id' => $businessData['user_id'] ?? null,
                'additional_data' => $businessData['additional_data'] ?? [],
            ],
            'error_type' => 'business_logic',
        ]);
    }

    /**
     * Add breadcrumb for user actions
     */
    public static function addUserActionBreadcrumb(string $action, array $data = []): void
    {
        Sentry::addBreadcrumb(
            new \Sentry\Breadcrumb(
                \Sentry\Breadcrumb::LEVEL_INFO,
                \Sentry\Breadcrumb::TYPE_USER,
                'user_action',
                $action,
                $data
            )
        );
    }

    /**
     * Add breadcrumb for API calls
     */
    public static function addAPIBreadcrumb(string $endpoint, string $method, int $statusCode, float $duration = null): void
    {
        Sentry::addBreadcrumb(
            new \Sentry\Breadcrumb(
                $statusCode >= 400 ? \Sentry\Breadcrumb::LEVEL_ERROR : \Sentry\Breadcrumb::LEVEL_INFO,
                \Sentry\Breadcrumb::TYPE_HTTP,
                'api_call',
                "{$method} {$endpoint} - {$statusCode}",
                [
                    'endpoint' => $endpoint,
                    'method' => $method,
                    'status_code' => $statusCode,
                    'duration' => $duration,
                ]
            )
        );
    }

    /**
     * Add breadcrumb for database operations
     */
    public static function addDatabaseBreadcrumb(string $query, string $table, float $duration = null): void
    {
        Sentry::addBreadcrumb(
            new \Sentry\Breadcrumb(
                \Sentry\Breadcrumb::LEVEL_INFO,
                \Sentry\Breadcrumb::TYPE_QUERY,
                'database',
                "Query on {$table}",
                [
                    'query' => $query,
                    'table' => $table,
                    'duration' => $duration,
                ]
            )
        );
    }

    /**
     * Get Sentry configuration status
     */
    public static function getStatus(): array
    {
        return [
            'enabled' => config('sentry.enabled', true),
            'dsn_configured' => !empty(config('sentry.dsn')),
            'environment' => config('sentry.environment'),
            'release' => config('sentry.release'),
            'sample_rate' => config('sentry.sample_rate'),
            'traces_sample_rate' => config('sentry.traces_sample_rate'),
        ];
    }

    /**
     * Test Sentry integration
     */
    public static function testIntegration(): bool
    {
        try {
            self::captureMessage('Sentry integration test', 'info', [
                'test' => true,
                'timestamp' => now()->toISOString(),
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Sentry test failed: ' . $e->getMessage());
            return false;
        }
    }
}
