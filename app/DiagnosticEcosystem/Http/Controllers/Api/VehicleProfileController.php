<?php

namespace App\DiagnosticEcosystem\Http\Controllers\Api;

use App\DiagnosticEcosystem\Models\DeVehicleProfile;
use App\DiagnosticEcosystem\Services\VehicleRegistrationService;
use App\DiagnosticEcosystem\Services\VinIdentificationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleProfileController extends Controller
{
    public function __construct(
        private readonly VehicleRegistrationService $registrationService,
        private readonly VinIdentificationService $vinIdentificationService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $vehicles = $this->registrationService->listForUser($request->user()->id);

        return response()->json([
            'success' => true,
            'data' => $vehicles,
            'message' => $vehicles->isEmpty()
                ? 'No vehicles registered yet. Add your first vehicle with its VIN.'
                : null,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vin' => ['required', 'string', 'size:17'],
            'license_plate' => ['nullable', 'string', 'max:20'],
            'nickname' => ['nullable', 'string', 'max:100'],
            'current_mileage' => ['nullable', 'integer', 'min:0', 'max:9999999'],
            'legacy_car_id' => ['nullable', 'integer'],
        ]);

        if (! $this->registrationService->isValidVin($validated['vin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid VIN format. A VIN must be exactly 17 characters (A-Z, 0-9, excluding I, O, Q).',
            ], 422);
        }

        $exists = DeVehicleProfile::where('user_id', $request->user()->id)
            ->where('vin', strtoupper(trim($validated['vin'])))
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This VIN is already registered in your profile.',
            ], 422);
        }

        if (isset($validated['legacy_car_id'])) {
            $ownsCar = $request->user()->cars()->where('id', $validated['legacy_car_id'])->exists();
            if (! $ownsCar) {
                return response()->json([
                    'success' => false,
                    'message' => 'The linked car does not belong to your account.',
                ], 403);
            }
        }

        $profile = $this->registrationService->register(
            $request->user()->id,
            $validated,
        );

        if (config('diagnostic-ecosystem.vin_identification')) {
            $this->vinIdentificationService->identifyProfile($profile);
            $profile = $profile->fresh('latestVinDecode');
        }

        return response()->json([
            'success' => true,
            'data' => $profile,
            'message' => 'Vehicle registered successfully.',
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $profile = DeVehicleProfile::where('user_id', $request->user()->id)
            ->with('latestVinDecode')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $profile,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $profile = DeVehicleProfile::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'license_plate' => ['nullable', 'string', 'max:20'],
            'nickname' => ['nullable', 'string', 'max:100'],
            'current_mileage' => ['nullable', 'integer', 'min:0', 'max:9999999'],
        ]);

        $profile = $this->registrationService->update($profile, $validated);

        return response()->json([
            'success' => true,
            'data' => $profile,
            'message' => 'Vehicle profile updated.',
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $profile = DeVehicleProfile::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $this->registrationService->archive($profile);

        return response()->json([
            'success' => true,
            'message' => 'Vehicle archived. History is preserved.',
        ]);
    }
}
