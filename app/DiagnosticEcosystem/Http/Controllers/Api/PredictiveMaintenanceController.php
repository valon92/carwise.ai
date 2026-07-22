<?php

namespace App\DiagnosticEcosystem\Http\Controllers\Api;

use App\DiagnosticEcosystem\Models\DeMaintenanceRecommendation;
use App\DiagnosticEcosystem\Models\DeVehicleProfile;
use App\DiagnosticEcosystem\Services\PredictiveMaintenanceService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class PredictiveMaintenanceController extends Controller
{
    public function __construct(
        private readonly PredictiveMaintenanceService $predictiveMaintenanceService,
    ) {}

    public function index(Request $request, int $vehicleId): JsonResponse
    {
        $profile = DeVehicleProfile::where('user_id', $request->user()->id)->findOrFail($vehicleId);

        return response()->json([
            'success' => true,
            'data' => $this->predictiveMaintenanceService->listForVehicle($profile, $request->query('status')),
        ]);
    }

    public function generate(Request $request, int $vehicleId): JsonResponse
    {
        $profile = DeVehicleProfile::where('user_id', $request->user()->id)->findOrFail($vehicleId);

        try {
            $items = $this->predictiveMaintenanceService->generateAndPersist($profile);

            return response()->json([
                'success' => true,
                'data' => $items,
                'message' => 'Predictive maintenance recommendations generated.',
            ]);
        } catch (RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 503);
        }
    }

    public function update(Request $request, int $recommendationId): JsonResponse
    {
        $recommendation = DeMaintenanceRecommendation::whereHas('vehicleProfile', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->findOrFail($recommendationId);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,scheduled,completed,dismissed'],
        ]);

        try {
            $updated = $this->predictiveMaintenanceService->updateStatus($recommendation, $validated['status']);

            return response()->json([
                'success' => true,
                'data' => $updated,
                'message' => 'Recommendation updated.',
            ]);
        } catch (RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
