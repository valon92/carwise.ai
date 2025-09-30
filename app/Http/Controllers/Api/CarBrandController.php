<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarBrandController extends Controller
{
    /**
     * Get popular car brands.
     */
    public function popular(): JsonResponse
    {
        try {
            // Temporary direct query to bypass cache issues
            $brands = \App\Models\CarBrand::where('is_popular', true)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'logo_url']);

            return response()->json([
                'success' => true,
                'data' => $brands,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch popular brands',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all car brands.
     */
    public function index(): JsonResponse
    {
        try {
            // Temporary direct query to bypass cache issues
            $brands = \App\Models\CarBrand::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'logo_url', 'is_popular']);

            return response()->json([
                'success' => true,
                'data' => $brands,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch car brands',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get car models by brand.
     */
    public function models(Request $request, int $brandId): JsonResponse
    {
        try {
            // Temporary direct query to bypass cache issues
            $models = \App\Models\CarModel::where('car_brand_id', $brandId)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'body_type', 'start_year', 'end_year']);

            return response()->json([
                'success' => true,
                'data' => $models,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch car models',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}