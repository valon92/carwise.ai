<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuthorizedCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthorizedCompanyController extends Controller
{
    /**
     * Display a listing of authorized companies.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = AuthorizedCompany::active()->verified();

            // Filtering
            if ($request->has('country')) {
                $query->country($request->input('country'));
            }
            if ($request->has('specialization')) {
                $query->specialization($request->input('specialization'));
            }
            if ($request->has('is_featured')) {
                $query->where('is_featured', (bool)$request->input('is_featured'));
            }

            // Search
            if ($request->has('search')) {
                $query->search($request->input('search'));
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'sort_order');
            $sortOrder = $request->input('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->input('per_page', 20);
            $companies = $query->paginate($perPage);

            // Get unique filter options for the frontend
            $filters = [
                'countries' => AuthorizedCompany::active()->verified()->distinct()->pluck('country')->filter()->values()->toArray(),
                'specializations' => AuthorizedCompany::active()->verified()
                    ->get()
                    ->pluck('specializations')
                    ->flatten()
                    ->unique()
                    ->values()
                    ->toArray(),
            ];

            return response()->json([
                'success' => true,
                'data' => $companies->items(),
                'pagination' => [
                    'current_page' => $companies->currentPage(),
                    'last_page' => $companies->lastPage(),
                    'per_page' => $companies->perPage(),
                    'total' => $companies->total(),
                    'from' => $companies->firstItem(),
                    'to' => $companies->lastItem(),
                ],
                'filters' => $filters
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch authorized companies: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch authorized companies.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified authorized company.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $company = AuthorizedCompany::active()->verified()->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $company,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch authorized company: ' . $e->getMessage(), ['id' => $id, 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Authorized company not found.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get featured authorized companies.
     */
    public function getFeatured(): JsonResponse
    {
        try {
            $featuredCompanies = AuthorizedCompany::active()->verified()->featured()
                ->orderBy('sort_order', 'asc')
                ->limit(4)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $featuredCompanies,
                'count' => $featuredCompanies->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch featured authorized companies: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch featured authorized companies.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get car parts for a specific authorized company.
     */
    public function getCarParts(string $id): JsonResponse
    {
        try {
            $company = AuthorizedCompany::active()->verified()->findOrFail($id);
            $carParts = $company->carParts()->active()->inStock()->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $carParts->items(),
                'company' => $company,
                'pagination' => [
                    'current_page' => $carParts->currentPage(),
                    'last_page' => $carParts->lastPage(),
                    'per_page' => $carParts->perPage(),
                    'total' => $carParts->total(),
                    'from' => $carParts->firstItem(),
                    'to' => $carParts->lastItem(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch car parts for authorized company: ' . $e->getMessage(), ['id' => $id, 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch car parts for this company.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search for authorized companies.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $keywords = $request->input('keywords');
            if (empty($keywords)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search keywords are required.'
                ], 400);
            }

            $companies = AuthorizedCompany::active()->verified()->search($keywords)->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $companies->items(),
                'pagination' => [
                    'current_page' => $companies->currentPage(),
                    'last_page' => $companies->lastPage(),
                    'per_page' => $companies->perPage(),
                    'total' => $companies->total(),
                    'from' => $companies->firstItem(),
                    'to' => $companies->lastItem(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to search authorized companies: ' . $e->getMessage(), ['keywords' => $request->input('keywords'), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to search authorized companies.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}