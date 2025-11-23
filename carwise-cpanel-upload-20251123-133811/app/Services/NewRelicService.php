<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NewRelicService
{
    private $apiKey;
    private $accountId;
    private $baseUrl;
    private $enabled;

    public function __construct()
    {
        $this->apiKey = config('services.newrelic.api_key');
        $this->accountId = config('services.newrelic.account_id');
        $this->baseUrl = config('services.newrelic.base_url', 'https://api.newrelic.com');
        $this->enabled = config('services.newrelic.enabled', false);
    }

    /**
     * Check if New Relic is enabled and configured
     */
    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->apiKey) && !empty($this->accountId);
    }

    /**
     * Send custom event to New Relic
     */
    public function sendCustomEvent(string $eventType, array $attributes = []): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        try {
            $payload = [
                'eventType' => $eventType,
                'timestamp' => time(),
                'attributes' => array_merge([
                    'app_name' => config('app.name', 'CarWise.ai'),
                    'environment' => config('app.env', 'production'),
                    'version' => '1.0.0',
                ], $attributes)
            ];

            $response = Http::withHeaders([
                'Api-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/v2/accounts/{$this->accountId}/events", [
                'event' => $payload
            ]);

            if ($response->successful()) {
                Log::info('New Relic custom event sent successfully', [
                    'event_type' => $eventType,
                    'attributes' => $attributes
                ]);
                return true;
            } else {
                Log::warning('Failed to send New Relic custom event', [
                    'event_type' => $eventType,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('New Relic custom event error', [
                'event_type' => $eventType,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Track user registration
     */
    public function trackUserRegistration(array $userData = []): bool
    {
        return $this->sendCustomEvent('UserRegistration', [
            'user_id' => $userData['id'] ?? null,
            'user_type' => $userData['role'] ?? 'customer',
            'registration_method' => $userData['method'] ?? 'email',
            'country' => $userData['country'] ?? 'unknown',
            'created_at' => $userData['created_at'] ?? now()->toISOString(),
        ]);
    }

    /**
     * Track user login
     */
    public function trackUserLogin(array $userData = []): bool
    {
        return $this->sendCustomEvent('UserLogin', [
            'user_id' => $userData['id'] ?? null,
            'user_type' => $userData['role'] ?? 'customer',
            'login_method' => $userData['method'] ?? 'email',
            'last_login' => $userData['last_login'] ?? now()->toISOString(),
        ]);
    }

    /**
     * Track car diagnosis
     */
    public function trackCarDiagnosis(array $diagnosisData = []): bool
    {
        return $this->sendCustomEvent('CarDiagnosis', [
            'user_id' => $diagnosisData['user_id'] ?? null,
            'car_brand' => $diagnosisData['brand'] ?? 'unknown',
            'car_model' => $diagnosisData['model'] ?? 'unknown',
            'car_year' => $diagnosisData['year'] ?? 'unknown',
            'diagnosis_type' => $diagnosisData['type'] ?? 'ai',
            'severity' => $diagnosisData['severity'] ?? 'unknown',
            'confidence_score' => $diagnosisData['confidence'] ?? 0,
            'ai_provider' => $diagnosisData['ai_provider'] ?? 'unknown',
            'processing_time' => $diagnosisData['processing_time'] ?? 0,
            'session_id' => $diagnosisData['session_id'] ?? null,
        ]);
    }

    /**
     * Track car addition
     */
    public function trackCarAdded(array $carData = []): bool
    {
        return $this->sendCustomEvent('CarAdded', [
            'user_id' => $carData['user_id'] ?? null,
            'car_brand' => $carData['brand'] ?? 'unknown',
            'car_model' => $carData['model'] ?? 'unknown',
            'car_year' => $carData['year'] ?? 'unknown',
            'fuel_type' => $carData['fuel_type'] ?? 'unknown',
            'engine_type' => $carData['engine_type'] ?? 'unknown',
            'mileage' => $carData['mileage'] ?? 0,
        ]);
    }

    /**
     * Track car part search
     */
    public function trackPartSearch(array $searchData = []): bool
    {
        return $this->sendCustomEvent('PartSearch', [
            'user_id' => $searchData['user_id'] ?? null,
            'search_term' => $searchData['term'] ?? '',
            'car_brand' => $searchData['brand'] ?? 'unknown',
            'car_model' => $searchData['model'] ?? 'unknown',
            'part_category' => $searchData['category'] ?? 'unknown',
            'results_count' => $searchData['results_count'] ?? 0,
            'search_duration' => $searchData['duration'] ?? 0,
        ]);
    }

    /**
     * Track API performance
     */
    public function trackAPIPerformance(string $endpoint, string $method, int $statusCode, float $duration, array $context = []): bool
    {
        return $this->sendCustomEvent('APIPerformance', [
            'endpoint' => $endpoint,
            'method' => $method,
            'status_code' => $statusCode,
            'duration_ms' => $duration * 1000,
            'user_id' => $context['user_id'] ?? null,
            'response_size' => $context['response_size'] ?? 0,
            'cache_hit' => $context['cache_hit'] ?? false,
        ]);
    }

    /**
     * Track database performance
     */
    public function trackDatabasePerformance(string $query, string $table, float $duration, int $rowsAffected = 0): bool
    {
        return $this->sendCustomEvent('DatabasePerformance', [
            'query_type' => $this->getQueryType($query),
            'table_name' => $table,
            'duration_ms' => $duration * 1000,
            'rows_affected' => $rowsAffected,
            'query_hash' => md5($query),
        ]);
    }

    /**
     * Track business metrics
     */
    public function trackBusinessMetric(string $metricName, float $value, array $attributes = []): bool
    {
        return $this->sendCustomEvent('BusinessMetric', [
            'metric_name' => $metricName,
            'metric_value' => $value,
            'timestamp' => time(),
            'attributes' => $attributes,
        ]);
    }

    /**
     * Track error occurrence
     */
    public function trackError(string $errorType, string $errorMessage, array $context = []): bool
    {
        return $this->sendCustomEvent('ErrorOccurrence', [
            'error_type' => $errorType,
            'error_message' => $errorMessage,
            'user_id' => $context['user_id'] ?? null,
            'endpoint' => $context['endpoint'] ?? 'unknown',
            'stack_trace' => $context['stack_trace'] ?? null,
            'severity' => $context['severity'] ?? 'error',
        ]);
    }

    /**
     * Track page view
     */
    public function trackPageView(string $page, array $context = []): bool
    {
        return $this->sendCustomEvent('PageView', [
            'page_name' => $page,
            'page_url' => $context['url'] ?? request()->url(),
            'user_id' => $context['user_id'] ?? null,
            'session_id' => $context['session_id'] ?? session()->getId(),
            'load_time' => $context['load_time'] ?? 0,
            'referrer' => $context['referrer'] ?? request()->header('referer'),
        ]);
    }

    /**
     * Track conversion
     */
    public function trackConversion(string $conversionType, array $conversionData = []): bool
    {
        return $this->sendCustomEvent('Conversion', [
            'conversion_type' => $conversionType,
            'user_id' => $conversionData['user_id'] ?? null,
            'value' => $conversionData['value'] ?? 0,
            'currency' => $conversionData['currency'] ?? 'USD',
            'funnel_step' => $conversionData['funnel_step'] ?? 'unknown',
            'conversion_source' => $conversionData['source'] ?? 'unknown',
        ]);
    }

    /**
     * Get query type from SQL query
     */
    private function getQueryType(string $query): string
    {
        $query = trim(strtoupper($query));
        
        if (str_starts_with($query, 'SELECT')) return 'SELECT';
        if (str_starts_with($query, 'INSERT')) return 'INSERT';
        if (str_starts_with($query, 'UPDATE')) return 'UPDATE';
        if (str_starts_with($query, 'DELETE')) return 'DELETE';
        if (str_starts_with($query, 'CREATE')) return 'CREATE';
        if (str_starts_with($query, 'DROP')) return 'DROP';
        if (str_starts_with($query, 'ALTER')) return 'ALTER';
        
        return 'OTHER';
    }

    /**
     * Get New Relic configuration status
     */
    public function getStatus(): array
    {
        return [
            'enabled' => $this->enabled,
            'api_key_configured' => !empty($this->apiKey),
            'account_id_configured' => !empty($this->accountId),
            'base_url' => $this->baseUrl,
            'fully_configured' => $this->isEnabled(),
        ];
    }

    /**
     * Test New Relic integration
     */
    public function testIntegration(): bool
    {
        if (!$this->isEnabled()) {
            Log::warning('New Relic integration test skipped - not properly configured');
            return false;
        }

        try {
            $result = $this->sendCustomEvent('IntegrationTest', [
                'test' => true,
                'timestamp' => now()->toISOString(),
                'environment' => config('app.env'),
            ]);

            if ($result) {
                Log::info('New Relic integration test successful');
                return true;
            } else {
                Log::error('New Relic integration test failed');
                return false;
            }
        } catch (\Exception $e) {
            Log::error('New Relic integration test error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get application performance summary
     */
    public function getPerformanceSummary(): array
    {
        // This would typically query New Relic's API for performance data
        // For now, return a mock structure
        return [
            'response_time' => [
                'average' => 0,
                'p95' => 0,
                'p99' => 0,
            ],
            'throughput' => [
                'requests_per_minute' => 0,
                'errors_per_minute' => 0,
            ],
            'errors' => [
                'error_rate' => 0,
                'top_errors' => [],
            ],
            'database' => [
                'slow_queries' => 0,
                'average_query_time' => 0,
            ],
        ];
    }
}

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;















