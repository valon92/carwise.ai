<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarBrand;
use Illuminate\Http\Request;

class CarBrandController extends Controller
{
    /**
     * Get all car brands with optional filtering.
     */
    public function index(Request $request)
    {
        $query = CarBrand::query();

        // Filter by country
        if ($request->has('country')) {
            $query->where('country', $request->country);
        }

        // Filter by specialty
        if ($request->has('specialty')) {
            $query->whereJsonContains('specialties', $request->specialty);
        }

        // Filter by popularity
        if ($request->has('popular') && $request->popular) {
            $query->where('is_popular', true);
        }

        // Filter by active status
        if ($request->has('active') && $request->active) {
            $query->where('is_active', true);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort by sort_order, then by name
        $query->orderBy('sort_order')->orderBy('name');

        $brands = $query->get();

        return response()->json([
            'success' => true,
            'data' => $brands,
            'count' => $brands->count()
        ]);
    }

    /**
     * Get a specific car brand by ID or slug.
     */
    public function show($id)
    {
        $brand = CarBrand::where('id', $id)
            ->orWhere('slug', $id)
            ->with('models')
            ->first();

        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Car brand not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $brand
        ]);
    }

    /**
     * Get popular car brands.
     */
    public function popular()
    {
        $brands = CarBrand::where('is_popular', true)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $brands,
            'count' => $brands->count()
        ]);
    }

    /**
     * Get car brands by country.
     */
    public function byCountry($country)
    {
        $brands = CarBrand::where('country', $country)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $brands,
            'count' => $brands->count(),
            'country' => $country
        ]);
    }

    /**
     * Get car brands by specialty.
     */
    public function bySpecialty($specialty)
    {
        $brands = CarBrand::whereJsonContains('specialties', $specialty)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $brands,
            'count' => $brands->count(),
            'specialty' => $specialty
        ]);
    }

    /**
     * Get all countries with brand counts.
     */
    public function countries()
    {
        $countries = CarBrand::selectRaw('country, COUNT(*) as brand_count')
            ->where('is_active', true)
            ->groupBy('country')
            ->orderBy('brand_count', 'desc')
            ->orderBy('country')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $countries
        ]);
    }

    /**
     * Get all specialties with brand counts.
     */
    public function specialties()
    {
        $brands = CarBrand::where('is_active', true)->get();
        $specialties = [];

        foreach ($brands as $brand) {
            if ($brand->specialties) {
                foreach ($brand->specialties as $specialty) {
                    if (!isset($specialties[$specialty])) {
                        $specialties[$specialty] = 0;
                    }
                    $specialties[$specialty]++;
                }
            }
        }

        $specialtyData = collect($specialties)->map(function ($count, $specialty) {
            return [
                'specialty' => $specialty,
                'brand_count' => $count
            ];
        })->sortByDesc('brand_count')->values();

        return response()->json([
            'success' => true,
            'data' => $specialtyData
        ]);
    }

    /**
     * Search car brands.
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required'
            ], 400);
        }

        $brands = CarBrand::where('name', 'like', '%' . $query . '%')
            ->orWhere('country', 'like', '%' . $query . '%')
            ->orWhere('headquarters', 'like', '%' . $query . '%')
            ->where('is_active', true)
            ->orderBy('is_popular', 'desc')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $brands,
            'count' => $brands->count(),
            'query' => $query
        ]);
    }
}