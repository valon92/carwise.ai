<?php

namespace App\DiagnosticEcosystem\Http\Controllers\Api;

use App\DiagnosticEcosystem\Contracts\MarketplaceIntegrationInterface;
use App\DiagnosticEcosystem\Models\DeAiAnalysis;
use App\DiagnosticEcosystem\Models\DeVehicleProfile;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class MarketplaceHookController extends Controller
{
    public function __construct(
        private readonly MarketplaceIntegrationInterface $marketplace,
    ) {}

    public function searchParts(Request $request, int $vehicleId): JsonResponse
    {
        try {
            $this->ensureEnabled();
            $profile = DeVehicleProfile::where('user_id', $request->user()->id)->findOrFail($vehicleId);
            $query = (string) $request->input('q', $request->input('query', ''));

            return response()->json([
                'success' => true,
                'data' => $this->marketplace->searchParts($profile->id, $query),
            ]);
        } catch (RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 503);
        }
    }

    public function partsForAnalysis(Request $request, int $analysisId): JsonResponse
    {
        try {
            $this->ensureEnabled();

            $analysis = DeAiAnalysis::whereHas('vehicleProfile', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })->findOrFail($analysisId);

            $terms = $analysis->recommended_parts ?? [];
            $query = is_array($terms) ? implode(' ', array_slice($terms, 0, 5)) : (string) $terms;

            return response()->json([
                'success' => true,
                'data' => $this->marketplace->searchParts($analysis->vehicle_profile_id, $query),
                'analysis_id' => $analysis->id,
            ]);
        } catch (RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 503);
        }
    }

    public function shops(Request $request, int $vehicleId): JsonResponse
    {
        return $this->hookResponse($request, $vehicleId, fn ($profile) => $this->marketplace->findShops($profile->id, [
            'lat' => $request->input('lat'),
            'lng' => $request->input('lng'),
            'city' => $request->input('city'),
        ]));
    }

    public function dealers(Request $request, int $vehicleId): JsonResponse
    {
        return $this->hookResponse($request, $vehicleId, fn ($profile) => $this->marketplace->findDealers($profile->id));
    }

    public function insurance(Request $request, int $vehicleId): JsonResponse
    {
        return $this->hookResponse($request, $vehicleId, fn ($profile) => $this->marketplace->getInsuranceQuotes($profile->id));
    }

    public function roadside(Request $request, int $vehicleId): JsonResponse
    {
        return $this->hookResponse($request, $vehicleId, fn ($profile) => $this->marketplace->requestRoadside($profile->id));
    }

    public function inspection(Request $request, int $vehicleId): JsonResponse
    {
        return $this->hookResponse($request, $vehicleId, fn ($profile) => $this->marketplace->bookInspection($profile->id));
    }

    private function hookResponse(Request $request, int $vehicleId, callable $callback): JsonResponse
    {
        try {
            $this->ensureEnabled();
            $profile = DeVehicleProfile::where('user_id', $request->user()->id)->findOrFail($vehicleId);

            return response()->json([
                'success' => true,
                'data' => $callback($profile),
            ]);
        } catch (RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 503);
        }
    }

    private function ensureEnabled(): void
    {
        if (! config('diagnostic-ecosystem.marketplace_hooks')) {
            throw new RuntimeException('Marketplace hooks are disabled. Set DE_MARKETPLACE_HOOKS=true.');
        }
    }
}
