<?php

namespace App\DiagnosticEcosystem\Http\Controllers\Api;

use App\DiagnosticEcosystem\Models\DeVehicleProfile;
use App\DiagnosticEcosystem\Services\VehicleRegistrationService;
use App\DiagnosticEcosystem\Services\VinIdentificationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VinIdentificationController extends Controller
{
    public function __construct(
        private readonly VinIdentificationService $vinIdentificationService,
        private readonly VehicleRegistrationService $registrationService,
    ) {}

    public function preview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vin' => ['required', 'string', 'size:17'],
        ]);

        if (! $this->registrationService->isValidVin($validated['vin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid VIN format.',
            ], 422);
        }

        $result = $this->vinIdentificationService->preview(strtoupper(trim($validated['vin'])));

        if (! $result) {
            return response()->json([
                'success' => false,
                'message' => 'No VIN data was returned by the configured providers.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $result->toArray(),
            'message' => 'VIN preview completed successfully.',
        ]);
    }

    public function identify(Request $request, int $id): JsonResponse
    {
        $profile = DeVehicleProfile::where('user_id', $request->user()->id)->findOrFail($id);
        $decode = $this->vinIdentificationService->identifyProfile($profile);

        if (! $decode) {
            return response()->json([
                'success' => false,
                'message' => 'VIN identification did not return any results.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $decode,
            'message' => 'VIN identified successfully.',
        ]);
    }

    public function history(Request $request, int $id): JsonResponse
    {
        $profile = DeVehicleProfile::where('user_id', $request->user()->id)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->vinIdentificationService->historyForProfile($profile),
        ]);
    }
}
