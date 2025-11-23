<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MultiBrandPlatformAPIService
{
    private $platformConfigs;
    private $defaultTimeout = 30;

    public function __construct()
    {
        $this->platformConfigs = [
            'smartcar' => [
                'enabled' => config('services.smartcar.enabled', false),
                'client_id' => config('services.smartcar.client_id'),
                'client_secret' => config('services.smartcar.client_secret'),
                'redirect_uri' => config('services.smartcar.redirect_uri'),
                'base_url' => config('services.smartcar.base_url', 'https://api.smartcar.com'),
                'supported_brands' => [
                    'BMW', 'Ford', 'Volkswagen', 'Tesla', 'Toyota', 'Hyundai', 'Kia', 'Genesis',
                    'Mercedes-Benz', 'Audi', 'Porsche', 'Volvo', 'Nissan', 'Honda', 'Mazda',
                    'Subaru', 'Mitsubishi', 'Jaguar', 'Land Rover', 'Mini', 'Fiat', 'Chrysler',
                    'Dodge', 'Jeep', 'Ram', 'Chevrolet', 'GMC', 'Cadillac', 'Buick', 'Lincoln'
                ],
                'endpoints' => [
                    'auth' => '/v2.0/oauth/authorize',
                    'token' => '/v2.0/oauth/token',
                    'vehicles' => '/v2.0/vehicles',
                    'vehicle_data' => '/v2.0/vehicles/{vehicle_id}',
                    'battery' => '/v2.0/vehicles/{vehicle_id}/battery',
                    'charge' => '/v2.0/vehicles/{vehicle_id}/charge',
                    'engine_oil' => '/v2.0/vehicles/{vehicle_id}/engine/oil',
                    'fuel' => '/v2.0/vehicles/{vehicle_id}/fuel',
                    'location' => '/v2.0/vehicles/{vehicle_id}/location',
                    'odometer' => '/v2.0/vehicles/{vehicle_id}/odometer',
                    'tire_pressure' => '/v2.0/vehicles/{vehicle_id}/tires/pressure',
                    'vin' => '/v2.0/vehicles/{vehicle_id}/vin'
                ]
            ],
            'high_mobility' => [
                'enabled' => config('services.high_mobility.enabled', false),
                'api_key' => config('services.high_mobility.api_key'),
                'base_url' => config('services.high_mobility.base_url', 'https://api.high-mobility.com'),
                'supported_brands' => [
                    'BMW', 'Audi', 'Mercedes-Benz', 'Porsche', 'Toyota', 'Volkswagen', 'Ford',
                    'Hyundai', 'Kia', 'Genesis', 'Nissan', 'Honda', 'Mazda', 'Subaru'
                ],
                'endpoints' => [
                    'vehicle_data' => '/v1/vehicles/{vehicle_id}/data',
                    'diagnostics' => '/v1/vehicles/{vehicle_id}/diagnostics',
                    'maintenance' => '/v1/vehicles/{vehicle_id}/maintenance',
                    'status' => '/v1/vehicles/{vehicle_id}/status',
                    'capabilities' => '/v1/vehicles/{vehicle_id}/capabilities'
                ]
            ],
            'otonomo' => [
                'enabled' => config('services.otonomo.enabled', false),
                'api_key' => config('services.otonomo.api_key'),
                'base_url' => config('services.otonomo.base_url', 'https://api.otonomo.io'),
                'supported_brands' => [
                    'BMW', 'Mercedes-Benz', 'Volkswagen', 'Audi', 'Ford', 'Toyota', 'Hyundai',
                    'Kia', 'Genesis', 'Nissan', 'Honda', 'Mazda', 'Subaru', 'Mitsubishi'
                ],
                'endpoints' => [
                    'fleet_data' => '/v1/fleet/data',
                    'vehicle_telemetry' => '/v1/vehicles/{vehicle_id}/telemetry',
                    'diagnostics' => '/v1/vehicles/{vehicle_id}/diagnostics',
                    'maintenance' => '/v1/vehicles/{vehicle_id}/maintenance',
                    'analytics' => '/v1/analytics'
                ]
            ],
            'wejo' => [
                'enabled' => config('services.wejo.enabled', false),
                'api_key' => config('services.wejo.api_key'),
                'base_url' => config('services.wejo.base_url', 'https://api.wejo.com'),
                'supported_brands' => [
                    'BMW', 'Mercedes-Benz', 'Volkswagen', 'Audi', 'Ford', 'Toyota', 'Hyundai',
                    'Kia', 'Genesis', 'Nissan', 'Honda', 'Mazda', 'Subaru', 'Mitsubishi',
                    'Jaguar', 'Land Rover', 'Mini', 'Fiat', 'Chrysler', 'Dodge', 'Jeep'
                ],
                'endpoints' => [
                    'connected_vehicles' => '/v1/connected-vehicles',
                    'vehicle_data' => '/v1/vehicles/{vehicle_id}/data',
                    'telemetry' => '/v1/vehicles/{vehicle_id}/telemetry',
                    'diagnostics' => '/v1/vehicles/{vehicle_id}/diagnostics',
                    'analytics' => '/v1/analytics'
                ]
            ],
            'motordata' => [
                'enabled' => config('services.motordata.enabled', false),
                'api_key' => config('services.motordata.api_key'),
                'base_url' => config('services.motordata.base_url', 'https://api.motordata.net'),
                'supported_brands' => [
                    'BMW', 'Mercedes-Benz', 'Volkswagen', 'Audi', 'Ford', 'Toyota', 'Hyundai',
                    'Kia', 'Genesis', 'Nissan', 'Honda', 'Mazda', 'Subaru', 'Mitsubishi',
                    'Jaguar', 'Land Rover', 'Mini', 'Fiat', 'Chrysler', 'Dodge', 'Jeep',
                    'Chevrolet', 'GMC', 'Cadillac', 'Buick', 'Lincoln', 'Tesla', 'Volvo'
                ],
                'endpoints' => [
                    'diagnostic_codes' => '/v1/diagnostic-codes',
                    'repair_info' => '/v1/repair-info',
                    'vehicle_specs' => '/v1/vehicle-specs',
                    'maintenance_schedule' => '/v1/maintenance-schedule',
                    'recall_info' => '/v1/recall-info'
                ]
            ],
            'carapi' => [
                'enabled' => config('services.carapi.enabled', false),
                'api_key' => config('services.carapi.api_key'),
                'base_url' => config('services.carapi.base_url', 'https://carapi.app/api'),
                'supported_brands' => [
                    'BMW', 'Mercedes-Benz', 'Volkswagen', 'Audi', 'Ford', 'Toyota', 'Hyundai',
                    'Kia', 'Genesis', 'Nissan', 'Honda', 'Mazda', 'Subaru', 'Mitsubishi',
                    'Jaguar', 'Land Rover', 'Mini', 'Fiat', 'Chrysler', 'Dodge', 'Jeep',
                    'Chevrolet', 'GMC', 'Cadillac', 'Buick', 'Lincoln', 'Tesla', 'Volvo',
                    'Porsche', 'Ferrari', 'Lamborghini', 'Maserati', 'Bentley', 'Rolls-Royce'
                ],
                'endpoints' => [
                    'makes' => '/makes',
                    'models' => '/models',
                    'years' => '/years',
                    'trims' => '/trims',
                    'engines' => '/engines',
                    'colors' => '/colors',
                    'body_styles' => '/body-styles',
                    'vehicle_data' => '/vehicle-data',
                    'diagnostics' => '/diagnostics',
                    'maintenance' => '/maintenance'
                ]
            ]
        ];
    }

    /**
     * Get vehicle data from Smartcar
     */
    public function getSmartcarVehicleData(string $vehicleId, string $accessToken): array
    {
        if (!$this->isPlatformEnabled('smartcar')) {
            return $this->getMockVehicleData('smartcar', $vehicleId);
        }

        $config = $this->platformConfigs['smartcar'];
        $endpoint = str_replace('{vehicle_id}', $vehicleId, $config['endpoints']['vehicle_data']);
        $url = $config['base_url'] . $endpoint;

        try {
            $response = Http::timeout($this->defaultTimeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->get($url);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Smartcar vehicle data retrieved successfully', [
                    'vehicle_id' => $vehicleId,
                    'endpoint' => $endpoint
                ]);

                return $this->normalizeSmartcarData($data);
            } else {
                Log::warning('Failed to retrieve Smartcar vehicle data', [
                    'vehicle_id' => $vehicleId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockVehicleData('smartcar', $vehicleId);
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving Smartcar vehicle data', [
                'vehicle_id' => $vehicleId,
                'error' => $e->getMessage()
            ]);

            return $this->getMockVehicleData('smartcar', $vehicleId);
        }
    }

    /**
     * Get vehicle data from High Mobility
     */
    public function getHighMobilityVehicleData(string $vehicleId): array
    {
        if (!$this->isPlatformEnabled('high_mobility')) {
            return $this->getMockVehicleData('high_mobility', $vehicleId);
        }

        $config = $this->platformConfigs['high_mobility'];
        $endpoint = str_replace('{vehicle_id}', $vehicleId, $config['endpoints']['vehicle_data']);
        $url = $config['base_url'] . $endpoint;

        try {
            $response = Http::timeout($this->defaultTimeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->get($url);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('High Mobility vehicle data retrieved successfully', [
                    'vehicle_id' => $vehicleId,
                    'endpoint' => $endpoint
                ]);

                return $this->normalizeHighMobilityData($data);
            } else {
                Log::warning('Failed to retrieve High Mobility vehicle data', [
                    'vehicle_id' => $vehicleId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockVehicleData('high_mobility', $vehicleId);
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving High Mobility vehicle data', [
                'vehicle_id' => $vehicleId,
                'error' => $e->getMessage()
            ]);

            return $this->getMockVehicleData('high_mobility', $vehicleId);
        }
    }

    /**
     * Get vehicle data from Otonomo
     */
    public function getOtonomoVehicleData(string $vehicleId): array
    {
        if (!$this->isPlatformEnabled('otonomo')) {
            return $this->getMockVehicleData('otonomo', $vehicleId);
        }

        $config = $this->platformConfigs['otonomo'];
        $endpoint = str_replace('{vehicle_id}', $vehicleId, $config['endpoints']['vehicle_telemetry']);
        $url = $config['base_url'] . $endpoint;

        try {
            $response = Http::timeout($this->defaultTimeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->get($url);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Otonomo vehicle data retrieved successfully', [
                    'vehicle_id' => $vehicleId,
                    'endpoint' => $endpoint
                ]);

                return $this->normalizeOtonomoData($data);
            } else {
                Log::warning('Failed to retrieve Otonomo vehicle data', [
                    'vehicle_id' => $vehicleId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockVehicleData('otonomo', $vehicleId);
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving Otonomo vehicle data', [
                'vehicle_id' => $vehicleId,
                'error' => $e->getMessage()
            ]);

            return $this->getMockVehicleData('otonomo', $vehicleId);
        }
    }

    /**
     * Get vehicle data from Wejo
     */
    public function getWejoVehicleData(string $vehicleId): array
    {
        if (!$this->isPlatformEnabled('wejo')) {
            return $this->getMockVehicleData('wejo', $vehicleId);
        }

        $config = $this->platformConfigs['wejo'];
        $endpoint = str_replace('{vehicle_id}', $vehicleId, $config['endpoints']['vehicle_data']);
        $url = $config['base_url'] . $endpoint;

        try {
            $response = Http::timeout($this->defaultTimeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->get($url);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Wejo vehicle data retrieved successfully', [
                    'vehicle_id' => $vehicleId,
                    'endpoint' => $endpoint
                ]);

                return $this->normalizeWejoData($data);
            } else {
                Log::warning('Failed to retrieve Wejo vehicle data', [
                    'vehicle_id' => $vehicleId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockVehicleData('wejo', $vehicleId);
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving Wejo vehicle data', [
                'vehicle_id' => $vehicleId,
                'error' => $e->getMessage()
            ]);

            return $this->getMockVehicleData('wejo', $vehicleId);
        }
    }

    /**
     * Get diagnostic data from MotorData
     */
    public function getMotorDataDiagnostics(string $vin, string $dtcCode = null): array
    {
        if (!$this->isPlatformEnabled('motordata')) {
            return $this->getMockDiagnostics('motordata', $vin);
        }

        $config = $this->platformConfigs['motordata'];
        $url = $config['base_url'] . $config['endpoints']['diagnostic_codes'];

        try {
            $params = ['vin' => $vin];
            if ($dtcCode) {
                $params['dtc_code'] = $dtcCode;
            }

            $response = Http::timeout($this->defaultTimeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->get($url, $params);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('MotorData diagnostics retrieved successfully', [
                    'vin' => $vin,
                    'dtc_code' => $dtcCode
                ]);

                return $this->normalizeMotorDataDiagnostics($data);
            } else {
                Log::warning('Failed to retrieve MotorData diagnostics', [
                    'vin' => $vin,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockDiagnostics('motordata', $vin);
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving MotorData diagnostics', [
                'vin' => $vin,
                'error' => $e->getMessage()
            ]);

            return $this->getMockDiagnostics('motordata', $vin);
        }
    }

    /**
     * Get vehicle data from CarAPI.app
     */
    public function getCarAPIVehicleData(string $make, string $model, string $year): array
    {
        if (!$this->isPlatformEnabled('carapi')) {
            return $this->getMockVehicleData('carapi', $make . $model . $year);
        }

        $config = $this->platformConfigs['carapi'];
        $url = $config['base_url'] . $config['endpoints']['vehicle_data'];

        try {
            $params = [
                'make' => $make,
                'model' => $model,
                'year' => $year
            ];

            $response = Http::timeout($this->defaultTimeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->get($url, $params);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('CarAPI vehicle data retrieved successfully', [
                    'make' => $make,
                    'model' => $model,
                    'year' => $year
                ]);

                return $this->normalizeCarAPIData($data);
            } else {
                Log::warning('Failed to retrieve CarAPI vehicle data', [
                    'make' => $make,
                    'model' => $model,
                    'year' => $year,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockVehicleData('carapi', $make . $model . $year);
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving CarAPI vehicle data', [
                'make' => $make,
                'model' => $model,
                'year' => $year,
                'error' => $e->getMessage()
            ]);

            return $this->getMockVehicleData('carapi', $make . $model . $year);
        }
    }

    /**
     * Get comprehensive vehicle data from all available platforms
     */
    public function getComprehensiveVehicleData(string $vehicleId, string $make = null, string $model = null, string $year = null): array
    {
        $comprehensiveData = [
            'vehicle_id' => $vehicleId,
            'make' => $make,
            'model' => $model,
            'year' => $year,
            'platforms' => [],
            'aggregated_data' => [],
            'last_updated' => now()->toISOString()
        ];

        // Try each platform
        foreach ($this->platformConfigs as $platform => $config) {
            if (!$config['enabled']) {
                continue;
            }

            try {
                switch ($platform) {
                    case 'smartcar':
                        // Smartcar requires OAuth token - skip for now
                        break;
                    case 'high_mobility':
                        $data = $this->getHighMobilityVehicleData($vehicleId);
                        $comprehensiveData['platforms']['high_mobility'] = $data;
                        break;
                    case 'otonomo':
                        $data = $this->getOtonomoVehicleData($vehicleId);
                        $comprehensiveData['platforms']['otonomo'] = $data;
                        break;
                    case 'wejo':
                        $data = $this->getWejoVehicleData($vehicleId);
                        $comprehensiveData['platforms']['wejo'] = $data;
                        break;
                    case 'motordata':
                        if ($vehicleId) {
                            $data = $this->getMotorDataDiagnostics($vehicleId);
                            $comprehensiveData['platforms']['motordata'] = $data;
                        }
                        break;
                    case 'carapi':
                        if ($make && $model && $year) {
                            $data = $this->getCarAPIVehicleData($make, $model, $year);
                            $comprehensiveData['platforms']['carapi'] = $data;
                        }
                        break;
                }
            } catch (\Exception $e) {
                Log::error("Error getting data from {$platform}", [
                    'vehicle_id' => $vehicleId,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Aggregate data from all platforms
        $comprehensiveData['aggregated_data'] = $this->aggregateVehicleData($comprehensiveData['platforms']);

        return $comprehensiveData;
    }

    /**
     * Check if platform is enabled
     */
    private function isPlatformEnabled(string $platform): bool
    {
        return isset($this->platformConfigs[$platform]) && 
               $this->platformConfigs[$platform]['enabled'] &&
               !empty($this->platformConfigs[$platform]['api_key'] ?? $this->platformConfigs[$platform]['client_id'] ?? null);
    }

    /**
     * Normalize Smartcar data
     */
    private function normalizeSmartcarData(array $data): array
    {
        return [
            'platform' => 'smartcar',
            'vehicle_id' => $data['id'] ?? '',
            'make' => $data['make'] ?? '',
            'model' => $data['model'] ?? '',
            'year' => $data['year'] ?? '',
            'vin' => $data['vin'] ?? '',
            'battery_level' => $data['battery']['percentRemaining'] ?? null,
            'fuel_level' => $data['fuel']['percentRemaining'] ?? null,
            'odometer' => $data['odometer']['distance'] ?? null,
            'location' => [
                'latitude' => $data['location']['latitude'] ?? null,
                'longitude' => $data['location']['longitude'] ?? null,
                'address' => $data['location']['address'] ?? null
            ],
            'tire_pressure' => $data['tires']['pressure'] ?? null,
            'last_updated' => now()->toISOString(),
            'data_source' => 'smartcar_api'
        ];
    }

    /**
     * Normalize High Mobility data
     */
    private function normalizeHighMobilityData(array $data): array
    {
        return [
            'platform' => 'high_mobility',
            'vehicle_id' => $data['vehicle_id'] ?? '',
            'make' => $data['make'] ?? '',
            'model' => $data['model'] ?? '',
            'year' => $data['year'] ?? '',
            'vin' => $data['vin'] ?? '',
            'engine' => $data['engine'] ?? [],
            'transmission' => $data['transmission'] ?? '',
            'fuel_type' => $data['fuel_type'] ?? '',
            'battery_level' => $data['battery_level'] ?? null,
            'fuel_level' => $data['fuel_level'] ?? null,
            'odometer' => $data['odometer'] ?? null,
            'diagnostics' => $data['diagnostics'] ?? [],
            'maintenance' => $data['maintenance'] ?? [],
            'last_updated' => now()->toISOString(),
            'data_source' => 'high_mobility_api'
        ];
    }

    /**
     * Normalize Otonomo data
     */
    private function normalizeOtonomoData(array $data): array
    {
        return [
            'platform' => 'otonomo',
            'vehicle_id' => $data['vehicle_id'] ?? '',
            'make' => $data['make'] ?? '',
            'model' => $data['model'] ?? '',
            'year' => $data['year'] ?? '',
            'vin' => $data['vin'] ?? '',
            'telemetry' => $data['telemetry'] ?? [],
            'diagnostics' => $data['diagnostics'] ?? [],
            'maintenance' => $data['maintenance'] ?? [],
            'analytics' => $data['analytics'] ?? [],
            'last_updated' => now()->toISOString(),
            'data_source' => 'otonomo_api'
        ];
    }

    /**
     * Normalize Wejo data
     */
    private function normalizeWejoData(array $data): array
    {
        return [
            'platform' => 'wejo',
            'vehicle_id' => $data['vehicle_id'] ?? '',
            'make' => $data['make'] ?? '',
            'model' => $data['model'] ?? '',
            'year' => $data['year'] ?? '',
            'vin' => $data['vin'] ?? '',
            'connected_data' => $data['connected_data'] ?? [],
            'telemetry' => $data['telemetry'] ?? [],
            'diagnostics' => $data['diagnostics'] ?? [],
            'analytics' => $data['analytics'] ?? [],
            'last_updated' => now()->toISOString(),
            'data_source' => 'wejo_api'
        ];
    }

    /**
     * Normalize MotorData diagnostics
     */
    private function normalizeMotorDataDiagnostics(array $data): array
    {
        return [
            'platform' => 'motordata',
            'vin' => $data['vin'] ?? '',
            'diagnostic_codes' => $data['diagnostic_codes'] ?? [],
            'repair_info' => $data['repair_info'] ?? [],
            'vehicle_specs' => $data['vehicle_specs'] ?? [],
            'maintenance_schedule' => $data['maintenance_schedule'] ?? [],
            'recall_info' => $data['recall_info'] ?? [],
            'last_updated' => now()->toISOString(),
            'data_source' => 'motordata_api'
        ];
    }

    /**
     * Normalize CarAPI data
     */
    private function normalizeCarAPIData(array $data): array
    {
        return [
            'platform' => 'carapi',
            'make' => $data['make'] ?? '',
            'model' => $data['model'] ?? '',
            'year' => $data['year'] ?? '',
            'vin' => $data['vin'] ?? '',
            'specifications' => $data['specifications'] ?? [],
            'diagnostics' => $data['diagnostics'] ?? [],
            'maintenance' => $data['maintenance'] ?? [],
            'last_updated' => now()->toISOString(),
            'data_source' => 'carapi_api'
        ];
    }

    /**
     * Aggregate vehicle data from multiple platforms
     */
    private function aggregateVehicleData(array $platformsData): array
    {
        $aggregated = [
            'make' => null,
            'model' => null,
            'year' => null,
            'vin' => null,
            'engine' => [],
            'transmission' => null,
            'fuel_type' => null,
            'battery_level' => null,
            'fuel_level' => null,
            'odometer' => null,
            'location' => [],
            'diagnostics' => [],
            'maintenance' => [],
            'data_sources' => []
        ];

        foreach ($platformsData as $platform => $data) {
            if (empty($data)) continue;

            // Aggregate basic vehicle info
            if (!$aggregated['make'] && !empty($data['make'])) {
                $aggregated['make'] = $data['make'];
            }
            if (!$aggregated['model'] && !empty($data['model'])) {
                $aggregated['model'] = $data['model'];
            }
            if (!$aggregated['year'] && !empty($data['year'])) {
                $aggregated['year'] = $data['year'];
            }
            if (!$aggregated['vin'] && !empty($data['vin'])) {
                $aggregated['vin'] = $data['vin'];
            }

            // Aggregate engine info
            if (!empty($data['engine'])) {
                $aggregated['engine'] = array_merge($aggregated['engine'], $data['engine']);
            }

            // Aggregate other data
            if (!$aggregated['transmission'] && !empty($data['transmission'])) {
                $aggregated['transmission'] = $data['transmission'];
            }
            if (!$aggregated['fuel_type'] && !empty($data['fuel_type'])) {
                $aggregated['fuel_type'] = $data['fuel_type'];
            }
            if (!$aggregated['battery_level'] && !empty($data['battery_level'])) {
                $aggregated['battery_level'] = $data['battery_level'];
            }
            if (!$aggregated['fuel_level'] && !empty($data['fuel_level'])) {
                $aggregated['fuel_level'] = $data['fuel_level'];
            }
            if (!$aggregated['odometer'] && !empty($data['odometer'])) {
                $aggregated['odometer'] = $data['odometer'];
            }

            // Aggregate location
            if (!empty($data['location'])) {
                $aggregated['location'] = array_merge($aggregated['location'], $data['location']);
            }

            // Aggregate diagnostics
            if (!empty($data['diagnostics'])) {
                $aggregated['diagnostics'] = array_merge($aggregated['diagnostics'], $data['diagnostics']);
            }

            // Aggregate maintenance
            if (!empty($data['maintenance'])) {
                $aggregated['maintenance'] = array_merge($aggregated['maintenance'], $data['maintenance']);
            }

            // Track data sources
            $aggregated['data_sources'][] = $data['data_source'] ?? $platform;
        }

        return $aggregated;
    }

    /**
     * Get mock vehicle data for testing
     */
    private function getMockVehicleData(string $platform, string $vehicleId): array
    {
        return [
            'platform' => $platform,
            'vehicle_id' => $vehicleId,
            'make' => 'Sample',
            'model' => 'Vehicle',
            'year' => 2023,
            'vin' => 'SAMPLE123456789012',
            'engine' => [
                'type' => 'Gasoline',
                'size' => '2.0L',
                'power' => '200 HP'
            ],
            'transmission' => 'Automatic',
            'fuel_type' => 'Gasoline',
            'battery_level' => 85,
            'fuel_level' => 75,
            'odometer' => 25000,
            'location' => [
                'latitude' => 42.6629,
                'longitude' => 21.1655,
                'address' => 'Pristina, Kosovo'
            ],
            'diagnostics' => [],
            'maintenance' => [],
            'last_updated' => now()->toISOString(),
            'data_source' => 'mock_data'
        ];
    }

    /**
     * Get mock diagnostics for testing
     */
    private function getMockDiagnostics(string $platform, string $vin): array
    {
        return [
            'platform' => $platform,
            'vin' => $vin,
            'diagnostic_codes' => [],
            'repair_info' => [],
            'vehicle_specs' => [],
            'maintenance_schedule' => [],
            'recall_info' => [],
            'last_updated' => now()->toISOString(),
            'data_source' => 'mock_data'
        ];
    }

    /**
     * Get supported platforms
     */
    public function getSupportedPlatforms(): array
    {
        return array_keys($this->platformConfigs);
    }

    /**
     * Get platform configuration
     */
    public function getPlatformConfig(string $platform): array
    {
        return $this->platformConfigs[$platform] ?? [];
    }

    /**
     * Get service status
     */
    public function getStatus(): array
    {
        $status = [];
        
        foreach ($this->platformConfigs as $platform => $config) {
            $status[$platform] = [
                'enabled' => $config['enabled'],
                'configured' => !empty($config['api_key'] ?? $config['client_id'] ?? null),
                'base_url' => $config['base_url'],
                'supported_brands' => $config['supported_brands']
            ];
        }

        return $status;
    }

    /**
     * Test platform API connection
     */
    public function testPlatformAPI(string $platform): bool
    {
        if (!$this->isPlatformEnabled($platform)) {
            return false;
        }

        $config = $this->platformConfigs[$platform];
        $testUrl = $config['base_url'] . '/health'; // Most APIs have a health endpoint

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . ($config['api_key'] ?? $config['client_id'] ?? ''),
                    'Accept' => 'application/json'
                ])
                ->get($testUrl);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Platform API test failed', [
                'platform' => $platform,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MultiBrandPlatformAPIService
{
    private $platformConfigs;
    private $defaultTimeout = 30;

    public function __construct()
    {
        $this->platformConfigs = [
            'smartcar' => [
                'enabled' => config('services.smartcar.enabled', false),
                'client_id' => config('services.smartcar.client_id'),
                'client_secret' => config('services.smartcar.client_secret'),
                'redirect_uri' => config('services.smartcar.redirect_uri'),
                'base_url' => config('services.smartcar.base_url', 'https://api.smartcar.com'),
                'supported_brands' => [
                    'BMW', 'Ford', 'Volkswagen', 'Tesla', 'Toyota', 'Hyundai', 'Kia', 'Genesis',
                    'Mercedes-Benz', 'Audi', 'Porsche', 'Volvo', 'Nissan', 'Honda', 'Mazda',
                    'Subaru', 'Mitsubishi', 'Jaguar', 'Land Rover', 'Mini', 'Fiat', 'Chrysler',
                    'Dodge', 'Jeep', 'Ram', 'Chevrolet', 'GMC', 'Cadillac', 'Buick', 'Lincoln'
                ],
                'endpoints' => [
                    'auth' => '/v2.0/oauth/authorize',
                    'token' => '/v2.0/oauth/token',
                    'vehicles' => '/v2.0/vehicles',
                    'vehicle_data' => '/v2.0/vehicles/{vehicle_id}',
                    'battery' => '/v2.0/vehicles/{vehicle_id}/battery',
                    'charge' => '/v2.0/vehicles/{vehicle_id}/charge',
                    'engine_oil' => '/v2.0/vehicles/{vehicle_id}/engine/oil',
                    'fuel' => '/v2.0/vehicles/{vehicle_id}/fuel',
                    'location' => '/v2.0/vehicles/{vehicle_id}/location',
                    'odometer' => '/v2.0/vehicles/{vehicle_id}/odometer',
                    'tire_pressure' => '/v2.0/vehicles/{vehicle_id}/tires/pressure',
                    'vin' => '/v2.0/vehicles/{vehicle_id}/vin'
                ]
            ],
            'high_mobility' => [
                'enabled' => config('services.high_mobility.enabled', false),
                'api_key' => config('services.high_mobility.api_key'),
                'base_url' => config('services.high_mobility.base_url', 'https://api.high-mobility.com'),
                'supported_brands' => [
                    'BMW', 'Audi', 'Mercedes-Benz', 'Porsche', 'Toyota', 'Volkswagen', 'Ford',
                    'Hyundai', 'Kia', 'Genesis', 'Nissan', 'Honda', 'Mazda', 'Subaru'
                ],
                'endpoints' => [
                    'vehicle_data' => '/v1/vehicles/{vehicle_id}/data',
                    'diagnostics' => '/v1/vehicles/{vehicle_id}/diagnostics',
                    'maintenance' => '/v1/vehicles/{vehicle_id}/maintenance',
                    'status' => '/v1/vehicles/{vehicle_id}/status',
                    'capabilities' => '/v1/vehicles/{vehicle_id}/capabilities'
                ]
            ],
            'otonomo' => [
                'enabled' => config('services.otonomo.enabled', false),
                'api_key' => config('services.otonomo.api_key'),
                'base_url' => config('services.otonomo.base_url', 'https://api.otonomo.io'),
                'supported_brands' => [
                    'BMW', 'Mercedes-Benz', 'Volkswagen', 'Audi', 'Ford', 'Toyota', 'Hyundai',
                    'Kia', 'Genesis', 'Nissan', 'Honda', 'Mazda', 'Subaru', 'Mitsubishi'
                ],
                'endpoints' => [
                    'fleet_data' => '/v1/fleet/data',
                    'vehicle_telemetry' => '/v1/vehicles/{vehicle_id}/telemetry',
                    'diagnostics' => '/v1/vehicles/{vehicle_id}/diagnostics',
                    'maintenance' => '/v1/vehicles/{vehicle_id}/maintenance',
                    'analytics' => '/v1/analytics'
                ]
            ],
            'wejo' => [
                'enabled' => config('services.wejo.enabled', false),
                'api_key' => config('services.wejo.api_key'),
                'base_url' => config('services.wejo.base_url', 'https://api.wejo.com'),
                'supported_brands' => [
                    'BMW', 'Mercedes-Benz', 'Volkswagen', 'Audi', 'Ford', 'Toyota', 'Hyundai',
                    'Kia', 'Genesis', 'Nissan', 'Honda', 'Mazda', 'Subaru', 'Mitsubishi',
                    'Jaguar', 'Land Rover', 'Mini', 'Fiat', 'Chrysler', 'Dodge', 'Jeep'
                ],
                'endpoints' => [
                    'connected_vehicles' => '/v1/connected-vehicles',
                    'vehicle_data' => '/v1/vehicles/{vehicle_id}/data',
                    'telemetry' => '/v1/vehicles/{vehicle_id}/telemetry',
                    'diagnostics' => '/v1/vehicles/{vehicle_id}/diagnostics',
                    'analytics' => '/v1/analytics'
                ]
            ],
            'motordata' => [
                'enabled' => config('services.motordata.enabled', false),
                'api_key' => config('services.motordata.api_key'),
                'base_url' => config('services.motordata.base_url', 'https://api.motordata.net'),
                'supported_brands' => [
                    'BMW', 'Mercedes-Benz', 'Volkswagen', 'Audi', 'Ford', 'Toyota', 'Hyundai',
                    'Kia', 'Genesis', 'Nissan', 'Honda', 'Mazda', 'Subaru', 'Mitsubishi',
                    'Jaguar', 'Land Rover', 'Mini', 'Fiat', 'Chrysler', 'Dodge', 'Jeep',
                    'Chevrolet', 'GMC', 'Cadillac', 'Buick', 'Lincoln', 'Tesla', 'Volvo'
                ],
                'endpoints' => [
                    'diagnostic_codes' => '/v1/diagnostic-codes',
                    'repair_info' => '/v1/repair-info',
                    'vehicle_specs' => '/v1/vehicle-specs',
                    'maintenance_schedule' => '/v1/maintenance-schedule',
                    'recall_info' => '/v1/recall-info'
                ]
            ],
            'carapi' => [
                'enabled' => config('services.carapi.enabled', false),
                'api_key' => config('services.carapi.api_key'),
                'base_url' => config('services.carapi.base_url', 'https://carapi.app/api'),
                'supported_brands' => [
                    'BMW', 'Mercedes-Benz', 'Volkswagen', 'Audi', 'Ford', 'Toyota', 'Hyundai',
                    'Kia', 'Genesis', 'Nissan', 'Honda', 'Mazda', 'Subaru', 'Mitsubishi',
                    'Jaguar', 'Land Rover', 'Mini', 'Fiat', 'Chrysler', 'Dodge', 'Jeep',
                    'Chevrolet', 'GMC', 'Cadillac', 'Buick', 'Lincoln', 'Tesla', 'Volvo',
                    'Porsche', 'Ferrari', 'Lamborghini', 'Maserati', 'Bentley', 'Rolls-Royce'
                ],
                'endpoints' => [
                    'makes' => '/makes',
                    'models' => '/models',
                    'years' => '/years',
                    'trims' => '/trims',
                    'engines' => '/engines',
                    'colors' => '/colors',
                    'body_styles' => '/body-styles',
                    'vehicle_data' => '/vehicle-data',
                    'diagnostics' => '/diagnostics',
                    'maintenance' => '/maintenance'
                ]
            ]
        ];
    }

    /**
     * Get vehicle data from Smartcar
     */
    public function getSmartcarVehicleData(string $vehicleId, string $accessToken): array
    {
        if (!$this->isPlatformEnabled('smartcar')) {
            return $this->getMockVehicleData('smartcar', $vehicleId);
        }

        $config = $this->platformConfigs['smartcar'];
        $endpoint = str_replace('{vehicle_id}', $vehicleId, $config['endpoints']['vehicle_data']);
        $url = $config['base_url'] . $endpoint;

        try {
            $response = Http::timeout($this->defaultTimeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->get($url);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Smartcar vehicle data retrieved successfully', [
                    'vehicle_id' => $vehicleId,
                    'endpoint' => $endpoint
                ]);

                return $this->normalizeSmartcarData($data);
            } else {
                Log::warning('Failed to retrieve Smartcar vehicle data', [
                    'vehicle_id' => $vehicleId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockVehicleData('smartcar', $vehicleId);
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving Smartcar vehicle data', [
                'vehicle_id' => $vehicleId,
                'error' => $e->getMessage()
            ]);

            return $this->getMockVehicleData('smartcar', $vehicleId);
        }
    }

    /**
     * Get vehicle data from High Mobility
     */
    public function getHighMobilityVehicleData(string $vehicleId): array
    {
        if (!$this->isPlatformEnabled('high_mobility')) {
            return $this->getMockVehicleData('high_mobility', $vehicleId);
        }

        $config = $this->platformConfigs['high_mobility'];
        $endpoint = str_replace('{vehicle_id}', $vehicleId, $config['endpoints']['vehicle_data']);
        $url = $config['base_url'] . $endpoint;

        try {
            $response = Http::timeout($this->defaultTimeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->get($url);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('High Mobility vehicle data retrieved successfully', [
                    'vehicle_id' => $vehicleId,
                    'endpoint' => $endpoint
                ]);

                return $this->normalizeHighMobilityData($data);
            } else {
                Log::warning('Failed to retrieve High Mobility vehicle data', [
                    'vehicle_id' => $vehicleId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockVehicleData('high_mobility', $vehicleId);
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving High Mobility vehicle data', [
                'vehicle_id' => $vehicleId,
                'error' => $e->getMessage()
            ]);

            return $this->getMockVehicleData('high_mobility', $vehicleId);
        }
    }

    /**
     * Get vehicle data from Otonomo
     */
    public function getOtonomoVehicleData(string $vehicleId): array
    {
        if (!$this->isPlatformEnabled('otonomo')) {
            return $this->getMockVehicleData('otonomo', $vehicleId);
        }

        $config = $this->platformConfigs['otonomo'];
        $endpoint = str_replace('{vehicle_id}', $vehicleId, $config['endpoints']['vehicle_telemetry']);
        $url = $config['base_url'] . $endpoint;

        try {
            $response = Http::timeout($this->defaultTimeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->get($url);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Otonomo vehicle data retrieved successfully', [
                    'vehicle_id' => $vehicleId,
                    'endpoint' => $endpoint
                ]);

                return $this->normalizeOtonomoData($data);
            } else {
                Log::warning('Failed to retrieve Otonomo vehicle data', [
                    'vehicle_id' => $vehicleId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockVehicleData('otonomo', $vehicleId);
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving Otonomo vehicle data', [
                'vehicle_id' => $vehicleId,
                'error' => $e->getMessage()
            ]);

            return $this->getMockVehicleData('otonomo', $vehicleId);
        }
    }

    /**
     * Get vehicle data from Wejo
     */
    public function getWejoVehicleData(string $vehicleId): array
    {
        if (!$this->isPlatformEnabled('wejo')) {
            return $this->getMockVehicleData('wejo', $vehicleId);
        }

        $config = $this->platformConfigs['wejo'];
        $endpoint = str_replace('{vehicle_id}', $vehicleId, $config['endpoints']['vehicle_data']);
        $url = $config['base_url'] . $endpoint;

        try {
            $response = Http::timeout($this->defaultTimeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->get($url);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Wejo vehicle data retrieved successfully', [
                    'vehicle_id' => $vehicleId,
                    'endpoint' => $endpoint
                ]);

                return $this->normalizeWejoData($data);
            } else {
                Log::warning('Failed to retrieve Wejo vehicle data', [
                    'vehicle_id' => $vehicleId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockVehicleData('wejo', $vehicleId);
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving Wejo vehicle data', [
                'vehicle_id' => $vehicleId,
                'error' => $e->getMessage()
            ]);

            return $this->getMockVehicleData('wejo', $vehicleId);
        }
    }

    /**
     * Get diagnostic data from MotorData
     */
    public function getMotorDataDiagnostics(string $vin, string $dtcCode = null): array
    {
        if (!$this->isPlatformEnabled('motordata')) {
            return $this->getMockDiagnostics('motordata', $vin);
        }

        $config = $this->platformConfigs['motordata'];
        $url = $config['base_url'] . $config['endpoints']['diagnostic_codes'];

        try {
            $params = ['vin' => $vin];
            if ($dtcCode) {
                $params['dtc_code'] = $dtcCode;
            }

            $response = Http::timeout($this->defaultTimeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->get($url, $params);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('MotorData diagnostics retrieved successfully', [
                    'vin' => $vin,
                    'dtc_code' => $dtcCode
                ]);

                return $this->normalizeMotorDataDiagnostics($data);
            } else {
                Log::warning('Failed to retrieve MotorData diagnostics', [
                    'vin' => $vin,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockDiagnostics('motordata', $vin);
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving MotorData diagnostics', [
                'vin' => $vin,
                'error' => $e->getMessage()
            ]);

            return $this->getMockDiagnostics('motordata', $vin);
        }
    }

    /**
     * Get vehicle data from CarAPI.app
     */
    public function getCarAPIVehicleData(string $make, string $model, string $year): array
    {
        if (!$this->isPlatformEnabled('carapi')) {
            return $this->getMockVehicleData('carapi', $make . $model . $year);
        }

        $config = $this->platformConfigs['carapi'];
        $url = $config['base_url'] . $config['endpoints']['vehicle_data'];

        try {
            $params = [
                'make' => $make,
                'model' => $model,
                'year' => $year
            ];

            $response = Http::timeout($this->defaultTimeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->get($url, $params);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('CarAPI vehicle data retrieved successfully', [
                    'make' => $make,
                    'model' => $model,
                    'year' => $year
                ]);

                return $this->normalizeCarAPIData($data);
            } else {
                Log::warning('Failed to retrieve CarAPI vehicle data', [
                    'make' => $make,
                    'model' => $model,
                    'year' => $year,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockVehicleData('carapi', $make . $model . $year);
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving CarAPI vehicle data', [
                'make' => $make,
                'model' => $model,
                'year' => $year,
                'error' => $e->getMessage()
            ]);

            return $this->getMockVehicleData('carapi', $make . $model . $year);
        }
    }

    /**
     * Get comprehensive vehicle data from all available platforms
     */
    public function getComprehensiveVehicleData(string $vehicleId, string $make = null, string $model = null, string $year = null): array
    {
        $comprehensiveData = [
            'vehicle_id' => $vehicleId,
            'make' => $make,
            'model' => $model,
            'year' => $year,
            'platforms' => [],
            'aggregated_data' => [],
            'last_updated' => now()->toISOString()
        ];

        // Try each platform
        foreach ($this->platformConfigs as $platform => $config) {
            if (!$config['enabled']) {
                continue;
            }

            try {
                switch ($platform) {
                    case 'smartcar':
                        // Smartcar requires OAuth token - skip for now
                        break;
                    case 'high_mobility':
                        $data = $this->getHighMobilityVehicleData($vehicleId);
                        $comprehensiveData['platforms']['high_mobility'] = $data;
                        break;
                    case 'otonomo':
                        $data = $this->getOtonomoVehicleData($vehicleId);
                        $comprehensiveData['platforms']['otonomo'] = $data;
                        break;
                    case 'wejo':
                        $data = $this->getWejoVehicleData($vehicleId);
                        $comprehensiveData['platforms']['wejo'] = $data;
                        break;
                    case 'motordata':
                        if ($vehicleId) {
                            $data = $this->getMotorDataDiagnostics($vehicleId);
                            $comprehensiveData['platforms']['motordata'] = $data;
                        }
                        break;
                    case 'carapi':
                        if ($make && $model && $year) {
                            $data = $this->getCarAPIVehicleData($make, $model, $year);
                            $comprehensiveData['platforms']['carapi'] = $data;
                        }
                        break;
                }
            } catch (\Exception $e) {
                Log::error("Error getting data from {$platform}", [
                    'vehicle_id' => $vehicleId,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Aggregate data from all platforms
        $comprehensiveData['aggregated_data'] = $this->aggregateVehicleData($comprehensiveData['platforms']);

        return $comprehensiveData;
    }

    /**
     * Check if platform is enabled
     */
    private function isPlatformEnabled(string $platform): bool
    {
        return isset($this->platformConfigs[$platform]) && 
               $this->platformConfigs[$platform]['enabled'] &&
               !empty($this->platformConfigs[$platform]['api_key'] ?? $this->platformConfigs[$platform]['client_id'] ?? null);
    }

    /**
     * Normalize Smartcar data
     */
    private function normalizeSmartcarData(array $data): array
    {
        return [
            'platform' => 'smartcar',
            'vehicle_id' => $data['id'] ?? '',
            'make' => $data['make'] ?? '',
            'model' => $data['model'] ?? '',
            'year' => $data['year'] ?? '',
            'vin' => $data['vin'] ?? '',
            'battery_level' => $data['battery']['percentRemaining'] ?? null,
            'fuel_level' => $data['fuel']['percentRemaining'] ?? null,
            'odometer' => $data['odometer']['distance'] ?? null,
            'location' => [
                'latitude' => $data['location']['latitude'] ?? null,
                'longitude' => $data['location']['longitude'] ?? null,
                'address' => $data['location']['address'] ?? null
            ],
            'tire_pressure' => $data['tires']['pressure'] ?? null,
            'last_updated' => now()->toISOString(),
            'data_source' => 'smartcar_api'
        ];
    }

    /**
     * Normalize High Mobility data
     */
    private function normalizeHighMobilityData(array $data): array
    {
        return [
            'platform' => 'high_mobility',
            'vehicle_id' => $data['vehicle_id'] ?? '',
            'make' => $data['make'] ?? '',
            'model' => $data['model'] ?? '',
            'year' => $data['year'] ?? '',
            'vin' => $data['vin'] ?? '',
            'engine' => $data['engine'] ?? [],
            'transmission' => $data['transmission'] ?? '',
            'fuel_type' => $data['fuel_type'] ?? '',
            'battery_level' => $data['battery_level'] ?? null,
            'fuel_level' => $data['fuel_level'] ?? null,
            'odometer' => $data['odometer'] ?? null,
            'diagnostics' => $data['diagnostics'] ?? [],
            'maintenance' => $data['maintenance'] ?? [],
            'last_updated' => now()->toISOString(),
            'data_source' => 'high_mobility_api'
        ];
    }

    /**
     * Normalize Otonomo data
     */
    private function normalizeOtonomoData(array $data): array
    {
        return [
            'platform' => 'otonomo',
            'vehicle_id' => $data['vehicle_id'] ?? '',
            'make' => $data['make'] ?? '',
            'model' => $data['model'] ?? '',
            'year' => $data['year'] ?? '',
            'vin' => $data['vin'] ?? '',
            'telemetry' => $data['telemetry'] ?? [],
            'diagnostics' => $data['diagnostics'] ?? [],
            'maintenance' => $data['maintenance'] ?? [],
            'analytics' => $data['analytics'] ?? [],
            'last_updated' => now()->toISOString(),
            'data_source' => 'otonomo_api'
        ];
    }

    /**
     * Normalize Wejo data
     */
    private function normalizeWejoData(array $data): array
    {
        return [
            'platform' => 'wejo',
            'vehicle_id' => $data['vehicle_id'] ?? '',
            'make' => $data['make'] ?? '',
            'model' => $data['model'] ?? '',
            'year' => $data['year'] ?? '',
            'vin' => $data['vin'] ?? '',
            'connected_data' => $data['connected_data'] ?? [],
            'telemetry' => $data['telemetry'] ?? [],
            'diagnostics' => $data['diagnostics'] ?? [],
            'analytics' => $data['analytics'] ?? [],
            'last_updated' => now()->toISOString(),
            'data_source' => 'wejo_api'
        ];
    }

    /**
     * Normalize MotorData diagnostics
     */
    private function normalizeMotorDataDiagnostics(array $data): array
    {
        return [
            'platform' => 'motordata',
            'vin' => $data['vin'] ?? '',
            'diagnostic_codes' => $data['diagnostic_codes'] ?? [],
            'repair_info' => $data['repair_info'] ?? [],
            'vehicle_specs' => $data['vehicle_specs'] ?? [],
            'maintenance_schedule' => $data['maintenance_schedule'] ?? [],
            'recall_info' => $data['recall_info'] ?? [],
            'last_updated' => now()->toISOString(),
            'data_source' => 'motordata_api'
        ];
    }

    /**
     * Normalize CarAPI data
     */
    private function normalizeCarAPIData(array $data): array
    {
        return [
            'platform' => 'carapi',
            'make' => $data['make'] ?? '',
            'model' => $data['model'] ?? '',
            'year' => $data['year'] ?? '',
            'vin' => $data['vin'] ?? '',
            'specifications' => $data['specifications'] ?? [],
            'diagnostics' => $data['diagnostics'] ?? [],
            'maintenance' => $data['maintenance'] ?? [],
            'last_updated' => now()->toISOString(),
            'data_source' => 'carapi_api'
        ];
    }

    /**
     * Aggregate vehicle data from multiple platforms
     */
    private function aggregateVehicleData(array $platformsData): array
    {
        $aggregated = [
            'make' => null,
            'model' => null,
            'year' => null,
            'vin' => null,
            'engine' => [],
            'transmission' => null,
            'fuel_type' => null,
            'battery_level' => null,
            'fuel_level' => null,
            'odometer' => null,
            'location' => [],
            'diagnostics' => [],
            'maintenance' => [],
            'data_sources' => []
        ];

        foreach ($platformsData as $platform => $data) {
            if (empty($data)) continue;

            // Aggregate basic vehicle info
            if (!$aggregated['make'] && !empty($data['make'])) {
                $aggregated['make'] = $data['make'];
            }
            if (!$aggregated['model'] && !empty($data['model'])) {
                $aggregated['model'] = $data['model'];
            }
            if (!$aggregated['year'] && !empty($data['year'])) {
                $aggregated['year'] = $data['year'];
            }
            if (!$aggregated['vin'] && !empty($data['vin'])) {
                $aggregated['vin'] = $data['vin'];
            }

            // Aggregate engine info
            if (!empty($data['engine'])) {
                $aggregated['engine'] = array_merge($aggregated['engine'], $data['engine']);
            }

            // Aggregate other data
            if (!$aggregated['transmission'] && !empty($data['transmission'])) {
                $aggregated['transmission'] = $data['transmission'];
            }
            if (!$aggregated['fuel_type'] && !empty($data['fuel_type'])) {
                $aggregated['fuel_type'] = $data['fuel_type'];
            }
            if (!$aggregated['battery_level'] && !empty($data['battery_level'])) {
                $aggregated['battery_level'] = $data['battery_level'];
            }
            if (!$aggregated['fuel_level'] && !empty($data['fuel_level'])) {
                $aggregated['fuel_level'] = $data['fuel_level'];
            }
            if (!$aggregated['odometer'] && !empty($data['odometer'])) {
                $aggregated['odometer'] = $data['odometer'];
            }

            // Aggregate location
            if (!empty($data['location'])) {
                $aggregated['location'] = array_merge($aggregated['location'], $data['location']);
            }

            // Aggregate diagnostics
            if (!empty($data['diagnostics'])) {
                $aggregated['diagnostics'] = array_merge($aggregated['diagnostics'], $data['diagnostics']);
            }

            // Aggregate maintenance
            if (!empty($data['maintenance'])) {
                $aggregated['maintenance'] = array_merge($aggregated['maintenance'], $data['maintenance']);
            }

            // Track data sources
            $aggregated['data_sources'][] = $data['data_source'] ?? $platform;
        }

        return $aggregated;
    }

    /**
     * Get mock vehicle data for testing
     */
    private function getMockVehicleData(string $platform, string $vehicleId): array
    {
        return [
            'platform' => $platform,
            'vehicle_id' => $vehicleId,
            'make' => 'Sample',
            'model' => 'Vehicle',
            'year' => 2023,
            'vin' => 'SAMPLE123456789012',
            'engine' => [
                'type' => 'Gasoline',
                'size' => '2.0L',
                'power' => '200 HP'
            ],
            'transmission' => 'Automatic',
            'fuel_type' => 'Gasoline',
            'battery_level' => 85,
            'fuel_level' => 75,
            'odometer' => 25000,
            'location' => [
                'latitude' => 42.6629,
                'longitude' => 21.1655,
                'address' => 'Pristina, Kosovo'
            ],
            'diagnostics' => [],
            'maintenance' => [],
            'last_updated' => now()->toISOString(),
            'data_source' => 'mock_data'
        ];
    }

    /**
     * Get mock diagnostics for testing
     */
    private function getMockDiagnostics(string $platform, string $vin): array
    {
        return [
            'platform' => $platform,
            'vin' => $vin,
            'diagnostic_codes' => [],
            'repair_info' => [],
            'vehicle_specs' => [],
            'maintenance_schedule' => [],
            'recall_info' => [],
            'last_updated' => now()->toISOString(),
            'data_source' => 'mock_data'
        ];
    }

    /**
     * Get supported platforms
     */
    public function getSupportedPlatforms(): array
    {
        return array_keys($this->platformConfigs);
    }

    /**
     * Get platform configuration
     */
    public function getPlatformConfig(string $platform): array
    {
        return $this->platformConfigs[$platform] ?? [];
    }

    /**
     * Get service status
     */
    public function getStatus(): array
    {
        $status = [];
        
        foreach ($this->platformConfigs as $platform => $config) {
            $status[$platform] = [
                'enabled' => $config['enabled'],
                'configured' => !empty($config['api_key'] ?? $config['client_id'] ?? null),
                'base_url' => $config['base_url'],
                'supported_brands' => $config['supported_brands']
            ];
        }

        return $status;
    }

    /**
     * Test platform API connection
     */
    public function testPlatformAPI(string $platform): bool
    {
        if (!$this->isPlatformEnabled($platform)) {
            return false;
        }

        $config = $this->platformConfigs[$platform];
        $testUrl = $config['base_url'] . '/health'; // Most APIs have a health endpoint

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . ($config['api_key'] ?? $config['client_id'] ?? ''),
                    'Accept' => 'application/json'
                ])
                ->get($testUrl);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Platform API test failed', [
                'platform' => $platform,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}














