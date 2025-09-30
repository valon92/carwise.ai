<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CarController extends Controller
{
    /**
     * Get all cars for the authenticated user.
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Direct database query (cache disabled due to Redis configuration issues)
            $cars = Car::where('user_id', $user->id)->orderBy('created_at', 'desc')->get()
                ->map(function($car) {
                    return [
                        'id' => $car->id,
                        'brand' => $car->brand,
                        'model' => $car->model,
                        'year' => $car->year,
                        'vin' => $car->vin,
                        'image_url' => $car->getImageUrl(),
                        'thumbnail_url' => $car->getThumbnailUrl(),
                        'license_plate' => $car->license_plate,
                        'color' => $car->color,
                        'fuel_type' => $car->fuel_type,
                        'transmission' => $car->transmission,
                        'mileage' => $car->mileage,
                        'purchase_date' => $car->purchase_date?->format('Y-m-d'),
                        'purchase_price' => $car->purchase_price,
                        'notes' => $car->notes,
                        'specifications' => $car->specifications,
                        'status' => $car->status,
                        'full_name' => $car->full_name,
                        'display_name' => $car->display_name,
                        'age' => $car->age,
                        'diagnosis_count' => $car->diagnosisSessions()->count(),
                        'last_diagnosis' => $car->diagnosisSessions()->latest()->first()?->created_at?->format('Y-m-d') ?? 'Never',
                        'recent_diagnoses' => $car->diagnosisSessions()->latest()->limit(3)->get()->map(function($session) {
                            return [
                                'id' => $session->id,
                                'session_id' => $session->session_id,
                                'status' => $session->status,
                                'created_at' => $session->created_at->format('Y-m-d H:i'),
                                'vehicle_info' => $session->vehicle_info
                            ];
                        }),
                        // Maintenance data
                        'current_mileage' => $car->current_mileage,
                        'maintenance_status' => $car->getMaintenanceStatusColor(),
                        'next_maintenance_due' => $car->getNextMaintenanceDue(),
                        'created_at' => $car->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $car->updated_at->format('Y-m-d H:i:s'),
                    ];
                });

            return response()->json([
                'success' => true,
                'cars' => $cars,
                'total' => $cars->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cars',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific car by ID.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $car = Car::where('user_id', $user->id)
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'car' => [
                    'id' => $car->id,
                    'brand' => $car->brand,
                    'model' => $car->model,
                    'year' => $car->year,
                    'vin' => $car->vin,
                    'license_plate' => $car->license_plate,
                    'color' => $car->color,
                    'fuel_type' => $car->fuel_type,
                    'transmission' => $car->transmission,
                    'mileage' => $car->mileage,
                    'purchase_date' => $car->purchase_date?->format('Y-m-d'),
                    'purchase_price' => $car->purchase_price,
                    'notes' => $car->notes,
                    'specifications' => $car->specifications,
                    'maintenance_history' => $car->maintenance_history,
                    'status' => $car->status,
                    'full_name' => $car->full_name,
                    'display_name' => $car->display_name,
                    'age' => $car->age,
                    'diagnosis_sessions' => $car->diagnosisSessions()->with('result')->orderBy('created_at', 'desc')->get()->map(function($session) {
                        return [
                            'id' => $session->id,
                            'session_id' => $session->session_id,
                            'status' => $session->status,
                            'description' => $session->description,
                            'symptoms' => $session->symptoms,
                            'vehicle_info' => $session->vehicle_info,
                            'created_at' => $session->created_at->format('Y-m-d H:i'),
                            'has_result' => $session->result !== null
                        ];
                    }),
                    'created_at' => $car->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $car->updated_at->format('Y-m-d H:i:s'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Car not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Create a new car.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'brand' => 'required|string|max:100',
                'model' => 'required|string|max:100',
                'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'vin' => 'nullable|string|max:17|unique:cars,vin',
                'license_plate' => 'nullable|string|max:20',
                'color' => 'nullable|string|max:50',
                'fuel_type' => 'nullable|string|in:gasoline,diesel,electric,hybrid',
                'transmission' => 'nullable|string|in:manual,automatic,cvt',
                'mileage' => 'nullable|integer|min:0',
                'current_mileage' => 'nullable|integer|min:0',
                'purchase_date' => 'nullable|date|before_or_equal:today',
                'purchase_price' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string|max:1000',
                'specifications' => 'nullable|array',
                'status' => 'nullable|string|in:active,sold,scrapped',
                // Maintenance preferences
                'oil_change_interval' => 'nullable|integer|min:5000|max:50000',
                'notification_advance_days' => 'nullable|integer|min:1|max:365',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = Auth::user();
            $carData = $request->only([
                'brand', 'model', 'year', 'vin', 'license_plate', 'color',
                'fuel_type', 'transmission', 'mileage', 'current_mileage', 'purchase_date',
                'purchase_price', 'notes', 'specifications', 'status',
                'oil_change_interval', 'notification_advance_days'
            ]);
            $carData['user_id'] = $user->id;
            $carData['status'] = $carData['status'] ?? 'active';

            $car = Car::create($carData);

            // Note: Cache clearing disabled due to Redis configuration issues
            // CacheService::clearUserCache($user->id);

            return response()->json([
                'success' => true,
                'message' => 'Car created successfully',
                'car' => [
                    'id' => $car->id,
                    'brand' => $car->brand,
                    'model' => $car->model,
                    'year' => $car->year,
                    'vin' => $car->vin,
                    'license_plate' => $car->license_plate,
                    'color' => $car->color,
                    'fuel_type' => $car->fuel_type,
                    'transmission' => $car->transmission,
                    'mileage' => $car->mileage,
                    'purchase_date' => $car->purchase_date?->format('Y-m-d'),
                    'purchase_price' => $car->purchase_price,
                    'notes' => $car->notes,
                    'specifications' => $car->specifications,
                    'status' => $car->status,
                    'full_name' => $car->full_name,
                    'display_name' => $car->display_name,
                    'age' => $car->age,
                    'diagnosis_count' => 0,
                    'last_diagnosis' => 'Never',
                    'recent_diagnoses' => [],
                    'created_at' => $car->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $car->updated_at->format('Y-m-d H:i:s'),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create car',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing car.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $car = Car::where('user_id', $user->id)->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'brand' => 'sometimes|required|string|max:100',
                'model' => 'sometimes|required|string|max:100',
                'year' => 'sometimes|required|integer|min:1900|max:' . (date('Y') + 1),
                'vin' => 'nullable|string|max:17|unique:cars,vin,' . $id,
                'license_plate' => 'nullable|string|max:20',
                'color' => 'nullable|string|max:50',
                'fuel_type' => 'nullable|string|in:gasoline,diesel,electric,hybrid',
                'transmission' => 'nullable|string|in:manual,automatic,cvt',
                'mileage' => 'nullable|integer|min:0',
                'current_mileage' => 'nullable|integer|min:0',
                'purchase_date' => 'nullable|date|before_or_equal:today',
                'purchase_price' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string|max:1000',
                'specifications' => 'nullable|array',
                'status' => 'nullable|string|in:active,sold,scrapped',
                // Maintenance preferences
                'oil_change_interval' => 'nullable|integer|min:5000|max:50000',
                'notification_advance_days' => 'nullable|integer|min:1|max:365',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $carData = $request->only([
                'brand', 'model', 'year', 'vin', 'license_plate', 'color',
                'fuel_type', 'transmission', 'mileage', 'current_mileage', 'purchase_date',
                'purchase_price', 'notes', 'specifications', 'status',
                'oil_change_interval', 'notification_advance_days'
            ]);

            $car->update($carData);

            // Note: Cache clearing disabled due to Redis configuration issues
            // CacheService::clearUserCache($user->id);

            return response()->json([
                'success' => true,
                'message' => 'Car updated successfully',
                'car' => [
                    'id' => $car->id,
                    'brand' => $car->brand,
                    'model' => $car->model,
                    'year' => $car->year,
                    'vin' => $car->vin,
                    'license_plate' => $car->license_plate,
                    'color' => $car->color,
                    'fuel_type' => $car->fuel_type,
                    'transmission' => $car->transmission,
                    'mileage' => $car->mileage,
                    'purchase_date' => $car->purchase_date?->format('Y-m-d'),
                    'purchase_price' => $car->purchase_price,
                    'notes' => $car->notes,
                    'specifications' => $car->specifications,
                    'status' => $car->status,
                    'full_name' => $car->full_name,
                    'display_name' => $car->display_name,
                    'age' => $car->age,
                    'diagnosis_count' => $car->diagnosisSessions()->count(),
                    'last_diagnosis' => $car->diagnosisSessions()->latest()->first()?->created_at?->format('Y-m-d') ?? 'Never',
                    'recent_diagnoses' => $car->diagnosisSessions()->latest()->limit(3)->get()->map(function($session) {
                        return [
                            'id' => $session->id,
                            'session_id' => $session->session_id,
                            'status' => $session->status,
                            'created_at' => $session->created_at->format('Y-m-d H:i'),
                            'vehicle_info' => $session->vehicle_info
                        ];
                    }),
                    'created_at' => $car->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $car->updated_at->format('Y-m-d H:i:s'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update car',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a car.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $car = Car::where('user_id', $user->id)->findOrFail($id);

            // Check if car has diagnosis sessions
            if ($car->diagnosisSessions()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete car with existing diagnosis sessions. Please contact support.',
                ], 422);
            }

            $car->delete();

            // Note: Cache clearing disabled due to Redis configuration issues  
            // CacheService::clearUserCache($user->id);

            return response()->json([
                'success' => true,
                'message' => 'Car deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete car',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get car statistics for the authenticated user.
     */
    public function statistics(): JsonResponse
    {
        try {
            $user = Auth::user();
            $cars = Car::where('user_id', $user->id);

            // Get cached basic statistics
            $baseStats = CacheService::getUserStatistics($user->id);
            
            $stats = [
                'total_cars' => $baseStats['total_cars'],
                'active_cars' => $cars->where('status', 'active')->count(),
                'total_diagnoses' => $baseStats['total_diagnoses'],
                'brands' => $cars->selectRaw('brand, COUNT(*) as count')
                    ->groupBy('brand')
                    ->orderBy('count', 'desc')
                    ->get(),
                'fuel_types' => $cars->selectRaw('fuel_type, COUNT(*) as count')
                    ->whereNotNull('fuel_type')
                    ->groupBy('fuel_type')
                    ->get(),
                'average_age' => $cars->selectRaw('AVG(YEAR(CURDATE()) - year) as avg_age')
                    ->value('avg_age'),
            ];

            return response()->json([
                'success' => true,
                'statistics' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}