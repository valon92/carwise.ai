<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class LicensedPartsAPIService
{
    protected $licensedProviders;

    public function __construct()
    {
        $this->licensedProviders = [
            // CarAPI.app - Vehicle Data & Parts API
            'carapi' => [
                'name' => 'CarAPI.app',
                'api_url' => env('CARAPI_BASE_URL', 'https://carapi.app/api'),
                'api_key' => env('CARAPI_TOKEN'),
                'api_secret' => env('CARAPI_SECRET'),
                'commission_rate' => 5.0,
                'enabled' => env('CARAPI_ENABLED', false),
                'type' => 'data_provider'
            ],
            // Official OEM Parts APIs
            'ford_parts' => [
                'name' => 'Ford Parts',
                'api_url' => 'https://api.ford.com/parts/v1',
                'api_key' => env('FORD_PARTS_API_KEY'),
                'commission_rate' => 8.0,
                'enabled' => env('FORD_PARTS_ENABLED', false),
                'type' => 'oem'
            ],
            'gm_parts' => [
                'name' => 'General Motors Parts',
                'api_url' => 'https://api.gm.com/parts/v2',
                'api_key' => env('GM_PARTS_API_KEY'),
                'commission_rate' => 7.5,
                'enabled' => env('GM_PARTS_ENABLED', false),
                'type' => 'oem'
            ],
            'toyota_parts' => [
                'name' => 'Toyota Parts',
                'api_url' => 'https://api.toyota.com/parts/v1',
                'api_key' => env('TOYOTA_PARTS_API_KEY'),
                'commission_rate' => 8.5,
                'enabled' => env('TOYOTA_PARTS_ENABLED', false),
                'type' => 'oem'
            ],
            'honda_parts' => [
                'name' => 'Honda Parts',
                'api_url' => 'https://api.honda.com/parts/v1',
                'api_key' => env('HONDA_PARTS_API_KEY'),
                'commission_rate' => 8.0,
                'enabled' => env('HONDA_PARTS_ENABLED', false),
                'type' => 'oem'
            ],
            'bmw_parts' => [
                'name' => 'BMW Parts',
                'api_url' => 'https://api.bmw.com/parts/v1',
                'api_key' => env('BMW_PARTS_API_KEY'),
                'commission_rate' => 9.0,
                'enabled' => env('BMW_PARTS_ENABLED', false),
                'type' => 'oem'
            ],
            'mercedes_parts' => [
                'name' => 'Mercedes-Benz Parts',
                'api_url' => 'https://api.mercedes-benz.com/parts/v1',
                'api_key' => env('MERCEDES_PARTS_API_KEY'),
                'commission_rate' => 9.5,
                'enabled' => env('MERCEDES_PARTS_ENABLED', false),
                'type' => 'oem'
            ],

            // Licensed Aftermarket Parts APIs
            'bosch_parts' => [
                'name' => 'Bosch Automotive Parts',
                'api_url' => 'https://api.bosch-automotive.com/v1',
                'api_key' => env('BOSCH_PARTS_API_KEY'),
                'commission_rate' => 6.0,
                'enabled' => env('BOSCH_PARTS_ENABLED', false),
                'type' => 'aftermarket'
            ],
            'continental_parts' => [
                'name' => 'Continental Automotive',
                'api_url' => 'https://api.continental-automotive.com/v1',
                'api_key' => env('CONTINENTAL_PARTS_API_KEY'),
                'commission_rate' => 5.5,
                'enabled' => env('CONTINENTAL_PARTS_ENABLED', false),
                'type' => 'aftermarket'
            ],
            'delphi_parts' => [
                'name' => 'Delphi Technologies',
                'api_url' => 'https://api.delphi.com/v1',
                'api_key' => env('DELPHI_PARTS_API_KEY'),
                'commission_rate' => 6.5,
                'enabled' => env('DELPHI_PARTS_ENABLED', false),
                'type' => 'aftermarket'
            ],
            'denso_parts' => [
                'name' => 'DENSO Parts',
                'api_url' => 'https://api.denso.com/v1',
                'api_key' => env('DENSO_PARTS_API_KEY'),
                'commission_rate' => 7.0,
                'enabled' => env('DENSO_PARTS_ENABLED', false),
                'type' => 'aftermarket'
            ],

            // Licensed Retailer APIs
            'autozone_official' => [
                'name' => 'AutoZone Official',
                'api_url' => 'https://api.autozone.com/v2',
                'api_key' => env('AUTOZONE_OFFICIAL_API_KEY'),
                'commission_rate' => 5.0,
                'enabled' => env('AUTOZONE_OFFICIAL_ENABLED', false),
                'type' => 'retailer'
            ],
            'oreilly_official' => [
                'name' => 'O\'Reilly Auto Parts Official',
                'api_url' => 'https://api.oreillyauto.com/v2',
                'api_key' => env('OREILLY_OFFICIAL_API_KEY'),
                'commission_rate' => 4.5,
                'enabled' => env('OREILLY_OFFICIAL_ENABLED', false),
                'type' => 'retailer'
            ],
            'napa_official' => [
                'name' => 'NAPA Auto Parts Official',
                'api_url' => 'https://api.napaonline.com/v2',
                'api_key' => env('NAPA_OFFICIAL_API_KEY'),
                'commission_rate' => 6.0,
                'enabled' => env('NAPA_OFFICIAL_ENABLED', false),
                'type' => 'retailer'
            ],
            'advance_official' => [
                'name' => 'Advance Auto Parts Official',
                'api_url' => 'https://api.advanceautoparts.com/v2',
                'api_key' => env('ADVANCE_OFFICIAL_API_KEY'),
                'commission_rate' => 4.0,
                'enabled' => env('ADVANCE_OFFICIAL_ENABLED', false),
                'type' => 'retailer'
            ],

            // Licensed Online Marketplaces
            'amazon_automotive' => [
                'name' => 'Amazon Automotive',
                'api_url' => 'https://webservices.amazon.com/paapi5',
                'api_key' => env('AMAZON_AUTOMOTIVE_API_KEY'),
                'secret_key' => env('AMAZON_AUTOMOTIVE_SECRET_KEY'),
                'partner_tag' => env('AMAZON_AUTOMOTIVE_PARTNER_TAG'),
                'commission_rate' => 2.5,
                'enabled' => env('AMAZON_AUTOMOTIVE_ENABLED', false),
                'type' => 'marketplace'
            ],
            'ebay_motors_official' => [
                'name' => 'eBay Motors Official',
                'api_url' => 'https://api.ebay.com/buy/browse/v1',
                'api_key' => env('EBAY_MOTORS_OFFICIAL_API_KEY'),
                'commission_rate' => 3.0,
                'enabled' => env('EBAY_MOTORS_OFFICIAL_ENABLED', false),
                'type' => 'marketplace'
            ],

            // Licensed Parts Databases
            'tecdoc_official' => [
                'name' => 'TecDoc Official',
                'api_url' => 'https://api.tecdoc.net/v1',
                'api_key' => env('TECDOC_OFFICIAL_API_KEY'),
                'commission_rate' => 0.0, // Data provider, not sales
                'enabled' => env('TECDOC_OFFICIAL_ENABLED', false),
                'type' => 'database'
            ],
            'autodata_official' => [
                'name' => 'Autodata Official',
                'api_url' => 'https://api.autodata-group.com/v1',
                'api_key' => env('AUTODATA_OFFICIAL_API_KEY'),
                'commission_rate' => 0.0, // Data provider, not sales
                'enabled' => env('AUTODATA_OFFICIAL_ENABLED', false),
                'type' => 'database'
            ]
        ];
    }

    /**
     * Search parts across all licensed providers
     */
    public function searchLicensedParts(string $query, array $filters = []): array
    {
        $results = [];
        $enabledProviders = $this->getEnabledProviders();

        foreach ($enabledProviders as $providerId => $provider) {
            try {
                $providerResults = $this->searchProviderParts($providerId, $query, $filters);
                if (!empty($providerResults)) {
                    $results[$providerId] = [
                        'provider' => $provider,
                        'parts' => $providerResults,
                        'count' => count($providerResults)
                    ];
                }
            } catch (\Exception $e) {
                Log::error("Error searching parts from {$provider['name']}: " . $e->getMessage());
                continue;
            }
        }

        return $results;
    }

    /**
     * Search parts from a specific licensed provider
     */
    public function searchProviderParts(string $providerId, string $query, array $filters = []): array
    {
        if (!isset($this->licensedProviders[$providerId])) {
            throw new \Exception("Provider not found: {$providerId}");
        }

        $provider = $this->licensedProviders[$providerId];
        
        if (!$provider['enabled']) {
            throw new \Exception("Provider is disabled: {$provider['name']}");
        }

        $cacheKey = "licensed_parts_{$providerId}_" . md5($query . serialize($filters));
        
        return Cache::remember($cacheKey, 1800, function () use ($provider, $query, $filters) {
            return $this->makeLicensedRequest($provider, 'search', array_merge([
                'query' => $query,
                'limit' => 50
            ], $filters));
        });
    }

    /**
     * Get part details from licensed provider
     */
    public function getPartDetails(string $providerId, string $partNumber): array
    {
        if (!isset($this->licensedProviders[$providerId])) {
            throw new \Exception("Provider not found: {$providerId}");
        }

        $provider = $this->licensedProviders[$providerId];
        
        $cacheKey = "licensed_part_details_{$providerId}_{$partNumber}";
        
        return Cache::remember($cacheKey, 3600, function () use ($provider, $partNumber) {
            return $this->makeLicensedRequest($provider, 'part-details', [
                'part_number' => $partNumber
            ]);
        });
    }

    /**
     * Get AI-powered part suggestions based on car model
     */
    public function getAIPartSuggestions(string $make, string $model, int $year, string $issue = null): array
    {
        $suggestions = [];
        $enabledProviders = $this->getEnabledProviders();

        // Get common parts for this vehicle
        $commonParts = $this->getCommonPartsForVehicle($make, $model, $year);
        
        // If issue is provided, get specific parts for that issue
        if ($issue) {
            $issueParts = $this->getPartsForIssue($make, $model, $year, $issue);
            $suggestions = array_merge($suggestions, $issueParts);
        }

        // Add common maintenance parts
        $suggestions = array_merge($suggestions, $commonParts);

        // Remove duplicates and sort by relevance
        $suggestions = $this->deduplicateAndRankSuggestions($suggestions);

        return array_slice($suggestions, 0, 20); // Return top 20 suggestions
    }

    /**
     * Make request to licensed provider API
     */
    private function makeLicensedRequest(array $provider, string $endpoint, array $params): array
    {
        $url = $provider['api_url'] . '/' . $endpoint;
        
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'User-Agent' => 'CarWise-AI/1.0'
        ];

        // Add authentication based on provider type
        if (isset($provider['api_key'])) {
            $headers['Authorization'] = 'Bearer ' . $provider['api_key'];
        }

        if (isset($provider['secret_key']) && isset($provider['partner_tag'])) {
            // Amazon PA-API 5.0 authentication
            $headers['X-Amz-Access-Key'] = $provider['api_key'];
            $headers['X-Amz-Secret-Key'] = $provider['secret_key'];
            $headers['X-Amz-Partner-Tag'] = $provider['partner_tag'];
        }

        $response = Http::withHeaders($headers)
            ->timeout(30)
            ->get($url, $params);

        if (!$response->successful()) {
            throw new \Exception("API request failed: " . $response->body());
        }

        return $response->json();
    }

    /**
     * Get common parts for a specific vehicle
     */
    private function getCommonPartsForVehicle(string $make, string $model, int $year): array
    {
        $commonParts = [
            'oil_filter' => ['category' => 'Engine', 'priority' => 1],
            'air_filter' => ['category' => 'Engine', 'priority' => 2],
            'cabin_filter' => ['category' => 'HVAC', 'priority' => 3],
            'brake_pads' => ['category' => 'Brakes', 'priority' => 4],
            'brake_rotors' => ['category' => 'Brakes', 'priority' => 5],
            'spark_plugs' => ['category' => 'Engine', 'priority' => 6],
            'battery' => ['category' => 'Electrical', 'priority' => 7],
            'alternator' => ['category' => 'Electrical', 'priority' => 8],
            'starter' => ['category' => 'Electrical', 'priority' => 9],
            'water_pump' => ['category' => 'Cooling', 'priority' => 10],
            'thermostat' => ['category' => 'Cooling', 'priority' => 11],
            'timing_belt' => ['category' => 'Engine', 'priority' => 12],
            'serpentine_belt' => ['category' => 'Engine', 'priority' => 13],
            'fuel_filter' => ['category' => 'Fuel', 'priority' => 14],
            'transmission_filter' => ['category' => 'Transmission', 'priority' => 15]
        ];

        $suggestions = [];
        foreach ($commonParts as $partType => $info) {
            $suggestions[] = [
                'part_type' => $partType,
                'category' => $info['category'],
                'priority' => $info['priority'],
                'make' => $make,
                'model' => $model,
                'year' => $year,
                'ai_recommended' => true,
                'reason' => 'Common maintenance part for this vehicle'
            ];
        }

        return $suggestions;
    }

    /**
     * Get parts for specific issue
     */
    private function getPartsForIssue(string $make, string $model, int $year, string $issue): array
    {
        $issueParts = [
            'engine_problem' => ['spark_plugs', 'fuel_filter', 'air_filter', 'oil_filter'],
            'brake_problem' => ['brake_pads', 'brake_rotors', 'brake_fluid'],
            'electrical_problem' => ['battery', 'alternator', 'starter'],
            'cooling_problem' => ['water_pump', 'thermostat', 'radiator'],
            'transmission_problem' => ['transmission_filter', 'transmission_fluid'],
            'suspension_problem' => ['shock_absorbers', 'struts', 'control_arms']
        ];

        $suggestions = [];
        if (isset($issueParts[$issue])) {
            foreach ($issueParts[$issue] as $partType) {
                $suggestions[] = [
                    'part_type' => $partType,
                    'make' => $make,
                    'model' => $model,
                    'year' => $year,
                    'ai_recommended' => true,
                    'reason' => "Recommended for {$issue} issue"
                ];
            }
        }

        return $suggestions;
    }

    /**
     * Deduplicate and rank suggestions
     */
    private function deduplicateAndRankSuggestions(array $suggestions): array
    {
        $unique = [];
        $seen = [];

        foreach ($suggestions as $suggestion) {
            $key = $suggestion['part_type'] . '_' . $suggestion['make'] . '_' . $suggestion['model'] . '_' . $suggestion['year'];
            
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = $suggestion;
            }
        }

        // Sort by priority
        usort($unique, function ($a, $b) {
            return ($a['priority'] ?? 999) <=> ($b['priority'] ?? 999);
        });

        return $unique;
    }

    /**
     * Get enabled providers
     */
    private function getEnabledProviders(): array
    {
        return array_filter($this->licensedProviders, function ($provider) {
            return $provider['enabled'] && !empty($provider['api_key']);
        });
    }

    /**
     * Get provider statistics
     */
    public function getProviderStats(): array
    {
        $stats = [];
        $enabledProviders = $this->getEnabledProviders();

        foreach ($enabledProviders as $providerId => $provider) {
            $stats[$providerId] = [
                'name' => $provider['name'],
                'type' => $provider['type'],
                'commission_rate' => $provider['commission_rate'],
                'status' => 'active'
            ];
        }

        return $stats;
    }

    /**
     * Handle CarAPI.app requests
     */
    private function handleCarAPIRequest(string $endpoint, array $params = []): ?array
    {
        $provider = $this->licensedProviders['carapi'];
        
        if (!$provider['enabled'] || empty($provider['api_key'])) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $provider['api_key'],
                'X-API-Secret' => $provider['api_secret'],
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->get($provider['api_url'] . '/' . $endpoint, $params);

            if ($response->successful()) {
                $data = $response->json();
                
                // Transform CarAPI data to our standard format
                return $this->transformCarAPIData($data, $endpoint);
            }

            Log::warning('CarAPI request failed', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('CarAPI request error', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Transform CarAPI data to our standard format
     */
    private function transformCarAPIData(array $data, string $endpoint): array
    {
        $transformed = [];

        switch ($endpoint) {
            case 'vehicles/parts':
                foreach ($data['data'] ?? [] as $part) {
                    $transformed[] = [
                        'id' => $part['id'] ?? uniqid(),
                        'name' => $part['name'] ?? 'Unknown Part',
                        'brand' => $part['brand'] ?? 'CarAPI',
                        'part_number' => $part['part_number'] ?? 'N/A',
                        'description' => $part['description'] ?? '',
                        'price' => $part['price'] ?? 0,
                        'currency' => $part['currency'] ?? 'USD',
                        'category' => $part['category'] ?? 'general',
                        'compatibility' => $part['compatibility'] ?? [],
                        'in_stock' => $part['in_stock'] ?? true,
                        'shipping_time' => $part['shipping_time'] ?? '2-3 business days',
                        'provider' => 'CarAPI.app',
                        'provider_id' => 'carapi',
                        'commission_rate' => 5.0,
                        'source' => 'carapi'
                    ];
                }
                break;

            case 'vehicles':
                foreach ($data['data'] ?? [] as $vehicle) {
                    $transformed[] = [
                        'id' => $vehicle['id'] ?? uniqid(),
                        'make' => $vehicle['make'] ?? '',
                        'model' => $vehicle['model'] ?? '',
                        'year' => $vehicle['year'] ?? '',
                        'body_type' => $vehicle['body_type'] ?? '',
                        'engine' => $vehicle['engine'] ?? '',
                        'transmission' => $vehicle['transmission'] ?? '',
                        'fuel_type' => $vehicle['fuel_type'] ?? '',
                        'provider' => 'CarAPI.app',
                        'source' => 'carapi'
                    ];
                }
                break;

            default:
                $transformed = $data;
        }

        return $transformed;
    }
}
