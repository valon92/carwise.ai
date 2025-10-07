<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarPart;
use App\Models\DiagnosisResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarPartController extends Controller
{
    /**
     * Display a listing of car parts.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = CarPart::active();

            // Apply basic filters
            if ($request->has('category')) {
                $query->byCategory($request->category);
            }

            if ($request->has('manufacturer')) {
                $query->byManufacturer($request->manufacturer);
            }

            if ($request->has('quality')) {
                $query->byQuality($request->quality);
            }

            if ($request->has('min_price') && $request->has('max_price')) {
                $query->byPriceRange($request->min_price, $request->max_price);
            }

            if ($request->has('search')) {
                $query->search($request->search);
            }

            if ($request->has('in_stock') && $request->boolean('in_stock')) {
                $query->inStock();
            }

            if ($request->has('featured') && $request->boolean('featured')) {
                $query->featured();
            }

            // Apply advanced filters
            $this->applyAdvancedFilters($query, $request);

            // Apply sorting
            $this->applySorting($query, $request);

            // Pagination
            $perPage = min($request->get('per_page', 20), 100);
            $parts = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $parts->items(),
                'pagination' => [
                    'current_page' => $parts->currentPage(),
                    'last_page' => $parts->lastPage(),
                    'per_page' => $parts->perPage(),
                    'total' => $parts->total(),
                    'from' => $parts->firstItem(),
                    'to' => $parts->lastItem(),
                ],
                'filters' => [
                    'categories' => CarPart::active()->distinct()->pluck('category')->sort()->values(),
                    'manufacturers' => CarPart::active()->distinct()->pluck('manufacturer')->sort()->values(),
                    'quality_grades' => CarPart::active()->distinct()->pluck('quality_grade')->sort()->values(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve car parts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Apply advanced filters to the query
     */
    private function applyAdvancedFilters($query, Request $request)
    {
        // Price range filter
        if ($request->has('price_min') || $request->has('price_max')) {
            $minPrice = $request->get('price_min', 0);
            $maxPrice = $request->get('price_max', 999999);
            $query->byPriceRange($minPrice, $maxPrice);
        }

        // Rating filter
        if ($request->has('min_rating')) {
            $query->where('rating', '>=', $request->get('min_rating'));
        }

        // Brand/Manufacturer filter (multiple)
        if ($request->has('brands')) {
            $brands = is_array($request->get('brands')) ? $request->get('brands') : explode(',', $request->get('brands'));
            $query->where(function($q) use ($brands) {
                foreach ($brands as $brand) {
                    $q->orWhere('manufacturer', 'like', "%{$brand}%")
                      ->orWhere('aftermarket_brand', 'like', "%{$brand}%");
                }
            });
        }

        // Category filter (multiple)
        if ($request->has('categories')) {
            $categories = is_array($request->get('categories')) ? $request->get('categories') : explode(',', $request->get('categories'));
            $query->whereIn('category', $categories);
        }

        // Condition filter
        if ($request->has('conditions')) {
            $conditions = is_array($request->get('conditions')) ? $request->get('conditions') : explode(',', $request->get('conditions'));
            $query->whereIn('condition', $conditions);
        }

        // Availability filter
        if ($request->has('availability')) {
            $availability = is_array($request->get('availability')) ? $request->get('availability') : explode(',', $request->get('availability'));
            $query->where(function($q) use ($availability) {
                foreach ($availability as $avail) {
                    switch ($avail) {
                        case 'in_stock':
                            $q->orWhere('stock_quantity', '>', 0);
                            break;
                        case 'out_of_stock':
                            $q->orWhere('stock_quantity', '<=', 0);
                            break;
                        case 'pre_order':
                            $q->orWhere('is_pre_order', true);
                            break;
                        case 'discontinued':
                            $q->orWhere('is_discontinued', true);
                            break;
                    }
                }
            });
        }

        // Shipping filter
        if ($request->has('shipping')) {
            $shipping = is_array($request->get('shipping')) ? $request->get('shipping') : explode(',', $request->get('shipping'));
            $query->where(function($q) use ($shipping) {
                foreach ($shipping as $ship) {
                    switch ($ship) {
                        case 'free_shipping':
                            $q->orWhere('shipping_cost', 0);
                            break;
                        case 'fast_shipping':
                            $q->orWhere('estimated_delivery', '<=', 2);
                            break;
                        case 'standard_shipping':
                            $q->orWhereBetween('estimated_delivery', [3, 5]);
                            break;
                        case 'express_shipping':
                            $q->orWhere('estimated_delivery', 1);
                            break;
                    }
                }
            });
        }

        // Review count filter
        if ($request->has('min_reviews')) {
            $query->where('review_count', '>=', $request->get('min_reviews'));
        }

        // View count filter
        if ($request->has('min_views')) {
            $query->where('view_count', '>=', $request->get('min_views'));
        }

        // Date range filter
        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->get('date_from'));
        }
        if ($request->has('date_to')) {
            $query->where('created_at', '<=', $request->get('date_to'));
        }

        // Quality grade filter
        if ($request->has('quality_grades')) {
            $grades = is_array($request->get('quality_grades')) ? $request->get('quality_grades') : explode(',', $request->get('quality_grades'));
            $query->whereIn('quality_grade', $grades);
        }

        // Warranty filter
        if ($request->has('warranty')) {
            $query->where('warranty_period', '>=', $request->get('warranty'));
        }

        // OEM/Aftermarket filter
        if ($request->has('part_type')) {
            $partTypes = is_array($request->get('part_type')) ? $request->get('part_type') : explode(',', $request->get('part_type'));
            $query->where(function($q) use ($partTypes) {
                foreach ($partTypes as $type) {
                    switch ($type) {
                        case 'oem':
                            $q->orWhere('is_oem', true);
                            break;
                        case 'aftermarket':
                            $q->orWhere('is_oem', false);
                            break;
                        case 'remanufactured':
                            $q->orWhere('is_remanufactured', true);
                            break;
                    }
                }
            });
        }
    }

    /**
     * Apply sorting to the query
     */
    private function applySorting($query, Request $request)
    {
        $sortBy = $request->get('sort_by', 'relevance');
        $sortOrder = $request->get('sort_order', 'asc');

        switch ($sortBy) {
            case 'relevance':
                // Default sorting by relevance (featured first, then by name)
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('view_count', 'desc')
                      ->orderBy('name', 'asc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'rating_desc':
                $query->orderBy('rating', 'desc')
                      ->orderBy('review_count', 'desc');
                break;
            case 'rating_asc':
                $query->orderBy('rating', 'asc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'popularity':
                $query->orderBy('view_count', 'desc')
                      ->orderBy('purchase_count', 'desc');
                break;
            case 'stock':
                $query->orderBy('stock_quantity', 'desc');
                break;
            default:
                $allowedSortFields = ['name', 'price', 'rating', 'view_count', 'created_at'];
                if (in_array($sortBy, $allowedSortFields)) {
                    $query->orderBy($sortBy, $sortOrder);
                } else {
                    $query->orderBy('name', 'asc');
                }
                break;
        }
    }

    /**
     * Display the specified car part.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $part = CarPart::active()->findOrFail($id);
            
            // Increment view count
            $part->incrementViews();

            // Load related parts
            $relatedParts = $part->getRelatedParts();

            return response()->json([
                'success' => true,
                'data' => [
                    'part' => $part,
                    'related_parts' => $relatedParts,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Car part not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Get parts suggested for a specific diagnosis result.
     */
    public function getSuggestedParts(string $diagnosisResultId): JsonResponse
    {
        try {
            $diagnosisResult = DiagnosisResult::with(['suggestedParts', 'diagnosisSuggestedParts'])->findOrFail($diagnosisResultId);
            
            $suggestedParts = $diagnosisResult->diagnosisSuggestedParts()
                ->with('carPart')
                ->byPriority()
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'diagnosis_result' => $diagnosisResult,
                    'suggested_parts' => $suggestedParts,
                    'total_cost' => $diagnosisResult->getFormattedTotalPartsCost(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve suggested parts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get parts by category.
     */
    public function getByCategory(string $category): JsonResponse
    {
        try {
            $parts = CarPart::active()
                ->byCategory($category)
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $parts,
                'category' => $category,
                'count' => $parts->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve parts by category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get featured parts.
     */
    public function getFeatured(): JsonResponse
    {
        try {
            $parts = CarPart::active()
                ->featured()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->limit(12)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $parts,
                'count' => $parts->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve featured parts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search parts.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'nullable|string|min:2|max:255',
                'search' => 'nullable|string|min:2|max:255',
                'category' => 'nullable|string',
                'manufacturer' => 'nullable|string',
                'limit' => 'nullable|integer|min:1|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $searchTerm = $request->input('query') ?? $request->input('search');
            
            if (!$searchTerm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search term is required',
                ], 422);
            }
            
            $query = CarPart::active()->search($searchTerm);

            if ($request->has('category')) {
                $query->byCategory($request->category);
            }

            if ($request->has('manufacturer')) {
                $query->byManufacturer($request->manufacturer);
            }

            $limit = $request->get('limit', 20);
            $parts = $query->orderBy('name')
                          ->limit($limit)
                          ->get();

            return response()->json([
                'success' => true,
                'data' => $parts,
                'query' => $request->query,
                'count' => $parts->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search parts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}