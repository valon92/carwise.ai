<?php

namespace App\DiagnosticEcosystem\Http\Controllers\Api;

use App\DiagnosticEcosystem\Models\DeVehicleProfile;
use App\DiagnosticEcosystem\Services\VehicleHistoryService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Throwable;

class VehicleHistoryController extends Controller
{
    public function __construct(
        private readonly VehicleHistoryService $vehicleHistoryService,
    ) {}

    public function index(Request $request, int $vehicleId): JsonResponse
    {
        $profile = DeVehicleProfile::where('user_id', $request->user()->id)->findOrFail($vehicleId);

        return response()->json([
            'success' => true,
            'data' => $this->vehicleHistoryService->recordsForVehicle($profile, [
                'event_type' => $request->query('event_type'),
            ]),
        ]);
    }

    public function store(Request $request, int $vehicleId): JsonResponse
    {
        $profile = DeVehicleProfile::where('user_id', $request->user()->id)->findOrFail($vehicleId);

        $validated = $request->validate([
            'event_type' => ['required', 'string', 'in:repair,part_replaced,service_note,mileage_update'],
            'title' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:2000'],
            'mileage' => ['nullable', 'integer', 'min:0', 'max:9999999'],
            'event_date' => ['nullable', 'date'],
        ]);

        try {
            $event = $this->vehicleHistoryService->createManualEvent($profile, $validated, $request->user()->id);

            return response()->json([
                'success' => true,
                'data' => $event,
                'message' => 'History event added.',
            ], 201);
        } catch (RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 503);
        }
    }

    public function exportJson(Request $request, int $vehicleId): JsonResponse
    {
        $profile = DeVehicleProfile::where('user_id', $request->user()->id)->findOrFail($vehicleId);

        return response()->json([
            'success' => true,
            'data' => $this->vehicleHistoryService->exportJson($profile),
        ]);
    }

    public function exportPdf(Request $request, int $vehicleId)
    {
        $profile = DeVehicleProfile::where('user_id', $request->user()->id)->findOrFail($vehicleId);

        try {
            return $this->vehicleHistoryService->exportPdf($profile);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'PDF export failed: '.$e->getMessage(),
            ], 500);
        }
    }
}
