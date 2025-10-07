<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarMaintenanceHistory;
use App\Models\MaintenanceNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CarMaintenanceController extends Controller
{
    /**
     * Get maintenance history for a car.
     */
    public function getMaintenanceHistory(Request $request, int $carId): JsonResponse
    {
        $car = Car::where('id', $carId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Car not found'
            ], 404);
        }

        $maintenanceHistory = $car->maintenanceHistory()
            ->orderBy('service_date', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $maintenanceHistory
        ]);
    }

    /**
     * Add maintenance record.
     */
    public function addMaintenanceRecord(Request $request, int $carId): JsonResponse
    {
        $car = Car::where('id', $carId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Car not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'maintenance_type' => 'required|string|in:oil_change,tire_change,timing_belt,brake_pad,air_filter,fuel_filter,spark_plugs,battery,general_service,inspection',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'service_date' => 'required|date',
            'service_mileage' => 'required|integer|min:0',
            'cost' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'service_provider' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $maintenanceRecord = CarMaintenanceHistory::create([
            'car_id' => $car->id,
            'user_id' => Auth::id(),
            'maintenance_type' => $request->maintenance_type,
            'title' => $request->title,
            'description' => $request->description,
            'service_date' => $request->service_date,
            'service_mileage' => $request->service_mileage,
            'cost' => $request->cost,
            'currency' => $request->currency ?? 'USD',
            'service_provider' => $request->service_provider,
            'notes' => $request->notes,
            'status' => 'completed'
        ]);

        // Update car's maintenance tracking fields
        $this->updateCarMaintenanceFields($car, $request->maintenance_type, $request->service_date, $request->service_mileage);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance record added successfully',
            'data' => $maintenanceRecord
        ]);
    }

    /**
     * Update car's maintenance tracking fields.
     */
    private function updateCarMaintenanceFields(Car $car, string $maintenanceType, string $serviceDate, int $serviceMileage): void
    {
        $updateData = [
            'current_mileage' => $serviceMileage,
            'last_service_date' => $serviceDate,
            'last_service_mileage' => $serviceMileage
        ];

        switch ($maintenanceType) {
            case 'oil_change':
                $updateData['last_oil_change_date'] = $serviceDate;
                $updateData['last_oil_change_mileage'] = $serviceMileage;
                break;
            case 'tire_change':
                $updateData['last_tire_change_date'] = $serviceDate;
                $updateData['last_tire_change_mileage'] = $serviceMileage;
                break;
            case 'timing_belt':
                $updateData['last_timing_belt_change_date'] = $serviceDate;
                $updateData['last_timing_belt_change_mileage'] = $serviceMileage;
                break;
            case 'brake_pad':
                $updateData['last_brake_pad_change_date'] = $serviceDate;
                $updateData['last_brake_pad_change_mileage'] = $serviceMileage;
                break;
            case 'air_filter':
                $updateData['last_air_filter_change_date'] = $serviceDate;
                $updateData['last_air_filter_change_mileage'] = $serviceMileage;
                break;
            case 'fuel_filter':
                $updateData['last_fuel_filter_change_date'] = $serviceDate;
                $updateData['last_fuel_filter_change_mileage'] = $serviceMileage;
                break;
            case 'spark_plugs':
                $updateData['last_spark_plugs_change_date'] = $serviceDate;
                $updateData['last_spark_plugs_change_mileage'] = $serviceMileage;
                break;
            case 'battery':
                $updateData['battery_installation_date'] = $serviceDate;
                break;
        }

        $car->update($updateData);
    }

    /**
     * Get maintenance notifications for a car.
     */
    public function getMaintenanceNotifications(Request $request, int $carId): JsonResponse
    {
        $car = Car::where('id', $carId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Car not found'
            ], 404);
        }

        $notifications = $car->maintenanceNotifications()
            ->orderBy('due_date', 'asc')
            ->orderBy('priority', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    /**
     * Update car mileage.
     */
    public function updateMileage(Request $request, int $carId): JsonResponse
    {
        $car = Car::where('id', $carId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Car not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'current_mileage' => 'required|integer|min:0|gte:' . ($car->current_mileage ?? $car->mileage ?? 0)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $car->updateMileage($request->current_mileage);

        return response()->json([
            'success' => true,
            'message' => 'Mileage updated successfully',
            'data' => [
                'current_mileage' => $car->current_mileage,
                'maintenance_status' => $car->getMaintenanceStatusColor(),
                'next_maintenance_due' => $car->getNextMaintenanceDue()
            ]
        ]);
    }

    /**
     * Get maintenance statistics for a car.
     */
    public function getMaintenanceStats(Request $request, int $carId): JsonResponse
    {
        $car = Car::where('id', $carId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Car not found'
            ], 404);
        }

        $stats = $car->getMaintenanceStats();
        $notifications = $car->maintenanceNotifications()->get();

        return response()->json([
            'success' => true,
            'data' => [
                'maintenance_stats' => $stats,
                'notifications' => [
                    'total' => $notifications->count(),
                    'unread' => $notifications->where('is_read', false)->count(),
                    'overdue' => $notifications->where('due_date', '<', now()->toDateString())->count(),
                    'upcoming' => $notifications->where('due_date', '<=', now()->addDays(30)->toDateString())
                        ->where('due_date', '>=', now()->toDateString())->count()
                ],
                'maintenance_status' => $car->getMaintenanceStatusColor(),
                'next_maintenance_due' => $car->getNextMaintenanceDue()
            ]
        ]);
    }

    /**
     * Generate maintenance report PDF.
     */
    public function generateMaintenanceReport(Request $request, int $carId): JsonResponse
    {
        $car = Car::where('id', $carId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Car not found'
            ], 404);
        }

        $maintenanceHistory = $car->maintenanceHistory()
            ->orderBy('service_date', 'desc')
            ->get();

        // Generate PDF content
        $pdfContent = view('maintenance.report-pdf', [
            'car' => $car,
            'maintenanceHistory' => $maintenanceHistory,
            'generatedAt' => now()
        ])->render();

        // For now, return a simple response indicating the feature is available
        // In a real implementation, you would generate and return a PDF file
        return response()->json([
            'success' => true,
            'message' => 'Maintenance report generated successfully',
            'data' => [
                'car_name' => $car->display_name,
                'total_records' => $maintenanceHistory->count(),
                'report_date' => now()->toDateString()
            ]
        ]);
    }
}