<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LatestVehicle;
use App\Services\MarketCheckInventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LatestVehicleController extends Controller
{
    /**
     * Display a listing of the latest vehicles.
     */
    public function index(Request $request): JsonResponse
    {
        $query = LatestVehicle::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            $query->available();
        }

        // Filter by featured
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // Filter by manufacturer
        if ($request->has('manufacturer')) {
            $query->where('manufacturer', $request->manufacturer);
        }

        // Limit results
        $limit = $request->integer('limit', 10);
        $vehicles = $query->orderBy('released_at', 'desc')
            ->orderBy('order', 'asc')
            ->limit($limit)
            ->get();

        // Increment view count for each vehicle
        foreach ($vehicles as $vehicle) {
            $vehicle->incrementViews();
        }

        return response()->json([
            'success' => true,
            'data' => $vehicles,
            'count' => $vehicles->count(),
        ]);
    }

    /**
     * Display the specified vehicle with full details.
     */
    public function show(string $id): JsonResponse
    {
        $vehicle = LatestVehicle::findOrFail($id);

        // Increment view count
        $vehicle->incrementViews();

        return response()->json([
            'success' => true,
            'data' => $vehicle,
        ]);
    }

    /**
     * Get featured vehicles for carousel (live MarketCheck inventory when configured).
     */
    public function featured(MarketCheckInventoryService $marketCheck): JsonResponse
    {
        if ($marketCheck->isConfigured()) {
            $live = $marketCheck->getFeaturedCarouselVehicles();

            return response()->json([
                'success' => true,
                'data' => $live,
                'count' => count($live),
                'source' => 'marketcheck',
                'listing_tier' => config('services.marketcheck.luxury_showcase') ? 'luxury' : 'standard',
                'message' => count($live) === 0
                    ? 'No listings returned. Check MARKETCHECK_ZIP or coordinates, API key, and plan limits.'
                    : null,
            ]);
        }

        if (config('services.marketcheck.carousel_database_fallback', false)) {
            $vehicles = LatestVehicle::featured()
                ->available()
                ->orderBy('released_at', 'desc')
                ->orderBy('order', 'asc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $vehicles,
                'count' => $vehicles->count(),
                'source' => 'local',
                'message' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [],
            'count' => 0,
            'source' => 'not_configured',
            'message' => 'Configure MarketCheck in .env (see .env.example), or set APP_ENV=local / MARKETCHECK_CAROUSEL_DATABASE_FALLBACK=true for demo vehicles.',
        ]);
    }
}
