<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CarManufacturerAPIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CarManufacturerController extends Controller
{
    private $manufacturerAPIService;

    public function __construct(CarManufacturerAPIService $manufacturerAPIService)
    {
        $this->manufacturerAPIService = $manufacturerAPIService;
    }

    /**
     * Get vehicle data from manufacturer API
     */
    public function getVehicleData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'manufacturer' => 'required|string|in:bmw,mercedes,volkswagen,audi,ford,toyota,volvo,tesla',
            'vin' => 'required|string|size:17',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $manufacturer = $request->manufacturer;
            $vin = $request->vin;

            $vehicleData = $this->manufacturerAPIService->getVehicleData($manufacturer, $vin);

            return response()->json([
                'success' => true,
                'data' => $vehicleData
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get vehicle data', [
                'user_id' => Auth::id(),
                'manufacturer' => $request->manufacturer,
                'vin' => $request->vin,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve vehicle data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vehicle diagnostics from manufacturer API
     */
    public function getVehicleDiagnostics(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'manufacturer' => 'required|string|in:bmw,mercedes,volkswagen,audi,ford,toyota,volvo,tesla',
            'vin' => 'required|string|size:17',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $manufacturer = $request->manufacturer;
            $vin = $request->vin;

            $diagnostics = $this->manufacturerAPIService->getVehicleDiagnostics($manufacturer, $vin);

            return response()->json([
                'success' => true,
                'data' => $diagnostics
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get vehicle diagnostics', [
                'user_id' => Auth::id(),
                'manufacturer' => $request->manufacturer,
                'vin' => $request->vin,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve vehicle diagnostics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vehicle maintenance data from manufacturer API
     */
    public function getVehicleMaintenance(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'manufacturer' => 'required|string|in:bmw,mercedes,volkswagen,audi,ford,toyota,volvo,tesla',
            'vin' => 'required|string|size:17',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $manufacturer = $request->manufacturer;
            $vin = $request->vin;

            $maintenance = $this->manufacturerAPIService->getVehicleMaintenance($manufacturer, $vin);

            return response()->json([
                'success' => true,
                'data' => $maintenance
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get vehicle maintenance data', [
                'user_id' => Auth::id(),
                'manufacturer' => $request->manufacturer,
                'vin' => $request->vin,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve vehicle maintenance data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vehicle status from manufacturer API
     */
    public function getVehicleStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'manufacturer' => 'required|string|in:bmw,mercedes,volkswagen,audi,ford,toyota,volvo,tesla',
            'vin' => 'required|string|size:17',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $manufacturer = $request->manufacturer;
            $vin = $request->vin;

            $status = $this->manufacturerAPIService->getVehicleStatus($manufacturer, $vin);

            return response()->json([
                'success' => true,
                'data' => $status
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get vehicle status', [
                'user_id' => Auth::id(),
                'manufacturer' => $request->manufacturer,
                'vin' => $request->vin,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve vehicle status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comprehensive vehicle information
     */
    public function getVehicleInfo(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'manufacturer' => 'required|string|in:bmw,mercedes,volkswagen,audi,ford,toyota,volvo,tesla',
            'vin' => 'required|string|size:17',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $manufacturer = $request->manufacturer;
            $vin = $request->vin;

            // Get all vehicle information in parallel
            $vehicleData = $this->manufacturerAPIService->getVehicleData($manufacturer, $vin);
            $diagnostics = $this->manufacturerAPIService->getVehicleDiagnostics($manufacturer, $vin);
            $maintenance = $this->manufacturerAPIService->getVehicleMaintenance($manufacturer, $vin);
            $status = $this->manufacturerAPIService->getVehicleStatus($manufacturer, $vin);

            $comprehensiveData = [
                'vehicle' => $vehicleData,
                'diagnostics' => $diagnostics,
                'maintenance' => $maintenance,
                'status' => $status,
                'last_updated' => now()->toISOString(),
                'data_sources' => [
                    'vehicle_data' => $vehicleData['data_source'] ?? 'unknown',
                    'diagnostics' => $diagnostics['data_source'] ?? 'unknown',
                    'maintenance' => $maintenance['data_source'] ?? 'unknown',
                    'status' => $status['data_source'] ?? 'unknown'
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $comprehensiveData
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get comprehensive vehicle information', [
                'user_id' => Auth::id(),
                'manufacturer' => $request->manufacturer,
                'vin' => $request->vin,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve comprehensive vehicle information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get supported manufacturers
     */
    public function getSupportedManufacturers(): JsonResponse
    {
        try {
            $manufacturers = $this->manufacturerAPIService->getSupportedManufacturers();
            $status = $this->manufacturerAPIService->getStatus();

            $manufacturerInfo = [];
            foreach ($manufacturers as $manufacturer) {
                $manufacturerInfo[] = [
                    'name' => $manufacturer,
                    'display_name' => ucfirst($manufacturer),
                    'enabled' => $status[$manufacturer]['enabled'] ?? false,
                    'configured' => $status[$manufacturer]['configured'] ?? false,
                    'api_url' => $status[$manufacturer]['base_url'] ?? ''
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'manufacturers' => $manufacturerInfo,
                    'total_count' => count($manufacturers)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get supported manufacturers', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve supported manufacturers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test manufacturer API connection
     */
    public function testManufacturerAPI(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'manufacturer' => 'required|string|in:bmw,mercedes,volkswagen,audi,ford,toyota,volvo,tesla',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $manufacturer = $request->manufacturer;
            $result = $this->manufacturerAPIService->testManufacturerAPI($manufacturer);

            return response()->json([
                'success' => true,
                'data' => [
                    'manufacturer' => $manufacturer,
                    'connection_test' => $result ? 'success' : 'failed',
                    'status' => $result ? 'API connection successful' : 'API connection failed'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to test manufacturer API', [
                'user_id' => Auth::id(),
                'manufacturer' => $request->manufacturer,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to test manufacturer API',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get manufacturer API status
     */
    public function getAPIStatus(): JsonResponse
    {
        try {
            $status = $this->manufacturerAPIService->getStatus();

            return response()->json([
                'success' => true,
                'data' => [
                    'apis' => $status,
                    'total_manufacturers' => count($status),
                    'enabled_apis' => count(array_filter($status, fn($s) => $s['enabled'])),
                    'configured_apis' => count(array_filter($status, fn($s) => $s['configured']))
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get manufacturer API status', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve manufacturer API status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get manufacturer API documentation
     */
    public function getAPIDocumentation(): JsonResponse
    {
        try {
            $documentation = [
                'bmw' => [
                    'name' => 'BMW CarData API',
                    'status' => 'Public',
                    'documentation_url' => 'https://bmw-cardata.bmwgroup.com',
                    'endpoints' => [
                        'vehicle_data' => 'Get vehicle information and specifications',
                        'diagnostics' => 'Get diagnostic trouble codes and system status',
                        'maintenance' => 'Get maintenance schedule and history',
                        'status' => 'Get real-time vehicle status'
                    ],
                    'authentication' => 'API Key',
                    'rate_limits' => '1000 requests/hour'
                ],
                'mercedes' => [
                    'name' => 'Mercedes-Benz Connected Vehicle API',
                    'status' => 'Public',
                    'documentation_url' => 'https://developer.mercedes-benz.com',
                    'endpoints' => [
                        'vehicle_data' => 'Get vehicle information and specifications',
                        'diagnostics' => 'Get diagnostic trouble codes and system status',
                        'maintenance' => 'Get maintenance schedule and history',
                        'status' => 'Get real-time vehicle status'
                    ],
                    'authentication' => 'OAuth 2.0',
                    'rate_limits' => '500 requests/hour'
                ],
                'volkswagen' => [
                    'name' => 'VW Automotive Cloud API',
                    'status' => 'Partner-only',
                    'documentation_url' => 'https://d-vwacv-c0e-apim-portal.vwcloud.org',
                    'endpoints' => [
                        'vehicle_data' => 'Get vehicle information and specifications',
                        'diagnostics' => 'Get diagnostic trouble codes and system status',
                        'maintenance' => 'Get maintenance schedule and history',
                        'status' => 'Get real-time vehicle status'
                    ],
                    'authentication' => 'API Key',
                    'rate_limits' => '2000 requests/hour'
                ],
                'audi' => [
                    'name' => 'Audi Data API',
                    'status' => 'Partner/Aggregator',
                    'documentation_url' => 'https://www.high-mobility.com/car-api/audi-data-api',
                    'endpoints' => [
                        'vehicle_data' => 'Get vehicle information and specifications',
                        'diagnostics' => 'Get diagnostic trouble codes and system status',
                        'maintenance' => 'Get maintenance schedule and history',
                        'status' => 'Get real-time vehicle status'
                    ],
                    'authentication' => 'API Key',
                    'rate_limits' => '1000 requests/hour'
                ],
                'ford' => [
                    'name' => 'FordPass API',
                    'status' => 'Public',
                    'documentation_url' => 'https://developer.ford.com',
                    'endpoints' => [
                        'vehicle_data' => 'Get vehicle information and specifications',
                        'diagnostics' => 'Get diagnostic trouble codes and system status',
                        'maintenance' => 'Get maintenance schedule and history',
                        'status' => 'Get real-time vehicle status'
                    ],
                    'authentication' => 'OAuth 2.0',
                    'rate_limits' => '1000 requests/hour'
                ],
                'toyota' => [
                    'name' => 'Toyota Developer Portal API',
                    'status' => 'Public',
                    'documentation_url' => 'https://developer.eig.toyota.com',
                    'endpoints' => [
                        'vehicle_data' => 'Get vehicle information and specifications',
                        'diagnostics' => 'Get diagnostic trouble codes and system status',
                        'maintenance' => 'Get maintenance schedule and history',
                        'status' => 'Get real-time vehicle status'
                    ],
                    'authentication' => 'API Key',
                    'rate_limits' => '500 requests/hour'
                ],
                'volvo' => [
                    'name' => 'Volvo Cars Connected Vehicle API',
                    'status' => 'Public',
                    'documentation_url' => 'https://developer.volvocars.com',
                    'endpoints' => [
                        'vehicle_data' => 'Get vehicle information and specifications',
                        'diagnostics' => 'Get diagnostic trouble codes and system status',
                        'maintenance' => 'Get maintenance schedule and history',
                        'status' => 'Get real-time vehicle status'
                    ],
                    'authentication' => 'OAuth 2.0',
                    'rate_limits' => '1000 requests/hour'
                ],
                'tesla' => [
                    'name' => 'Tesla Fleet/Owner API',
                    'status' => 'Public/Community',
                    'documentation_url' => 'https://developer.tesla.com',
                    'endpoints' => [
                        'vehicle_data' => 'Get vehicle information and specifications',
                        'diagnostics' => 'Get diagnostic trouble codes and system status',
                        'maintenance' => 'Get maintenance schedule and history',
                        'status' => 'Get real-time vehicle status'
                    ],
                    'authentication' => 'OAuth 2.0',
                    'rate_limits' => '1000 requests/hour'
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'documentation' => $documentation,
                    'total_apis' => count($documentation),
                    'public_apis' => count(array_filter($documentation, fn($d) => $d['status'] === 'Public')),
                    'partner_apis' => count(array_filter($documentation, fn($d) => str_contains($d['status'], 'Partner')))
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get manufacturer API documentation', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve manufacturer API documentation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}














