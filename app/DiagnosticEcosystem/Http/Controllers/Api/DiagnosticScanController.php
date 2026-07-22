<?php

namespace App\DiagnosticEcosystem\Http\Controllers\Api;

use App\DiagnosticEcosystem\Models\DeDiagnosticScan;
use App\DiagnosticEcosystem\Models\DeVehicleProfile;
use App\DiagnosticEcosystem\Services\ConnectorPairingService;
use App\DiagnosticEcosystem\Services\DiagnosticScanService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiagnosticScanController extends Controller
{
    public function __construct(
        private readonly DiagnosticScanService $diagnosticScanService,
        private readonly ConnectorPairingService $connectorPairingService,
    ) {}

    public function index(Request $request, int $vehicleId): JsonResponse
    {
        $profile = DeVehicleProfile::where('user_id', $request->user()->id)->findOrFail($vehicleId);

        return response()->json([
            'success' => true,
            'data' => $this->diagnosticScanService->listForVehicle($profile),
        ]);
    }

    public function manual(Request $request, int $vehicleId): JsonResponse
    {
        $profile = DeVehicleProfile::where('user_id', $request->user()->id)->findOrFail($vehicleId);

        $validated = $request->validate([
            'scan_date' => ['nullable', 'date'],
            'mileage' => ['nullable', 'integer', 'min:0', 'max:9999999'],
            'engine_dtcs' => ['nullable'],
            'abs_errors' => ['nullable'],
            'airbag_errors' => ['nullable'],
            'transmission_errors' => ['nullable'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $scan = $this->diagnosticScanService->createManualScan($profile, $validated);

        return response()->json([
            'success' => true,
            'data' => $scan,
            'message' => 'Manual diagnostic scan saved successfully.',
        ], 201);
    }

    public function store(Request $request, int $vehicleId): JsonResponse
    {
        $profile = DeVehicleProfile::where('user_id', $request->user()->id)->findOrFail($vehicleId);
        $pairing = $this->connectorPairingService->getOrCreateForVehicle($profile);
        $result = $this->diagnosticScanService->createConnectorScan($profile, $pairing);

        return response()->json($result, 501);
    }

    public function show(Request $request, int $scanId): JsonResponse
    {
        $scan = DeDiagnosticScan::whereHas('vehicleProfile', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->findOrFail($scanId);

        return response()->json([
            'success' => true,
            'data' => $scan,
        ]);
    }
}
