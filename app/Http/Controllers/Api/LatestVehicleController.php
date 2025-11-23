<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LatestVehicle;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
        $vehicles = $query->latest($limit)->get();

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
     * Get featured vehicles for carousel.
     */
    public function featured(): JsonResponse
    {
        $vehicles = LatestVehicle::featured()
            ->available()
            ->latest(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $vehicles,
            'count' => $vehicles->count(),
        ]);
    }
}
