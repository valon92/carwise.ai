<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleAnalyticsService
{
    private $measurementId;
    private $apiSecret;
    private $propertyId;
    private $enabled;

    public function __construct()
    {
        $this->measurementId = config('services.google_analytics.measurement_id');
        $this->apiSecret = config('services.google_analytics.api_secret');
        $this->propertyId = config('services.google_analytics.property_id');
        $this->enabled = config('services.google_analytics.enabled');
    }

    /**
     * Track a custom event
     */
    public function trackEvent(string $eventName, array $parameters = []): void
    {
        if (!$this->enabled || !$this->measurementId) {
            return;
        }

        try {
            $this->sendEvent($eventName, $parameters);
        } catch (\Exception $e) {
            Log::error('Google Analytics tracking failed: ' . $e->getMessage());
        }
    }

    /**
     * Track user registration
     */
    public function trackUserRegistration(array $userData = []): void
    {
        $this->trackEvent('user_registration', [
            'user_type' => $userData['role'] ?? 'customer',
            'registration_method' => $userData['method'] ?? 'email',
            'country' => $userData['country'] ?? 'unknown'
        ]);
    }

    /**
     * Track car diagnosis
     */
    public function trackDiagnosis(array $diagnosisData = []): void
    {
        $this->trackEvent('car_diagnosis', [
            'car_brand' => $diagnosisData['brand'] ?? 'unknown',
            'car_model' => $diagnosisData['model'] ?? 'unknown',
            'car_year' => $diagnosisData['year'] ?? 'unknown',
            'diagnosis_type' => $diagnosisData['type'] ?? 'ai',
            'severity' => $diagnosisData['severity'] ?? 'unknown',
            'confidence_score' => $diagnosisData['confidence'] ?? 0
        ]);
    }

    /**
     * Track car part search
     */
    public function trackPartSearch(array $searchData = []): void
    {
        $this->trackEvent('part_search', [
            'search_term' => $searchData['term'] ?? '',
            'car_brand' => $searchData['brand'] ?? 'unknown',
            'car_model' => $searchData['model'] ?? 'unknown',
            'part_category' => $searchData['category'] ?? 'unknown',
            'results_count' => $searchData['results_count'] ?? 0
        ]);
    }

    /**
     * Track car addition
     */
    public function trackCarAdded(array $carData = []): void
    {
        $this->trackEvent('car_added', [
            'car_brand' => $carData['brand'] ?? 'unknown',
            'car_model' => $carData['model'] ?? 'unknown',
            'car_year' => $carData['year'] ?? 'unknown',
            'fuel_type' => $carData['fuel_type'] ?? 'unknown',
            'engine_type' => $carData['engine_type'] ?? 'unknown'
        ]);
    }

    /**
     * Track mechanic interaction
     */
    public function trackMechanicInteraction(array $interactionData = []): void
    {
        $this->trackEvent('mechanic_interaction', [
            'interaction_type' => $interactionData['type'] ?? 'unknown',
            'mechanic_id' => $interactionData['mechanic_id'] ?? 'unknown',
            'location' => $interactionData['location'] ?? 'unknown',
            'rating' => $interactionData['rating'] ?? 0
        ]);
    }

    /**
     * Track page view
     */
    public function trackPageView(string $page, array $parameters = []): void
    {
        $this->trackEvent('page_view', array_merge([
            'page_title' => $page,
            'page_location' => request()->url(),
            'page_path' => request()->path()
        ], $parameters));
    }

    /**
     * Track conversion (e.g., successful diagnosis, part purchase)
     */
    public function trackConversion(string $conversionType, array $conversionData = []): void
    {
        $this->trackEvent('conversion', array_merge([
            'conversion_type' => $conversionType,
            'value' => $conversionData['value'] ?? 0,
            'currency' => $conversionData['currency'] ?? 'USD'
        ], $conversionData));
    }

    /**
     * Track user engagement
     */
    public function trackEngagement(string $engagementType, array $engagementData = []): void
    {
        $this->trackEvent('user_engagement', array_merge([
            'engagement_type' => $engagementType,
            'session_duration' => $engagementData['duration'] ?? 0,
            'interactions' => $engagementData['interactions'] ?? 0
        ], $engagementData));
    }

    /**
     * Send event to Google Analytics Measurement Protocol
     */
    private function sendEvent(string $eventName, array $parameters = []): void
    {
        if (!$this->measurementId || !$this->apiSecret) {
            return;
        }

        $clientId = $this->getClientId();
        
        $payload = [
            'client_id' => $clientId,
            'events' => [
                [
                    'name' => $eventName,
                    'params' => array_merge([
                        'timestamp_micros' => microtime(true) * 1000000,
                        'engagement_time_msec' => 1000
                    ], $parameters)
                ]
            ]
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://www.google-analytics.com/mp/collect?measurement_id={$this->measurementId}&api_secret={$this->apiSecret}", $payload);

        if (!$response->successful()) {
            Log::warning('Google Analytics event failed to send', [
                'event' => $eventName,
                'status' => $response->status(),
                'response' => $response->body()
            ]);
        }
    }

    /**
     * Get or generate client ID
     */
    private function getClientId(): string
    {
        // Try to get from session first
        $clientId = session('ga_client_id');
        
        if (!$clientId) {
            // Generate new client ID
            $clientId = $this->generateClientId();
            session(['ga_client_id' => $clientId]);
        }
        
        return $clientId;
    }

    /**
     * Generate a unique client ID
     */
    private function generateClientId(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Get analytics dashboard data
     */
    public function getAnalyticsData(array $filters = []): array
    {
        // This would integrate with Google Analytics Reporting API
        // For now, return mock data structure
        return [
            'total_users' => 0,
            'total_diagnoses' => 0,
            'total_cars' => 0,
            'conversion_rate' => 0,
            'top_car_brands' => [],
            'diagnosis_success_rate' => 0,
            'user_engagement' => []
        ];
    }
}

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleAnalyticsService
{
    private $measurementId;
    private $apiSecret;
    private $propertyId;
    private $enabled;

    public function __construct()
    {
        $this->measurementId = config('services.google_analytics.measurement_id');
        $this->apiSecret = config('services.google_analytics.api_secret');
        $this->propertyId = config('services.google_analytics.property_id');
        $this->enabled = config('services.google_analytics.enabled');
    }

    /**
     * Track a custom event
     */
    public function trackEvent(string $eventName, array $parameters = []): void
    {
        if (!$this->enabled || !$this->measurementId) {
            return;
        }

        try {
            $this->sendEvent($eventName, $parameters);
        } catch (\Exception $e) {
            Log::error('Google Analytics tracking failed: ' . $e->getMessage());
        }
    }

    /**
     * Track user registration
     */
    public function trackUserRegistration(array $userData = []): void
    {
        $this->trackEvent('user_registration', [
            'user_type' => $userData['role'] ?? 'customer',
            'registration_method' => $userData['method'] ?? 'email',
            'country' => $userData['country'] ?? 'unknown'
        ]);
    }

    /**
     * Track car diagnosis
     */
    public function trackDiagnosis(array $diagnosisData = []): void
    {
        $this->trackEvent('car_diagnosis', [
            'car_brand' => $diagnosisData['brand'] ?? 'unknown',
            'car_model' => $diagnosisData['model'] ?? 'unknown',
            'car_year' => $diagnosisData['year'] ?? 'unknown',
            'diagnosis_type' => $diagnosisData['type'] ?? 'ai',
            'severity' => $diagnosisData['severity'] ?? 'unknown',
            'confidence_score' => $diagnosisData['confidence'] ?? 0
        ]);
    }

    /**
     * Track car part search
     */
    public function trackPartSearch(array $searchData = []): void
    {
        $this->trackEvent('part_search', [
            'search_term' => $searchData['term'] ?? '',
            'car_brand' => $searchData['brand'] ?? 'unknown',
            'car_model' => $searchData['model'] ?? 'unknown',
            'part_category' => $searchData['category'] ?? 'unknown',
            'results_count' => $searchData['results_count'] ?? 0
        ]);
    }

    /**
     * Track car addition
     */
    public function trackCarAdded(array $carData = []): void
    {
        $this->trackEvent('car_added', [
            'car_brand' => $carData['brand'] ?? 'unknown',
            'car_model' => $carData['model'] ?? 'unknown',
            'car_year' => $carData['year'] ?? 'unknown',
            'fuel_type' => $carData['fuel_type'] ?? 'unknown',
            'engine_type' => $carData['engine_type'] ?? 'unknown'
        ]);
    }

    /**
     * Track mechanic interaction
     */
    public function trackMechanicInteraction(array $interactionData = []): void
    {
        $this->trackEvent('mechanic_interaction', [
            'interaction_type' => $interactionData['type'] ?? 'unknown',
            'mechanic_id' => $interactionData['mechanic_id'] ?? 'unknown',
            'location' => $interactionData['location'] ?? 'unknown',
            'rating' => $interactionData['rating'] ?? 0
        ]);
    }

    /**
     * Track page view
     */
    public function trackPageView(string $page, array $parameters = []): void
    {
        $this->trackEvent('page_view', array_merge([
            'page_title' => $page,
            'page_location' => request()->url(),
            'page_path' => request()->path()
        ], $parameters));
    }

    /**
     * Track conversion (e.g., successful diagnosis, part purchase)
     */
    public function trackConversion(string $conversionType, array $conversionData = []): void
    {
        $this->trackEvent('conversion', array_merge([
            'conversion_type' => $conversionType,
            'value' => $conversionData['value'] ?? 0,
            'currency' => $conversionData['currency'] ?? 'USD'
        ], $conversionData));
    }

    /**
     * Track user engagement
     */
    public function trackEngagement(string $engagementType, array $engagementData = []): void
    {
        $this->trackEvent('user_engagement', array_merge([
            'engagement_type' => $engagementType,
            'session_duration' => $engagementData['duration'] ?? 0,
            'interactions' => $engagementData['interactions'] ?? 0
        ], $engagementData));
    }

    /**
     * Send event to Google Analytics Measurement Protocol
     */
    private function sendEvent(string $eventName, array $parameters = []): void
    {
        if (!$this->measurementId || !$this->apiSecret) {
            return;
        }

        $clientId = $this->getClientId();
        
        $payload = [
            'client_id' => $clientId,
            'events' => [
                [
                    'name' => $eventName,
                    'params' => array_merge([
                        'timestamp_micros' => microtime(true) * 1000000,
                        'engagement_time_msec' => 1000
                    ], $parameters)
                ]
            ]
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://www.google-analytics.com/mp/collect?measurement_id={$this->measurementId}&api_secret={$this->apiSecret}", $payload);

        if (!$response->successful()) {
            Log::warning('Google Analytics event failed to send', [
                'event' => $eventName,
                'status' => $response->status(),
                'response' => $response->body()
            ]);
        }
    }

    /**
     * Get or generate client ID
     */
    private function getClientId(): string
    {
        // Try to get from session first
        $clientId = session('ga_client_id');
        
        if (!$clientId) {
            // Generate new client ID
            $clientId = $this->generateClientId();
            session(['ga_client_id' => $clientId]);
        }
        
        return $clientId;
    }

    /**
     * Generate a unique client ID
     */
    private function generateClientId(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Get analytics dashboard data
     */
    public function getAnalyticsData(array $filters = []): array
    {
        // This would integrate with Google Analytics Reporting API
        // For now, return mock data structure
        return [
            'total_users' => 0,
            'total_diagnoses' => 0,
            'total_cars' => 0,
            'conversion_rate' => 0,
            'top_car_brands' => [],
            'diagnosis_success_rate' => 0,
            'user_engagement' => []
        ];
    }
}














