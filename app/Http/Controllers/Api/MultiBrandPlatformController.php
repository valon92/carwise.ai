<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MultiBrandPlatformAPIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MultiBrandPlatformController extends Controller
{
    private $multiBrandPlatformService;

    public function __construct(MultiBrandPlatformAPIService $multiBrandPlatformService)
    {
        $this->multiBrandPlatformService = $multiBrandPlatformService;
    }

    /**
     * Get vehicle data from Smartcar
     */
    public function getSmartcarVehicleData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|string',
            'access_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $vehicleId = $request->vehicle_id;
            $accessToken = $request->access_token;

            $vehicleData = $this->multiBrandPlatformService->getSmartcarVehicleData($vehicleId, $accessToken);

            return response()->json([
                'success' => true,
                'data' => $vehicleData
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get Smartcar vehicle data', [
                'user_id' => Auth::id(),
                'vehicle_id' => $request->vehicle_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Smartcar vehicle data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vehicle data from High Mobility
     */
    public function getHighMobilityVehicleData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $vehicleId = $request->vehicle_id;

            $vehicleData = $this->multiBrandPlatformService->getHighMobilityVehicleData($vehicleId);

            return response()->json([
                'success' => true,
                'data' => $vehicleData
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get High Mobility vehicle data', [
                'user_id' => Auth::id(),
                'vehicle_id' => $request->vehicle_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve High Mobility vehicle data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vehicle data from Otonomo
     */
    public function getOtonomoVehicleData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $vehicleId = $request->vehicle_id;

            $vehicleData = $this->multiBrandPlatformService->getOtonomoVehicleData($vehicleId);

            return response()->json([
                'success' => true,
                'data' => $vehicleData
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get Otonomo vehicle data', [
                'user_id' => Auth::id(),
                'vehicle_id' => $request->vehicle_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Otonomo vehicle data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vehicle data from Wejo
     */
    public function getWejoVehicleData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $vehicleId = $request->vehicle_id;

            $vehicleData = $this->multiBrandPlatformService->getWejoVehicleData($vehicleId);

            return response()->json([
                'success' => true,
                'data' => $vehicleData
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get Wejo vehicle data', [
                'user_id' => Auth::id(),
                'vehicle_id' => $request->vehicle_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Wejo vehicle data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get diagnostic data from MotorData
     */
    public function getMotorDataDiagnostics(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vin' => 'required|string|size:17',
            'dtc_code' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $vin = $request->vin;
            $dtcCode = $request->dtc_code;

            $diagnostics = $this->multiBrandPlatformService->getMotorDataDiagnostics($vin, $dtcCode);

            return response()->json([
                'success' => true,
                'data' => $diagnostics
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get MotorData diagnostics', [
                'user_id' => Auth::id(),
                'vin' => $request->vin,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve MotorData diagnostics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vehicle data from CarAPI.app
     */
    public function getCarAPIVehicleData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'make' => 'required|string',
            'model' => 'required|string',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $make = $request->make;
            $model = $request->model;
            $year = $request->year;

            $vehicleData = $this->multiBrandPlatformService->getCarAPIVehicleData($make, $model, $year);

            return response()->json([
                'success' => true,
                'data' => $vehicleData
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get CarAPI vehicle data', [
                'user_id' => Auth::id(),
                'make' => $request->make,
                'model' => $request->model,
                'year' => $request->year,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve CarAPI vehicle data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comprehensive vehicle data from all available platforms
     */
    public function getComprehensiveVehicleData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|string',
            'make' => 'nullable|string',
            'model' => 'nullable|string',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $vehicleId = $request->vehicle_id;
            $make = $request->make;
            $model = $request->model;
            $year = $request->year;

            $comprehensiveData = $this->multiBrandPlatformService->getComprehensiveVehicleData(
                $vehicleId, $make, $model, $year
            );

            return response()->json([
                'success' => true,
                'data' => $comprehensiveData
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get comprehensive vehicle data', [
                'user_id' => Auth::id(),
                'vehicle_id' => $request->vehicle_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve comprehensive vehicle data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get supported platforms
     */
    public function getSupportedPlatforms(): JsonResponse
    {
        try {
            $platforms = $this->multiBrandPlatformService->getSupportedPlatforms();
            $status = $this->multiBrandPlatformService->getStatus();

            $platformInfo = [];
            foreach ($platforms as $platform) {
                $config = $this->multiBrandPlatformService->getPlatformConfig($platform);
                $platformInfo[] = [
                    'name' => $platform,
                    'display_name' => ucwords(str_replace('_', ' ', $platform)),
                    'enabled' => $status[$platform]['enabled'] ?? false,
                    'configured' => $status[$platform]['configured'] ?? false,
                    'api_url' => $status[$platform]['base_url'] ?? '',
                    'supported_brands' => $status[$platform]['supported_brands'] ?? []
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'platforms' => $platformInfo,
                    'total_count' => count($platforms)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get supported platforms', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve supported platforms',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test platform API connection
     */
    public function testPlatformAPI(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required|string|in:smartcar,high_mobility,otonomo,wejo,motordata,carapi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $platform = $request->platform;
            $result = $this->multiBrandPlatformService->testPlatformAPI($platform);

            return response()->json([
                'success' => true,
                'data' => [
                    'platform' => $platform,
                    'connection_test' => $result ? 'success' : 'failed',
                    'status' => $result ? 'API connection successful' : 'API connection failed'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to test platform API', [
                'user_id' => Auth::id(),
                'platform' => $request->platform,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to test platform API',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get platform API status
     */
    public function getAPIStatus(): JsonResponse
    {
        try {
            $status = $this->multiBrandPlatformService->getStatus();

            return response()->json([
                'success' => true,
                'data' => [
                    'platforms' => $status,
                    'total_platforms' => count($status),
                    'enabled_platforms' => count(array_filter($status, fn($s) => $s['enabled'])),
                    'configured_platforms' => count(array_filter($status, fn($s) => $s['configured']))
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get platform API status', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve platform API status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get platform API documentation
     */
    public function getAPIDocumentation(): JsonResponse
    {
        try {
            $documentation = [
                'smartcar' => [
                    'name' => 'Smartcar API',
                    'description' => 'Unified API for 25+ car brands with OAuth authentication',
                    'website' => 'https://smartcar.com',
                    'documentation_url' => 'https://smartcar.com/docs',
                    'supported_brands' => [
                        'BMW', 'Ford', 'Volkswagen', 'Tesla', 'Toyota', 'Hyundai', 'Kia', 'Genesis',
                        'Mercedes-Benz', 'Audi', 'Porsche', 'Volvo', 'Nissan', 'Honda', 'Mazda',
                        'Subaru', 'Mitsubishi', 'Jaguar', 'Land Rover', 'Mini', 'Fiat', 'Chrysler',
                        'Dodge', 'Jeep', 'Ram', 'Chevrolet', 'GMC', 'Cadillac', 'Buick', 'Lincoln'
                    ],
                    'authentication' => 'OAuth 2.0',
                    'rate_limits' => '1000 requests/hour',
                    'features' => [
                        'Vehicle data access',
                        'Battery and fuel levels',
                        'Location tracking',
                        'Odometer readings',
                        'Tire pressure monitoring',
                        'Remote vehicle control'
                    ]
                ],
                'high_mobility' => [
                    'name' => 'High Mobility API',
                    'description' => 'Connected car platform with sandbox for testing',
                    'website' => 'https://www.high-mobility.com',
                    'documentation_url' => 'https://developers.high-mobility.com',
                    'supported_brands' => [
                        'BMW', 'Audi', 'Mercedes-Benz', 'Porsche', 'Toyota', 'Volkswagen', 'Ford',
                        'Hyundai', 'Kia', 'Genesis', 'Nissan', 'Honda', 'Mazda', 'Subaru'
                    ],
                    'authentication' => 'API Key',
                    'rate_limits' => '500 requests/hour',
                    'features' => [
                        'Vehicle data access',
                        'Diagnostic information',
                        'Maintenance scheduling',
                        'Real-time status updates',
                        'Sandbox environment'
                    ]
                ],
                'otonomo' => [
                    'name' => 'Otonomo API',
                    'description' => 'Data-as-a-Service for vehicle telemetry and fleet management',
                    'website' => 'https://otonomo.io',
                    'documentation_url' => 'https://docs.otonomo.io',
                    'supported_brands' => [
                        'BMW', 'Mercedes-Benz', 'Volkswagen', 'Audi', 'Ford', 'Toyota', 'Hyundai',
                        'Kia', 'Genesis', 'Nissan', 'Honda', 'Mazda', 'Subaru', 'Mitsubishi'
                    ],
                    'authentication' => 'API Key',
                    'rate_limits' => '2000 requests/hour',
                    'features' => [
                        'Fleet data management',
                        'Vehicle telemetry',
                        'Diagnostic information',
                        'Maintenance scheduling',
                        'Analytics and insights'
                    ]
                ],
                'wejo' => [
                    'name' => 'Wejo API',
                    'description' => 'Big data platform for connected vehicles on real roads',
                    'website' => 'https://www.wejo.com',
                    'documentation_url' => 'https://developers.wejo.com',
                    'supported_brands' => [
                        'BMW', 'Mercedes-Benz', 'Volkswagen', 'Audi', 'Ford', 'Toyota', 'Hyundai',
                        'Kia', 'Genesis', 'Nissan', 'Honda', 'Mazda', 'Subaru', 'Mitsubishi',
                        'Jaguar', 'Land Rover', 'Mini', 'Fiat', 'Chrysler', 'Dodge', 'Jeep'
                    ],
                    'authentication' => 'API Key',
                    'rate_limits' => '1500 requests/hour',
                    'features' => [
                        'Connected vehicle data',
                        'Real-time telemetry',
                        'Diagnostic information',
                        'Analytics and insights',
                        'Big data processing'
                    ]
                ],
                'motordata' => [
                    'name' => 'MotorData API',
                    'description' => 'Multi-brand diagnostics with DTC codes and repair information',
                    'website' => 'https://motordata.net',
                    'documentation_url' => 'https://docs.motordata.net',
                    'supported_brands' => [
                        'BMW', 'Mercedes-Benz', 'Volkswagen', 'Audi', 'Ford', 'Toyota', 'Hyundai',
                        'Kia', 'Genesis', 'Nissan', 'Honda', 'Mazda', 'Subaru', 'Mitsubishi',
                        'Jaguar', 'Land Rover', 'Mini', 'Fiat', 'Chrysler', 'Dodge', 'Jeep',
                        'Chevrolet', 'GMC', 'Cadillac', 'Buick', 'Lincoln', 'Tesla', 'Volvo'
                    ],
                    'authentication' => 'API Key',
                    'rate_limits' => '1000 requests/hour',
                    'features' => [
                        'Diagnostic trouble codes (DTC)',
                        'Repair information',
                        'Vehicle specifications',
                        'Maintenance schedules',
                        'Recall information'
                    ]
                ],
                'carapi' => [
                    'name' => 'CarAPI.app',
                    'description' => 'Multi-brand API for vehicle specifications and diagnostics',
                    'website' => 'https://carapi.app',
                    'documentation_url' => 'https://docs.carapi.app',
                    'supported_brands' => [
                        'BMW', 'Mercedes-Benz', 'Volkswagen', 'Audi', 'Ford', 'Toyota', 'Hyundai',
                        'Kia', 'Genesis', 'Nissan', 'Honda', 'Mazda', 'Subaru', 'Mitsubishi',
                        'Jaguar', 'Land Rover', 'Mini', 'Fiat', 'Chrysler', 'Dodge', 'Jeep',
                        'Chevrolet', 'GMC', 'Cadillac', 'Buick', 'Lincoln', 'Tesla', 'Volvo',
                        'Porsche', 'Ferrari', 'Lamborghini', 'Maserati', 'Bentley', 'Rolls-Royce'
                    ],
                    'authentication' => 'API Key',
                    'rate_limits' => '1000 requests/hour',
                    'features' => [
                        'Vehicle specifications',
                        'Make, model, year data',
                        'Engine information',
                        'Body styles and colors',
                        'Diagnostic information',
                        'Maintenance data'
                    ]
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'documentation' => $documentation,
                    'total_platforms' => count($documentation),
                    'total_supported_brands' => array_sum(array_map(fn($d) => count($d['supported_brands']), $documentation))
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get platform API documentation', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve platform API documentation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MultiBrandPlatformAPIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MultiBrandPlatformController extends Controller
{
    private $multiBrandPlatformService;

    public function __construct(MultiBrandPlatformAPIService $multiBrandPlatformService)
    {
        $this->multiBrandPlatformService = $multiBrandPlatformService;
    }

    /**
     * Get vehicle data from Smartcar
     */
    public function getSmartcarVehicleData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|string',
            'access_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $vehicleId = $request->vehicle_id;
            $accessToken = $request->access_token;

            $vehicleData = $this->multiBrandPlatformService->getSmartcarVehicleData($vehicleId, $accessToken);

            return response()->json([
                'success' => true,
                'data' => $vehicleData
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get Smartcar vehicle data', [
                'user_id' => Auth::id(),
                'vehicle_id' => $request->vehicle_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Smartcar vehicle data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vehicle data from High Mobility
     */
    public function getHighMobilityVehicleData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $vehicleId = $request->vehicle_id;

            $vehicleData = $this->multiBrandPlatformService->getHighMobilityVehicleData($vehicleId);

            return response()->json([
                'success' => true,
                'data' => $vehicleData
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get High Mobility vehicle data', [
                'user_id' => Auth::id(),
                'vehicle_id' => $request->vehicle_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve High Mobility vehicle data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vehicle data from Otonomo
     */
    public function getOtonomoVehicleData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $vehicleId = $request->vehicle_id;

            $vehicleData = $this->multiBrandPlatformService->getOtonomoVehicleData($vehicleId);

            return response()->json([
                'success' => true,
                'data' => $vehicleData
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get Otonomo vehicle data', [
                'user_id' => Auth::id(),
                'vehicle_id' => $request->vehicle_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Otonomo vehicle data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vehicle data from Wejo
     */
    public function getWejoVehicleData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $vehicleId = $request->vehicle_id;

            $vehicleData = $this->multiBrandPlatformService->getWejoVehicleData($vehicleId);

            return response()->json([
                'success' => true,
                'data' => $vehicleData
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get Wejo vehicle data', [
                'user_id' => Auth::id(),
                'vehicle_id' => $request->vehicle_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Wejo vehicle data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get diagnostic data from MotorData
     */
    public function getMotorDataDiagnostics(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vin' => 'required|string|size:17',
            'dtc_code' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $vin = $request->vin;
            $dtcCode = $request->dtc_code;

            $diagnostics = $this->multiBrandPlatformService->getMotorDataDiagnostics($vin, $dtcCode);

            return response()->json([
                'success' => true,
                'data' => $diagnostics
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get MotorData diagnostics', [
                'user_id' => Auth::id(),
                'vin' => $request->vin,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve MotorData diagnostics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vehicle data from CarAPI.app
     */
    public function getCarAPIVehicleData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'make' => 'required|string',
            'model' => 'required|string',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $make = $request->make;
            $model = $request->model;
            $year = $request->year;

            $vehicleData = $this->multiBrandPlatformService->getCarAPIVehicleData($make, $model, $year);

            return response()->json([
                'success' => true,
                'data' => $vehicleData
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get CarAPI vehicle data', [
                'user_id' => Auth::id(),
                'make' => $request->make,
                'model' => $request->model,
                'year' => $request->year,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve CarAPI vehicle data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comprehensive vehicle data from all available platforms
     */
    public function getComprehensiveVehicleData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|string',
            'make' => 'nullable|string',
            'model' => 'nullable|string',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $vehicleId = $request->vehicle_id;
            $make = $request->make;
            $model = $request->model;
            $year = $request->year;

            $comprehensiveData = $this->multiBrandPlatformService->getComprehensiveVehicleData(
                $vehicleId, $make, $model, $year
            );

            return response()->json([
                'success' => true,
                'data' => $comprehensiveData
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get comprehensive vehicle data', [
                'user_id' => Auth::id(),
                'vehicle_id' => $request->vehicle_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve comprehensive vehicle data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get supported platforms
     */
    public function getSupportedPlatforms(): JsonResponse
    {
        try {
            $platforms = $this->multiBrandPlatformService->getSupportedPlatforms();
            $status = $this->multiBrandPlatformService->getStatus();

            $platformInfo = [];
            foreach ($platforms as $platform) {
                $config = $this->multiBrandPlatformService->getPlatformConfig($platform);
                $platformInfo[] = [
                    'name' => $platform,
                    'display_name' => ucwords(str_replace('_', ' ', $platform)),
                    'enabled' => $status[$platform]['enabled'] ?? false,
                    'configured' => $status[$platform]['configured'] ?? false,
                    'api_url' => $status[$platform]['base_url'] ?? '',
                    'supported_brands' => $status[$platform]['supported_brands'] ?? []
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'platforms' => $platformInfo,
                    'total_count' => count($platforms)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get supported platforms', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve supported platforms',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test platform API connection
     */
    public function testPlatformAPI(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required|string|in:smartcar,high_mobility,otonomo,wejo,motordata,carapi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $platform = $request->platform;
            $result = $this->multiBrandPlatformService->testPlatformAPI($platform);

            return response()->json([
                'success' => true,
                'data' => [
                    'platform' => $platform,
                    'connection_test' => $result ? 'success' : 'failed',
                    'status' => $result ? 'API connection successful' : 'API connection failed'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to test platform API', [
                'user_id' => Auth::id(),
                'platform' => $request->platform,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to test platform API',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get platform API status
     */
    public function getAPIStatus(): JsonResponse
    {
        try {
            $status = $this->multiBrandPlatformService->getStatus();

            return response()->json([
                'success' => true,
                'data' => [
                    'platforms' => $status,
                    'total_platforms' => count($status),
                    'enabled_platforms' => count(array_filter($status, fn($s) => $s['enabled'])),
                    'configured_platforms' => count(array_filter($status, fn($s) => $s['configured']))
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get platform API status', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve platform API status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get platform API documentation
     */
    public function getAPIDocumentation(): JsonResponse
    {
        try {
            $documentation = [
                'smartcar' => [
                    'name' => 'Smartcar API',
                    'description' => 'Unified API for 25+ car brands with OAuth authentication',
                    'website' => 'https://smartcar.com',
                    'documentation_url' => 'https://smartcar.com/docs',
                    'supported_brands' => [
                        'BMW', 'Ford', 'Volkswagen', 'Tesla', 'Toyota', 'Hyundai', 'Kia', 'Genesis',
                        'Mercedes-Benz', 'Audi', 'Porsche', 'Volvo', 'Nissan', 'Honda', 'Mazda',
                        'Subaru', 'Mitsubishi', 'Jaguar', 'Land Rover', 'Mini', 'Fiat', 'Chrysler',
                        'Dodge', 'Jeep', 'Ram', 'Chevrolet', 'GMC', 'Cadillac', 'Buick', 'Lincoln'
                    ],
                    'authentication' => 'OAuth 2.0',
                    'rate_limits' => '1000 requests/hour',
                    'features' => [
                        'Vehicle data access',
                        'Battery and fuel levels',
                        'Location tracking',
                        'Odometer readings',
                        'Tire pressure monitoring',
                        'Remote vehicle control'
                    ]
                ],
                'high_mobility' => [
                    'name' => 'High Mobility API',
                    'description' => 'Connected car platform with sandbox for testing',
                    'website' => 'https://www.high-mobility.com',
                    'documentation_url' => 'https://developers.high-mobility.com',
                    'supported_brands' => [
                        'BMW', 'Audi', 'Mercedes-Benz', 'Porsche', 'Toyota', 'Volkswagen', 'Ford',
                        'Hyundai', 'Kia', 'Genesis', 'Nissan', 'Honda', 'Mazda', 'Subaru'
                    ],
                    'authentication' => 'API Key',
                    'rate_limits' => '500 requests/hour',
                    'features' => [
                        'Vehicle data access',
                        'Diagnostic information',
                        'Maintenance scheduling',
                        'Real-time status updates',
                        'Sandbox environment'
                    ]
                ],
                'otonomo' => [
                    'name' => 'Otonomo API',
                    'description' => 'Data-as-a-Service for vehicle telemetry and fleet management',
                    'website' => 'https://otonomo.io',
                    'documentation_url' => 'https://docs.otonomo.io',
                    'supported_brands' => [
                        'BMW', 'Mercedes-Benz', 'Volkswagen', 'Audi', 'Ford', 'Toyota', 'Hyundai',
                        'Kia', 'Genesis', 'Nissan', 'Honda', 'Mazda', 'Subaru', 'Mitsubishi'
                    ],
                    'authentication' => 'API Key',
                    'rate_limits' => '2000 requests/hour',
                    'features' => [
                        'Fleet data management',
                        'Vehicle telemetry',
                        'Diagnostic information',
                        'Maintenance scheduling',
                        'Analytics and insights'
                    ]
                ],
                'wejo' => [
                    'name' => 'Wejo API',
                    'description' => 'Big data platform for connected vehicles on real roads',
                    'website' => 'https://www.wejo.com',
                    'documentation_url' => 'https://developers.wejo.com',
                    'supported_brands' => [
                        'BMW', 'Mercedes-Benz', 'Volkswagen', 'Audi', 'Ford', 'Toyota', 'Hyundai',
                        'Kia', 'Genesis', 'Nissan', 'Honda', 'Mazda', 'Subaru', 'Mitsubishi',
                        'Jaguar', 'Land Rover', 'Mini', 'Fiat', 'Chrysler', 'Dodge', 'Jeep'
                    ],
                    'authentication' => 'API Key',
                    'rate_limits' => '1500 requests/hour',
                    'features' => [
                        'Connected vehicle data',
                        'Real-time telemetry',
                        'Diagnostic information',
                        'Analytics and insights',
                        'Big data processing'
                    ]
                ],
                'motordata' => [
                    'name' => 'MotorData API',
                    'description' => 'Multi-brand diagnostics with DTC codes and repair information',
                    'website' => 'https://motordata.net',
                    'documentation_url' => 'https://docs.motordata.net',
                    'supported_brands' => [
                        'BMW', 'Mercedes-Benz', 'Volkswagen', 'Audi', 'Ford', 'Toyota', 'Hyundai',
                        'Kia', 'Genesis', 'Nissan', 'Honda', 'Mazda', 'Subaru', 'Mitsubishi',
                        'Jaguar', 'Land Rover', 'Mini', 'Fiat', 'Chrysler', 'Dodge', 'Jeep',
                        'Chevrolet', 'GMC', 'Cadillac', 'Buick', 'Lincoln', 'Tesla', 'Volvo'
                    ],
                    'authentication' => 'API Key',
                    'rate_limits' => '1000 requests/hour',
                    'features' => [
                        'Diagnostic trouble codes (DTC)',
                        'Repair information',
                        'Vehicle specifications',
                        'Maintenance schedules',
                        'Recall information'
                    ]
                ],
                'carapi' => [
                    'name' => 'CarAPI.app',
                    'description' => 'Multi-brand API for vehicle specifications and diagnostics',
                    'website' => 'https://carapi.app',
                    'documentation_url' => 'https://docs.carapi.app',
                    'supported_brands' => [
                        'BMW', 'Mercedes-Benz', 'Volkswagen', 'Audi', 'Ford', 'Toyota', 'Hyundai',
                        'Kia', 'Genesis', 'Nissan', 'Honda', 'Mazda', 'Subaru', 'Mitsubishi',
                        'Jaguar', 'Land Rover', 'Mini', 'Fiat', 'Chrysler', 'Dodge', 'Jeep',
                        'Chevrolet', 'GMC', 'Cadillac', 'Buick', 'Lincoln', 'Tesla', 'Volvo',
                        'Porsche', 'Ferrari', 'Lamborghini', 'Maserati', 'Bentley', 'Rolls-Royce'
                    ],
                    'authentication' => 'API Key',
                    'rate_limits' => '1000 requests/hour',
                    'features' => [
                        'Vehicle specifications',
                        'Make, model, year data',
                        'Engine information',
                        'Body styles and colors',
                        'Diagnostic information',
                        'Maintenance data'
                    ]
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'documentation' => $documentation,
                    'total_platforms' => count($documentation),
                    'total_supported_brands' => array_sum(array_map(fn($d) => count($d['supported_brands']), $documentation))
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get platform API documentation', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve platform API documentation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}














