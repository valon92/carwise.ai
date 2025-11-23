<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CarManufacturerAPIService
{
    private $manufacturerConfigs;
    private $defaultTimeout = 30;

    public function __construct()
    {
        $this->manufacturerConfigs = [
            'bmw' => [
                'enabled' => config('services.bmw.enabled', false),
                'api_key' => config('services.bmw.api_key'),
                'base_url' => config('services.bmw.base_url', 'https://api.bmw.com'),
                'endpoints' => [
                    'vehicle_data' => '/v1/vehicles/{vin}/data',
                    'diagnostics' => '/v1/vehicles/{vin}/diagnostics',
                    'maintenance' => '/v1/vehicles/{vin}/maintenance',
                    'status' => '/v1/vehicles/{vin}/status'
                ]
            ],
            'mercedes' => [
                'enabled' => config('services.mercedes.enabled', false),
                'api_key' => config('services.mercedes.api_key'),
                'base_url' => config('services.mercedes.base_url', 'https://api.mercedes-benz.com'),
                'endpoints' => [
                    'vehicle_data' => '/vehicledata/v1/vehicles/{vin}',
                    'diagnostics' => '/vehicledata/v1/vehicles/{vin}/diagnostics',
                    'maintenance' => '/vehicledata/v1/vehicles/{vin}/maintenance',
                    'status' => '/vehicledata/v1/vehicles/{vin}/status'
                ]
            ],
            'volkswagen' => [
                'enabled' => config('services.volkswagen.enabled', false),
                'api_key' => config('services.volkswagen.api_key'),
                'base_url' => config('services.volkswagen.base_url', 'https://api.volkswagen.com'),
                'endpoints' => [
                    'vehicle_data' => '/v1/vehicles/{vin}/data',
                    'diagnostics' => '/v1/vehicles/{vin}/diagnostics',
                    'maintenance' => '/v1/vehicles/{vin}/maintenance',
                    'status' => '/v1/vehicles/{vin}/status'
                ]
            ],
            'audi' => [
                'enabled' => config('services.audi.enabled', false),
                'api_key' => config('services.audi.api_key'),
                'base_url' => config('services.audi.base_url', 'https://api.audi.com'),
                'endpoints' => [
                    'vehicle_data' => '/v1/vehicles/{vin}/data',
                    'diagnostics' => '/v1/vehicles/{vin}/diagnostics',
                    'maintenance' => '/v1/vehicles/{vin}/maintenance',
                    'status' => '/v1/vehicles/{vin}/status'
                ]
            ],
            'ford' => [
                'enabled' => config('services.ford.enabled', false),
                'api_key' => config('services.ford.api_key'),
                'base_url' => config('services.ford.base_url', 'https://api.ford.com'),
                'endpoints' => [
                    'vehicle_data' => '/v1/vehicles/{vin}/data',
                    'diagnostics' => '/v1/vehicles/{vin}/diagnostics',
                    'maintenance' => '/v1/vehicles/{vin}/maintenance',
                    'status' => '/v1/vehicles/{vin}/status'
                ]
            ],
            'toyota' => [
                'enabled' => config('services.toyota.enabled', false),
                'api_key' => config('services.toyota.api_key'),
                'base_url' => config('services.toyota.base_url', 'https://api.toyota.com'),
                'endpoints' => [
                    'vehicle_data' => '/v1/vehicles/{vin}/data',
                    'diagnostics' => '/v1/vehicles/{vin}/diagnostics',
                    'maintenance' => '/v1/vehicles/{vin}/maintenance',
                    'status' => '/v1/vehicles/{vin}/status'
                ]
            ],
            'volvo' => [
                'enabled' => config('services.volvo.enabled', false),
                'api_key' => config('services.volvo.api_key'),
                'base_url' => config('services.volvo.base_url', 'https://api.volvocars.com'),
                'endpoints' => [
                    'vehicle_data' => '/v1/vehicles/{vin}/data',
                    'diagnostics' => '/v1/vehicles/{vin}/diagnostics',
                    'maintenance' => '/v1/vehicles/{vin}/maintenance',
                    'status' => '/v1/vehicles/{vin}/status'
                ]
            ],
            'tesla' => [
                'enabled' => config('services.tesla.enabled', false),
                'api_key' => config('services.tesla.api_key'),
                'base_url' => config('services.tesla.base_url', 'https://owner-api.teslamotors.com'),
                'endpoints' => [
                    'vehicle_data' => '/api/1/vehicles/{vin}/data',
                    'diagnostics' => '/api/1/vehicles/{vin}/diagnostics',
                    'maintenance' => '/api/1/vehicles/{vin}/maintenance',
                    'status' => '/api/1/vehicles/{vin}/status'
                ]
            ]
        ];
    }

    /**
     * Get vehicle data from manufacturer API
     */
    public function getVehicleData(string $manufacturer, string $vin): array
    {
        if (!$this->isManufacturerEnabled($manufacturer)) {
            return $this->getMockVehicleData($manufacturer, $vin);
        }

        $config = $this->manufacturerConfigs[$manufacturer];
        $endpoint = str_replace('{vin}', $vin, $config['endpoints']['vehicle_data']);
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
                
                Log::info('Vehicle data retrieved successfully', [
                    'manufacturer' => $manufacturer,
                    'vin' => $vin,
                    'endpoint' => $endpoint
                ]);

                return $this->normalizeVehicleData($manufacturer, $data);
            } else {
                Log::warning('Failed to retrieve vehicle data', [
                    'manufacturer' => $manufacturer,
                    'vin' => $vin,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockVehicleData($manufacturer, $vin);
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving vehicle data', [
                'manufacturer' => $manufacturer,
                'vin' => $vin,
                'error' => $e->getMessage()
            ]);

            return $this->getMockVehicleData($manufacturer, $vin);
        }
    }

    /**
     * Get vehicle diagnostics from manufacturer API
     */
    public function getVehicleDiagnostics(string $manufacturer, string $vin): array
    {
        if (!$this->isManufacturerEnabled($manufacturer)) {
            return $this->getMockDiagnostics($manufacturer, $vin);
        }

        $config = $this->manufacturerConfigs[$manufacturer];
        $endpoint = str_replace('{vin}', $vin, $config['endpoints']['diagnostics']);
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
                
                Log::info('Vehicle diagnostics retrieved successfully', [
                    'manufacturer' => $manufacturer,
                    'vin' => $vin,
                    'endpoint' => $endpoint
                ]);

                return $this->normalizeDiagnostics($manufacturer, $data);
            } else {
                Log::warning('Failed to retrieve vehicle diagnostics', [
                    'manufacturer' => $manufacturer,
                    'vin' => $vin,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockDiagnostics($manufacturer, $vin);
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving vehicle diagnostics', [
                'manufacturer' => $manufacturer,
                'vin' => $vin,
                'error' => $e->getMessage()
            ]);

            return $this->getMockDiagnostics($manufacturer, $vin);
        }
    }

    /**
     * Get vehicle maintenance data from manufacturer API
     */
    public function getVehicleMaintenance(string $manufacturer, string $vin): array
    {
        if (!$this->isManufacturerEnabled($manufacturer)) {
            return $this->getMockMaintenance($manufacturer, $vin);
        }

        $config = $this->manufacturerConfigs[$manufacturer];
        $endpoint = str_replace('{vin}', $vin, $config['endpoints']['maintenance']);
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
                
                Log::info('Vehicle maintenance data retrieved successfully', [
                    'manufacturer' => $manufacturer,
                    'vin' => $vin,
                    'endpoint' => $endpoint
                ]);

                return $this->normalizeMaintenance($manufacturer, $data);
            } else {
                Log::warning('Failed to retrieve vehicle maintenance data', [
                    'manufacturer' => $manufacturer,
                    'vin' => $vin,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockMaintenance($manufacturer, $vin);
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving vehicle maintenance data', [
                'manufacturer' => $manufacturer,
                'vin' => $vin,
                'error' => $e->getMessage()
            ]);

            return $this->getMockMaintenance($manufacturer, $vin);
        }
    }

    /**
     * Get vehicle status from manufacturer API
     */
    public function getVehicleStatus(string $manufacturer, string $vin): array
    {
        if (!$this->isManufacturerEnabled($manufacturer)) {
            return $this->getMockStatus($manufacturer, $vin);
        }

        $config = $this->manufacturerConfigs[$manufacturer];
        $endpoint = str_replace('{vin}', $vin, $config['endpoints']['status']);
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
                
                Log::info('Vehicle status retrieved successfully', [
                    'manufacturer' => $manufacturer,
                    'vin' => $vin,
                    'endpoint' => $endpoint
                ]);

                return $this->normalizeStatus($manufacturer, $data);
            } else {
                Log::warning('Failed to retrieve vehicle status', [
                    'manufacturer' => $manufacturer,
                    'vin' => $vin,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockStatus($manufacturer, $vin);
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving vehicle status', [
                'manufacturer' => $manufacturer,
                'vin' => $vin,
                'error' => $e->getMessage()
            ]);

            return $this->getMockStatus($manufacturer, $vin);
        }
    }

    /**
     * Check if manufacturer API is enabled
     */
    private function isManufacturerEnabled(string $manufacturer): bool
    {
        return isset($this->manufacturerConfigs[$manufacturer]) && 
               $this->manufacturerConfigs[$manufacturer]['enabled'] &&
               !empty($this->manufacturerConfigs[$manufacturer]['api_key']);
    }

    /**
     * Normalize vehicle data from different manufacturers
     */
    private function normalizeVehicleData(string $manufacturer, array $data): array
    {
        $normalized = [
            'manufacturer' => $manufacturer,
            'vin' => $data['vin'] ?? '',
            'make' => $data['make'] ?? $manufacturer,
            'model' => $data['model'] ?? '',
            'year' => $data['year'] ?? '',
            'engine' => [
                'type' => $data['engine']['type'] ?? '',
                'size' => $data['engine']['size'] ?? '',
                'power' => $data['engine']['power'] ?? '',
                'fuel_type' => $data['engine']['fuel_type'] ?? ''
            ],
            'transmission' => $data['transmission'] ?? '',
            'mileage' => $data['mileage'] ?? 0,
            'fuel_level' => $data['fuel_level'] ?? 0,
            'battery_level' => $data['battery_level'] ?? 0,
            'location' => [
                'latitude' => $data['location']['latitude'] ?? 0,
                'longitude' => $data['location']['longitude'] ?? 0,
                'address' => $data['location']['address'] ?? ''
            ],
            'last_updated' => now()->toISOString(),
            'data_source' => 'manufacturer_api'
        ];

        return $normalized;
    }

    /**
     * Normalize diagnostics data from different manufacturers
     */
    private function normalizeDiagnostics(string $manufacturer, array $data): array
    {
        $normalized = [
            'manufacturer' => $manufacturer,
            'vin' => $data['vin'] ?? '',
            'diagnostic_codes' => $data['diagnostic_codes'] ?? [],
            'engine_status' => $data['engine_status'] ?? 'unknown',
            'transmission_status' => $data['transmission_status'] ?? 'unknown',
            'brake_status' => $data['brake_status'] ?? 'unknown',
            'tire_pressure' => $data['tire_pressure'] ?? [],
            'fluid_levels' => $data['fluid_levels'] ?? [],
            'warning_lights' => $data['warning_lights'] ?? [],
            'last_scan' => $data['last_scan'] ?? now()->toISOString(),
            'data_source' => 'manufacturer_api'
        ];

        return $normalized;
    }

    /**
     * Normalize maintenance data from different manufacturers
     */
    private function normalizeMaintenance(string $manufacturer, array $data): array
    {
        $normalized = [
            'manufacturer' => $manufacturer,
            'vin' => $data['vin'] ?? '',
            'maintenance_schedule' => $data['maintenance_schedule'] ?? [],
            'next_service' => $data['next_service'] ?? null,
            'service_history' => $data['service_history'] ?? [],
            'warranty_status' => $data['warranty_status'] ?? 'unknown',
            'recall_notices' => $data['recall_notices'] ?? [],
            'last_updated' => now()->toISOString(),
            'data_source' => 'manufacturer_api'
        ];

        return $normalized;
    }

    /**
     * Normalize status data from different manufacturers
     */
    private function normalizeStatus(string $manufacturer, array $data): array
    {
        $normalized = [
            'manufacturer' => $manufacturer,
            'vin' => $data['vin'] ?? '',
            'status' => $data['status'] ?? 'unknown',
            'doors_locked' => $data['doors_locked'] ?? false,
            'windows_closed' => $data['windows_closed'] ?? false,
            'lights_on' => $data['lights_on'] ?? false,
            'engine_running' => $data['engine_running'] ?? false,
            'climate_control' => $data['climate_control'] ?? [],
            'last_updated' => now()->toISOString(),
            'data_source' => 'manufacturer_api'
        ];

        return $normalized;
    }

    /**
     * Get mock vehicle data for testing
     */
    private function getMockVehicleData(string $manufacturer, string $vin): array
    {
        return [
            'manufacturer' => $manufacturer,
            'vin' => $vin,
            'make' => ucfirst($manufacturer),
            'model' => 'Sample Model',
            'year' => 2023,
            'engine' => [
                'type' => 'Gasoline',
                'size' => '2.0L',
                'power' => '200 HP',
                'fuel_type' => 'Gasoline'
            ],
            'transmission' => 'Automatic',
            'mileage' => rand(10000, 100000),
            'fuel_level' => rand(20, 100),
            'battery_level' => rand(80, 100),
            'location' => [
                'latitude' => 42.6629,
                'longitude' => 21.1655,
                'address' => 'Pristina, Kosovo'
            ],
            'last_updated' => now()->toISOString(),
            'data_source' => 'mock_data'
        ];
    }

    /**
     * Get mock diagnostics data for testing
     */
    private function getMockDiagnostics(string $manufacturer, string $vin): array
    {
        return [
            'manufacturer' => $manufacturer,
            'vin' => $vin,
            'diagnostic_codes' => [],
            'engine_status' => 'good',
            'transmission_status' => 'good',
            'brake_status' => 'good',
            'tire_pressure' => [
                'front_left' => 32,
                'front_right' => 32,
                'rear_left' => 30,
                'rear_right' => 30
            ],
            'fluid_levels' => [
                'engine_oil' => 'good',
                'coolant' => 'good',
                'brake_fluid' => 'good',
                'transmission_fluid' => 'good'
            ],
            'warning_lights' => [],
            'last_scan' => now()->toISOString(),
            'data_source' => 'mock_data'
        ];
    }

    /**
     * Get mock maintenance data for testing
     */
    private function getMockMaintenance(string $manufacturer, string $vin): array
    {
        return [
            'manufacturer' => $manufacturer,
            'vin' => $vin,
            'maintenance_schedule' => [
                [
                    'service' => 'Oil Change',
                    'due_mileage' => 5000,
                    'due_date' => now()->addMonths(3)->toISOString()
                ],
                [
                    'service' => 'Brake Inspection',
                    'due_mileage' => 10000,
                    'due_date' => now()->addMonths(6)->toISOString()
                ]
            ],
            'next_service' => [
                'service' => 'Oil Change',
                'due_mileage' => 5000,
                'due_date' => now()->addMonths(3)->toISOString()
            ],
            'service_history' => [],
            'warranty_status' => 'active',
            'recall_notices' => [],
            'last_updated' => now()->toISOString(),
            'data_source' => 'mock_data'
        ];
    }

    /**
     * Get mock status data for testing
     */
    private function getMockStatus(string $manufacturer, string $vin): array
    {
        return [
            'manufacturer' => $manufacturer,
            'vin' => $vin,
            'status' => 'parked',
            'doors_locked' => true,
            'windows_closed' => true,
            'lights_on' => false,
            'engine_running' => false,
            'climate_control' => [
                'temperature' => 22,
                'fan_speed' => 0,
                'ac_on' => false
            ],
            'last_updated' => now()->toISOString(),
            'data_source' => 'mock_data'
        ];
    }

    /**
     * Get supported manufacturers
     */
    public function getSupportedManufacturers(): array
    {
        return array_keys($this->manufacturerConfigs);
    }

    /**
     * Get manufacturer configuration
     */
    public function getManufacturerConfig(string $manufacturer): array
    {
        return $this->manufacturerConfigs[$manufacturer] ?? [];
    }

    /**
     * Get service status
     */
    public function getStatus(): array
    {
        $status = [];
        
        foreach ($this->manufacturerConfigs as $manufacturer => $config) {
            $status[$manufacturer] = [
                'enabled' => $config['enabled'],
                'configured' => !empty($config['api_key']),
                'base_url' => $config['base_url']
            ];
        }

        return $status;
    }

    /**
     * Test manufacturer API connection
     */
    public function testManufacturerAPI(string $manufacturer): bool
    {
        if (!$this->isManufacturerEnabled($manufacturer)) {
            return false;
        }

        $config = $this->manufacturerConfigs[$manufacturer];
        $testVin = 'TEST12345678901234'; // Test VIN
        $endpoint = str_replace('{vin}', $testVin, $config['endpoints']['vehicle_data']);
        $url = $config['base_url'] . $endpoint;

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Accept' => 'application/json'
                ])
                ->get($url);

            // Even if the test VIN doesn't exist, a 404 is better than auth failure
            return $response->status() !== 401 && $response->status() !== 403;
        } catch (\Exception $e) {
            Log::error('Manufacturer API test failed', [
                'manufacturer' => $manufacturer,
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

class CarManufacturerAPIService
{
    private $manufacturerConfigs;
    private $defaultTimeout = 30;

    public function __construct()
    {
        $this->manufacturerConfigs = [
            'bmw' => [
                'enabled' => config('services.bmw.enabled', false),
                'api_key' => config('services.bmw.api_key'),
                'base_url' => config('services.bmw.base_url', 'https://api.bmw.com'),
                'endpoints' => [
                    'vehicle_data' => '/v1/vehicles/{vin}/data',
                    'diagnostics' => '/v1/vehicles/{vin}/diagnostics',
                    'maintenance' => '/v1/vehicles/{vin}/maintenance',
                    'status' => '/v1/vehicles/{vin}/status'
                ]
            ],
            'mercedes' => [
                'enabled' => config('services.mercedes.enabled', false),
                'api_key' => config('services.mercedes.api_key'),
                'base_url' => config('services.mercedes.base_url', 'https://api.mercedes-benz.com'),
                'endpoints' => [
                    'vehicle_data' => '/vehicledata/v1/vehicles/{vin}',
                    'diagnostics' => '/vehicledata/v1/vehicles/{vin}/diagnostics',
                    'maintenance' => '/vehicledata/v1/vehicles/{vin}/maintenance',
                    'status' => '/vehicledata/v1/vehicles/{vin}/status'
                ]
            ],
            'volkswagen' => [
                'enabled' => config('services.volkswagen.enabled', false),
                'api_key' => config('services.volkswagen.api_key'),
                'base_url' => config('services.volkswagen.base_url', 'https://api.volkswagen.com'),
                'endpoints' => [
                    'vehicle_data' => '/v1/vehicles/{vin}/data',
                    'diagnostics' => '/v1/vehicles/{vin}/diagnostics',
                    'maintenance' => '/v1/vehicles/{vin}/maintenance',
                    'status' => '/v1/vehicles/{vin}/status'
                ]
            ],
            'audi' => [
                'enabled' => config('services.audi.enabled', false),
                'api_key' => config('services.audi.api_key'),
                'base_url' => config('services.audi.base_url', 'https://api.audi.com'),
                'endpoints' => [
                    'vehicle_data' => '/v1/vehicles/{vin}/data',
                    'diagnostics' => '/v1/vehicles/{vin}/diagnostics',
                    'maintenance' => '/v1/vehicles/{vin}/maintenance',
                    'status' => '/v1/vehicles/{vin}/status'
                ]
            ],
            'ford' => [
                'enabled' => config('services.ford.enabled', false),
                'api_key' => config('services.ford.api_key'),
                'base_url' => config('services.ford.base_url', 'https://api.ford.com'),
                'endpoints' => [
                    'vehicle_data' => '/v1/vehicles/{vin}/data',
                    'diagnostics' => '/v1/vehicles/{vin}/diagnostics',
                    'maintenance' => '/v1/vehicles/{vin}/maintenance',
                    'status' => '/v1/vehicles/{vin}/status'
                ]
            ],
            'toyota' => [
                'enabled' => config('services.toyota.enabled', false),
                'api_key' => config('services.toyota.api_key'),
                'base_url' => config('services.toyota.base_url', 'https://api.toyota.com'),
                'endpoints' => [
                    'vehicle_data' => '/v1/vehicles/{vin}/data',
                    'diagnostics' => '/v1/vehicles/{vin}/diagnostics',
                    'maintenance' => '/v1/vehicles/{vin}/maintenance',
                    'status' => '/v1/vehicles/{vin}/status'
                ]
            ],
            'volvo' => [
                'enabled' => config('services.volvo.enabled', false),
                'api_key' => config('services.volvo.api_key'),
                'base_url' => config('services.volvo.base_url', 'https://api.volvocars.com'),
                'endpoints' => [
                    'vehicle_data' => '/v1/vehicles/{vin}/data',
                    'diagnostics' => '/v1/vehicles/{vin}/diagnostics',
                    'maintenance' => '/v1/vehicles/{vin}/maintenance',
                    'status' => '/v1/vehicles/{vin}/status'
                ]
            ],
            'tesla' => [
                'enabled' => config('services.tesla.enabled', false),
                'api_key' => config('services.tesla.api_key'),
                'base_url' => config('services.tesla.base_url', 'https://owner-api.teslamotors.com'),
                'endpoints' => [
                    'vehicle_data' => '/api/1/vehicles/{vin}/data',
                    'diagnostics' => '/api/1/vehicles/{vin}/diagnostics',
                    'maintenance' => '/api/1/vehicles/{vin}/maintenance',
                    'status' => '/api/1/vehicles/{vin}/status'
                ]
            ]
        ];
    }

    /**
     * Get vehicle data from manufacturer API
     */
    public function getVehicleData(string $manufacturer, string $vin): array
    {
        if (!$this->isManufacturerEnabled($manufacturer)) {
            return $this->getMockVehicleData($manufacturer, $vin);
        }

        $config = $this->manufacturerConfigs[$manufacturer];
        $endpoint = str_replace('{vin}', $vin, $config['endpoints']['vehicle_data']);
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
                
                Log::info('Vehicle data retrieved successfully', [
                    'manufacturer' => $manufacturer,
                    'vin' => $vin,
                    'endpoint' => $endpoint
                ]);

                return $this->normalizeVehicleData($manufacturer, $data);
            } else {
                Log::warning('Failed to retrieve vehicle data', [
                    'manufacturer' => $manufacturer,
                    'vin' => $vin,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockVehicleData($manufacturer, $vin);
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving vehicle data', [
                'manufacturer' => $manufacturer,
                'vin' => $vin,
                'error' => $e->getMessage()
            ]);

            return $this->getMockVehicleData($manufacturer, $vin);
        }
    }

    /**
     * Get vehicle diagnostics from manufacturer API
     */
    public function getVehicleDiagnostics(string $manufacturer, string $vin): array
    {
        if (!$this->isManufacturerEnabled($manufacturer)) {
            return $this->getMockDiagnostics($manufacturer, $vin);
        }

        $config = $this->manufacturerConfigs[$manufacturer];
        $endpoint = str_replace('{vin}', $vin, $config['endpoints']['diagnostics']);
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
                
                Log::info('Vehicle diagnostics retrieved successfully', [
                    'manufacturer' => $manufacturer,
                    'vin' => $vin,
                    'endpoint' => $endpoint
                ]);

                return $this->normalizeDiagnostics($manufacturer, $data);
            } else {
                Log::warning('Failed to retrieve vehicle diagnostics', [
                    'manufacturer' => $manufacturer,
                    'vin' => $vin,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockDiagnostics($manufacturer, $vin);
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving vehicle diagnostics', [
                'manufacturer' => $manufacturer,
                'vin' => $vin,
                'error' => $e->getMessage()
            ]);

            return $this->getMockDiagnostics($manufacturer, $vin);
        }
    }

    /**
     * Get vehicle maintenance data from manufacturer API
     */
    public function getVehicleMaintenance(string $manufacturer, string $vin): array
    {
        if (!$this->isManufacturerEnabled($manufacturer)) {
            return $this->getMockMaintenance($manufacturer, $vin);
        }

        $config = $this->manufacturerConfigs[$manufacturer];
        $endpoint = str_replace('{vin}', $vin, $config['endpoints']['maintenance']);
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
                
                Log::info('Vehicle maintenance data retrieved successfully', [
                    'manufacturer' => $manufacturer,
                    'vin' => $vin,
                    'endpoint' => $endpoint
                ]);

                return $this->normalizeMaintenance($manufacturer, $data);
            } else {
                Log::warning('Failed to retrieve vehicle maintenance data', [
                    'manufacturer' => $manufacturer,
                    'vin' => $vin,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockMaintenance($manufacturer, $vin);
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving vehicle maintenance data', [
                'manufacturer' => $manufacturer,
                'vin' => $vin,
                'error' => $e->getMessage()
            ]);

            return $this->getMockMaintenance($manufacturer, $vin);
        }
    }

    /**
     * Get vehicle status from manufacturer API
     */
    public function getVehicleStatus(string $manufacturer, string $vin): array
    {
        if (!$this->isManufacturerEnabled($manufacturer)) {
            return $this->getMockStatus($manufacturer, $vin);
        }

        $config = $this->manufacturerConfigs[$manufacturer];
        $endpoint = str_replace('{vin}', $vin, $config['endpoints']['status']);
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
                
                Log::info('Vehicle status retrieved successfully', [
                    'manufacturer' => $manufacturer,
                    'vin' => $vin,
                    'endpoint' => $endpoint
                ]);

                return $this->normalizeStatus($manufacturer, $data);
            } else {
                Log::warning('Failed to retrieve vehicle status', [
                    'manufacturer' => $manufacturer,
                    'vin' => $vin,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $this->getMockStatus($manufacturer, $vin);
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving vehicle status', [
                'manufacturer' => $manufacturer,
                'vin' => $vin,
                'error' => $e->getMessage()
            ]);

            return $this->getMockStatus($manufacturer, $vin);
        }
    }

    /**
     * Check if manufacturer API is enabled
     */
    private function isManufacturerEnabled(string $manufacturer): bool
    {
        return isset($this->manufacturerConfigs[$manufacturer]) && 
               $this->manufacturerConfigs[$manufacturer]['enabled'] &&
               !empty($this->manufacturerConfigs[$manufacturer]['api_key']);
    }

    /**
     * Normalize vehicle data from different manufacturers
     */
    private function normalizeVehicleData(string $manufacturer, array $data): array
    {
        $normalized = [
            'manufacturer' => $manufacturer,
            'vin' => $data['vin'] ?? '',
            'make' => $data['make'] ?? $manufacturer,
            'model' => $data['model'] ?? '',
            'year' => $data['year'] ?? '',
            'engine' => [
                'type' => $data['engine']['type'] ?? '',
                'size' => $data['engine']['size'] ?? '',
                'power' => $data['engine']['power'] ?? '',
                'fuel_type' => $data['engine']['fuel_type'] ?? ''
            ],
            'transmission' => $data['transmission'] ?? '',
            'mileage' => $data['mileage'] ?? 0,
            'fuel_level' => $data['fuel_level'] ?? 0,
            'battery_level' => $data['battery_level'] ?? 0,
            'location' => [
                'latitude' => $data['location']['latitude'] ?? 0,
                'longitude' => $data['location']['longitude'] ?? 0,
                'address' => $data['location']['address'] ?? ''
            ],
            'last_updated' => now()->toISOString(),
            'data_source' => 'manufacturer_api'
        ];

        return $normalized;
    }

    /**
     * Normalize diagnostics data from different manufacturers
     */
    private function normalizeDiagnostics(string $manufacturer, array $data): array
    {
        $normalized = [
            'manufacturer' => $manufacturer,
            'vin' => $data['vin'] ?? '',
            'diagnostic_codes' => $data['diagnostic_codes'] ?? [],
            'engine_status' => $data['engine_status'] ?? 'unknown',
            'transmission_status' => $data['transmission_status'] ?? 'unknown',
            'brake_status' => $data['brake_status'] ?? 'unknown',
            'tire_pressure' => $data['tire_pressure'] ?? [],
            'fluid_levels' => $data['fluid_levels'] ?? [],
            'warning_lights' => $data['warning_lights'] ?? [],
            'last_scan' => $data['last_scan'] ?? now()->toISOString(),
            'data_source' => 'manufacturer_api'
        ];

        return $normalized;
    }

    /**
     * Normalize maintenance data from different manufacturers
     */
    private function normalizeMaintenance(string $manufacturer, array $data): array
    {
        $normalized = [
            'manufacturer' => $manufacturer,
            'vin' => $data['vin'] ?? '',
            'maintenance_schedule' => $data['maintenance_schedule'] ?? [],
            'next_service' => $data['next_service'] ?? null,
            'service_history' => $data['service_history'] ?? [],
            'warranty_status' => $data['warranty_status'] ?? 'unknown',
            'recall_notices' => $data['recall_notices'] ?? [],
            'last_updated' => now()->toISOString(),
            'data_source' => 'manufacturer_api'
        ];

        return $normalized;
    }

    /**
     * Normalize status data from different manufacturers
     */
    private function normalizeStatus(string $manufacturer, array $data): array
    {
        $normalized = [
            'manufacturer' => $manufacturer,
            'vin' => $data['vin'] ?? '',
            'status' => $data['status'] ?? 'unknown',
            'doors_locked' => $data['doors_locked'] ?? false,
            'windows_closed' => $data['windows_closed'] ?? false,
            'lights_on' => $data['lights_on'] ?? false,
            'engine_running' => $data['engine_running'] ?? false,
            'climate_control' => $data['climate_control'] ?? [],
            'last_updated' => now()->toISOString(),
            'data_source' => 'manufacturer_api'
        ];

        return $normalized;
    }

    /**
     * Get mock vehicle data for testing
     */
    private function getMockVehicleData(string $manufacturer, string $vin): array
    {
        return [
            'manufacturer' => $manufacturer,
            'vin' => $vin,
            'make' => ucfirst($manufacturer),
            'model' => 'Sample Model',
            'year' => 2023,
            'engine' => [
                'type' => 'Gasoline',
                'size' => '2.0L',
                'power' => '200 HP',
                'fuel_type' => 'Gasoline'
            ],
            'transmission' => 'Automatic',
            'mileage' => rand(10000, 100000),
            'fuel_level' => rand(20, 100),
            'battery_level' => rand(80, 100),
            'location' => [
                'latitude' => 42.6629,
                'longitude' => 21.1655,
                'address' => 'Pristina, Kosovo'
            ],
            'last_updated' => now()->toISOString(),
            'data_source' => 'mock_data'
        ];
    }

    /**
     * Get mock diagnostics data for testing
     */
    private function getMockDiagnostics(string $manufacturer, string $vin): array
    {
        return [
            'manufacturer' => $manufacturer,
            'vin' => $vin,
            'diagnostic_codes' => [],
            'engine_status' => 'good',
            'transmission_status' => 'good',
            'brake_status' => 'good',
            'tire_pressure' => [
                'front_left' => 32,
                'front_right' => 32,
                'rear_left' => 30,
                'rear_right' => 30
            ],
            'fluid_levels' => [
                'engine_oil' => 'good',
                'coolant' => 'good',
                'brake_fluid' => 'good',
                'transmission_fluid' => 'good'
            ],
            'warning_lights' => [],
            'last_scan' => now()->toISOString(),
            'data_source' => 'mock_data'
        ];
    }

    /**
     * Get mock maintenance data for testing
     */
    private function getMockMaintenance(string $manufacturer, string $vin): array
    {
        return [
            'manufacturer' => $manufacturer,
            'vin' => $vin,
            'maintenance_schedule' => [
                [
                    'service' => 'Oil Change',
                    'due_mileage' => 5000,
                    'due_date' => now()->addMonths(3)->toISOString()
                ],
                [
                    'service' => 'Brake Inspection',
                    'due_mileage' => 10000,
                    'due_date' => now()->addMonths(6)->toISOString()
                ]
            ],
            'next_service' => [
                'service' => 'Oil Change',
                'due_mileage' => 5000,
                'due_date' => now()->addMonths(3)->toISOString()
            ],
            'service_history' => [],
            'warranty_status' => 'active',
            'recall_notices' => [],
            'last_updated' => now()->toISOString(),
            'data_source' => 'mock_data'
        ];
    }

    /**
     * Get mock status data for testing
     */
    private function getMockStatus(string $manufacturer, string $vin): array
    {
        return [
            'manufacturer' => $manufacturer,
            'vin' => $vin,
            'status' => 'parked',
            'doors_locked' => true,
            'windows_closed' => true,
            'lights_on' => false,
            'engine_running' => false,
            'climate_control' => [
                'temperature' => 22,
                'fan_speed' => 0,
                'ac_on' => false
            ],
            'last_updated' => now()->toISOString(),
            'data_source' => 'mock_data'
        ];
    }

    /**
     * Get supported manufacturers
     */
    public function getSupportedManufacturers(): array
    {
        return array_keys($this->manufacturerConfigs);
    }

    /**
     * Get manufacturer configuration
     */
    public function getManufacturerConfig(string $manufacturer): array
    {
        return $this->manufacturerConfigs[$manufacturer] ?? [];
    }

    /**
     * Get service status
     */
    public function getStatus(): array
    {
        $status = [];
        
        foreach ($this->manufacturerConfigs as $manufacturer => $config) {
            $status[$manufacturer] = [
                'enabled' => $config['enabled'],
                'configured' => !empty($config['api_key']),
                'base_url' => $config['base_url']
            ];
        }

        return $status;
    }

    /**
     * Test manufacturer API connection
     */
    public function testManufacturerAPI(string $manufacturer): bool
    {
        if (!$this->isManufacturerEnabled($manufacturer)) {
            return false;
        }

        $config = $this->manufacturerConfigs[$manufacturer];
        $testVin = 'TEST12345678901234'; // Test VIN
        $endpoint = str_replace('{vin}', $testVin, $config['endpoints']['vehicle_data']);
        $url = $config['base_url'] . $endpoint;

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Accept' => 'application/json'
                ])
                ->get($url);

            // Even if the test VIN doesn't exist, a 404 is better than auth failure
            return $response->status() !== 401 && $response->status() !== 403;
        } catch (\Exception $e) {
            Log::error('Manufacturer API test failed', [
                'manufacturer' => $manufacturer,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}














