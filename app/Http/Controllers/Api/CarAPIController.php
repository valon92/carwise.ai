<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CarAPIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CarAPIController extends Controller
{
    private $carAPIService;

    public function __construct(CarAPIService $carAPIService)
    {
        $this->carAPIService = $carAPIService;
    }

    /**
     * Get API status
     */
    public function getStatus(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->carAPIService->getStatus()
        ]);
    }

    /**
     * Get all available makes
     */
    public function getAllMakes(): JsonResponse
    {
        try {
            $makes = $this->carAPIService->getAllMakes();
            
            if ($makes === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'CarAPI is not available or not configured'
                ], 503);
            }

            return response()->json([
                'success' => true,
                'data' => $makes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve makes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get models for a specific make
     */
    public function getModelsByMake(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'make' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $models = $this->carAPIService->getModelsByMake($request->make);
            
            if ($models === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'CarAPI is not available or not configured'
                ], 503);
            }

            return response()->json([
                'success' => true,
                'data' => $models
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve models',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get years for a specific make and model
     */
    public function getYearsByMakeModel(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $years = $this->carAPIService->getYearsByMakeModel($request->make, $request->model);
            
            if ($years === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'CarAPI is not available or not configured'
                ], 503);
            }

            return response()->json([
                'success' => true,
                'data' => $years
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve years',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vehicle information
     */
    public function getVehicleInfo(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $vehicleInfo = $this->carAPIService->getVehicleInfo(
                $request->make,
                $request->model,
                $request->year
            );
            
            if ($vehicleInfo === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'CarAPI is not available or not configured'
                ], 503);
            }

            return response()->json([
                'success' => true,
                'data' => $vehicleInfo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve vehicle information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vehicle specifications
     */
    public function getVehicleSpecs(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $specs = $this->carAPIService->getVehicleSpecs(
                $request->make,
                $request->model,
                $request->year
            );
            
            if ($specs === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'CarAPI is not available or not configured'
                ], 503);
            }

            return response()->json([
                'success' => true,
                'data' => $specs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve vehicle specifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vehicle recalls
     */
    public function getVehicleRecalls(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $recalls = $this->carAPIService->getVehicleRecalls(
                $request->make,
                $request->model,
                $request->year
            );
            
            if ($recalls === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'CarAPI is not available or not configured'
                ], 503);
            }

            return response()->json([
                'success' => true,
                'data' => $recalls
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve vehicle recalls',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get maintenance schedule
     */
    public function getMaintenanceSchedule(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $maintenance = $this->carAPIService->getMaintenanceSchedule(
                $request->make,
                $request->model,
                $request->year
            );
            
            if ($maintenance === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'CarAPI is not available or not configured'
                ], 503);
            }

            return response()->json([
                'success' => true,
                'data' => $maintenance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve maintenance schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get compatible parts
     */
    public function getCompatibleParts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'part_type' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $parts = $this->carAPIService->getCompatibleParts(
                $request->make,
                $request->model,
                $request->year,
                $request->part_type
            );
            
            if ($parts === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'CarAPI is not available or not configured'
                ], 503);
            }

            return response()->json([
                'success' => true,
                'data' => $parts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve compatible parts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search vehicles by make
     */
    public function searchVehiclesByMake(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'make' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $vehicles = $this->carAPIService->searchVehiclesByMake($request->make);
            
            if ($vehicles === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'CarAPI is not available or not configured'
                ], 503);
            }

            return response()->json([
                'success' => true,
                'data' => $vehicles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search vehicles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get real car parts with stock, images, and prices
     */
    public function getRealCarParts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'make' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:50',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'limit' => 'nullable|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->carAPIService->getRealCarParts(
                $request->make,
                $request->model,
                $request->year,
                $request->get('limit', 50)
            );

            if ($result) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data'],
                    'total' => $result['total'],
                    'source' => $result['source']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No parts found'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching car parts: ' . $e->getMessage()
            ], 500);
        }
    }
}

