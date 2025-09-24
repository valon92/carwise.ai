<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarModel;
use App\Models\CarBrand;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CarModelController extends Controller
{
    /**
     * Get all car models with optional filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $query = CarModel::with('brand');

        // Filter by brand
        if ($request->has('brand_id')) {
            $query->where('car_brand_id', $request->brand_id);
        }

        // Filter by brand slug
        if ($request->has('brand_slug')) {
            $brand = CarBrand::where('slug', $request->brand_slug)->first();
            if ($brand) {
                $query->where('car_brand_id', $brand->id);
            }
        }

        // Filter by body type
        if ($request->has('body_type')) {
            $query->where('body_type', $request->body_type);
        }

        // Filter by segment
        if ($request->has('segment')) {
            $query->where('segment', $request->segment);
        }

        // Filter by fuel type
        if ($request->has('fuel_type')) {
            $query->whereJsonContains('fuel_types', $request->fuel_type);
        }

        // Filter by year range
        if ($request->has('year')) {
            $year = $request->year;
            $query->where(function ($q) use ($year) {
                $q->where('start_year', '<=', $year)
                  ->where(function ($q2) use ($year) {
                      $q2->whereNull('end_year')
                         ->orWhere('end_year', '>=', $year);
                  });
            });
        }

        // Filter by popular models only
        if ($request->boolean('popular_only')) {
            $query->where('is_popular', true);
        }

        // Filter by active models only
        if ($request->boolean('active_only', true)) {
            $query->where('is_active', true);
        }

        // Search by name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('generation', 'like', "%{$search}%");
            });
        }

        // Sort order
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 50);
        $models = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $models->items(),
            'pagination' => [
                'current_page' => $models->currentPage(),
                'last_page' => $models->lastPage(),
                'per_page' => $models->perPage(),
                'total' => $models->total(),
            ],
            'filters' => [
                'brand_id' => $request->brand_id,
                'brand_slug' => $request->brand_slug,
                'body_type' => $request->body_type,
                'segment' => $request->segment,
                'fuel_type' => $request->fuel_type,
                'year' => $request->year,
                'popular_only' => $request->boolean('popular_only'),
                'active_only' => $request->boolean('active_only', true),
                'search' => $request->search,
            ]
        ]);
    }

    /**
     * Get a specific car model by ID.
     */
    public function show($id): JsonResponse
    {
        $model = CarModel::with('brand')->find($id);

        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'Car model not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $model
        ]);
    }

    /**
     * Get a specific car model by slug.
     */
    public function showBySlug($slug): JsonResponse
    {
        $model = CarModel::with('brand')->where('slug', $slug)->first();

        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'Car model not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $model
        ]);
    }

    /**
     * Get models by brand ID.
     */
    public function byBrand($brandId): JsonResponse
    {
        $brand = CarBrand::find($brandId);
        
        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Car brand not found'
            ], 404);
        }

        $models = CarModel::where('car_brand_id', $brandId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $models,
            'brand' => $brand
        ]);
    }

    /**
     * Get models by brand slug.
     */
    public function byBrandSlug($brandSlug): JsonResponse
    {
        $brand = CarBrand::where('slug', $brandSlug)->first();
        
        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Car brand not found'
            ], 404);
        }

        $models = CarModel::where('car_brand_id', $brand->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $models,
            'brand' => $brand
        ]);
    }

    /**
     * Get popular models.
     */
    public function popular(): JsonResponse
    {
        $models = CarModel::with('brand')
            ->where('is_popular', true)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $models
        ]);
    }

    /**
     * Get models by body type.
     */
    public function byBodyType($bodyType): JsonResponse
    {
        $models = CarModel::with('brand')
            ->where('body_type', $bodyType)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $models,
            'body_type' => $bodyType
        ]);
    }

    /**
     * Get models by segment.
     */
    public function bySegment($segment): JsonResponse
    {
        $models = CarModel::with('brand')
            ->where('segment', $segment)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $models,
            'segment' => $segment
        ]);
    }

    /**
     * Get models by fuel type.
     */
    public function byFuelType($fuelType): JsonResponse
    {
        $models = CarModel::with('brand')
            ->whereJsonContains('fuel_types', $fuelType)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $models,
            'fuel_type' => $fuelType
        ]);
    }

    /**
     * Search models.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required'
            ], 400);
        }

        $models = CarModel::with('brand')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('generation', 'like', "%{$query}%")
                  ->orWhereHas('brand', function ($brandQuery) use ($query) {
                      $brandQuery->where('name', 'like', "%{$query}%");
                  });
            })
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $models,
            'query' => $query
        ]);
    }

    /**
     * Get available body types.
     */
    public function bodyTypes(): JsonResponse
    {
        $bodyTypes = CarModel::select('body_type')
            ->whereNotNull('body_type')
            ->distinct()
            ->orderBy('body_type')
            ->pluck('body_type');

        return response()->json([
            'success' => true,
            'data' => $bodyTypes
        ]);
    }

    /**
     * Get available segments.
     */
    public function segments(): JsonResponse
    {
        $segments = CarModel::select('segment')
            ->whereNotNull('segment')
            ->distinct()
            ->orderBy('segment')
            ->pluck('segment');

        return response()->json([
            'success' => true,
            'data' => $segments
        ]);
    }

    /**
     * Get available fuel types.
     */
    public function fuelTypes(): JsonResponse
    {
        $models = CarModel::whereNotNull('fuel_types')->get();
        $fuelTypes = [];
        
        foreach ($models as $model) {
            if ($model->fuel_types) {
                foreach ($model->fuel_types as $fuel) {
                    if (!in_array($fuel, $fuelTypes)) {
                        $fuelTypes[] = $fuel;
                    }
                }
            }
        }
        
        sort($fuelTypes);

        return response()->json([
            'success' => true,
            'data' => $fuelTypes
        ]);
    }
}
