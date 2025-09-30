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

            // Apply filters
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

            // Apply sorting
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');

            $allowedSortFields = ['name', 'price', 'rating', 'view_count', 'created_at'];
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortOrder);
            }

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
                'query' => 'required|string|min:2|max:255',
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

            $query = CarPart::active()->search($request->query);

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