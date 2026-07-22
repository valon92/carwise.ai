<?php

namespace App\DiagnosticEcosystem\Http\Controllers\Api;

use App\DiagnosticEcosystem\Models\DeVehicleProfile;
use App\DiagnosticEcosystem\Services\ConnectorPairingService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConnectorPairingController extends Controller
{
    public function __construct(
        private readonly ConnectorPairingService $connectorPairingService,
    ) {}

    public function show(Request $request, int $vehicleId): JsonResponse
    {
        $profile = DeVehicleProfile::where('user_id', $request->user()->id)->findOrFail($vehicleId);

        return response()->json([
            'success' => true,
            'data' => $this->connectorPairingService->getStatus($profile),
        ]);
    }

    public function pair(Request $request, int $vehicleId): JsonResponse
    {
        $profile = DeVehicleProfile::where('user_id', $request->user()->id)->findOrFail($vehicleId);
        $validated = $request->validate([
            'device_identifier' => ['nullable', 'string', 'max:255'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->connectorPairingService->pair($profile, $validated['device_identifier'] ?? null),
            'message' => 'Connector pairing architecture is prepared. Hardware communication will be added in a later phase.',
        ]);
    }

    public function destroy(Request $request, int $vehicleId): JsonResponse
    {
        $profile = DeVehicleProfile::where('user_id', $request->user()->id)->findOrFail($vehicleId);

        return response()->json([
            'success' => true,
            'data' => $this->connectorPairingService->revoke($profile),
            'message' => 'Connector pairing has been revoked.',
        ]);
    }
}
