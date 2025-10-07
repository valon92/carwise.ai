<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SavedSearch;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SavedSearchController extends Controller
{
    /**
     * Get user's saved searches
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 15);
        $searchType = $request->get('search_type');
        $searchCategory = $request->get('search_category');
        $isPublic = $request->get('is_public');
        $isFavorite = $request->get('is_favorite');
        $hasNotifications = $request->get('has_notifications');
        $tag = $request->get('tag');
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'search_count');
        $sortOrder = $request->get('sort_order', 'desc');

        $query = SavedSearch::where('user_id', $user->id);

        // Filter by search type
        if ($searchType) {
            $query->where('search_type', $searchType);
        }

        // Filter by search category
        if ($searchCategory) {
            $query->where('search_category', $searchCategory);
        }

        // Filter by public/private
        if ($isPublic !== null) {
            $query->where('is_public', $isPublic);
        }

        // Filter by favorite
        if ($isFavorite !== null) {
            $query->where('is_favorite', $isFavorite);
        }

        // Filter by notifications
        if ($hasNotifications !== null) {
            $query->where('notification_enabled', $hasNotifications);
        }

        // Filter by tag
        if ($tag) {
            $query->whereJsonContains('tags', $tag);
        }

        // Search in saved searches
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('search_name', 'like', '%' . $search . '%')
                  ->orWhere('search_query', 'like', '%' . $search . '%')
                  ->orWhere('search_description', 'like', '%' . $search . '%')
                  ->orWhereJsonContains('tags', $search);
            });
        }

        // Sort
        $allowedSortFields = ['search_count', 'last_searched_at', 'created_at', 'search_name', 'success_rate'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('search_count', 'desc');
        }

        $savedSearches = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $savedSearches->items(),
            'pagination' => [
                'current_page' => $savedSearches->currentPage(),
                'last_page' => $savedSearches->lastPage(),
                'per_page' => $savedSearches->perPage(),
                'total' => $savedSearches->total(),
                'has_more' => $savedSearches->hasMorePages()
            ]
        ]);
    }

    /**
     * Store a new saved search
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search_name' => 'required|string|max:255',
            'search_query' => 'required|string|max:255',
            'search_type' => 'nullable|string|max:50',
            'search_category' => 'nullable|string|max:50',
            'search_filters' => 'nullable|array',
            'search_description' => 'nullable|string|max:1000',
            'is_public' => 'nullable|boolean',
            'is_favorite' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'notification_enabled' => 'nullable|boolean',
            'notification_frequency' => 'nullable|in:daily,weekly,monthly,instant',
            'search_source' => 'nullable|string|max:50',
            'search_context' => 'nullable|string|max:255',
            'additional_data' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        try {
            // Check if saved search with same name already exists
            $existingSearch = SavedSearch::where('user_id', $user->id)
                ->where('search_name', $request->input('search_name'))
                ->first();

            if ($existingSearch) {
                return response()->json([
                    'success' => false,
                    'message' => 'A saved search with this name already exists.',
                    'data' => $existingSearch
                ], 409);
            }

            $savedSearch = SavedSearch::create([
                'user_id' => $user->id,
                'search_name' => $request->input('search_name'),
                'search_query' => $request->input('search_query'),
                'search_type' => $request->input('search_type', 'car_parts'),
                'search_category' => $request->input('search_category'),
                'search_filters' => $request->input('search_filters'),
                'search_description' => $request->input('search_description'),
                'is_public' => $request->input('is_public', false),
                'is_favorite' => $request->input('is_favorite', false),
                'tags' => $request->input('tags'),
                'notification_enabled' => $request->input('notification_enabled', false),
                'notification_frequency' => $request->input('notification_frequency', 'weekly'),
                'search_source' => $request->input('search_source', 'web'),
                'search_context' => $request->input('search_context'),
                'additional_data' => $request->input('additional_data')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Saved search created successfully',
                'data' => $savedSearch
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create saved search',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a saved search
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'search_name' => 'sometimes|required|string|max:255',
            'search_query' => 'sometimes|required|string|max:255',
            'search_type' => 'nullable|string|max:50',
            'search_category' => 'nullable|string|max:50',
            'search_filters' => 'nullable|array',
            'search_description' => 'nullable|string|max:1000',
            'is_public' => 'nullable|boolean',
            'is_favorite' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'notification_enabled' => 'nullable|boolean',
            'notification_frequency' => 'nullable|in:daily,weekly,monthly,instant',
            'search_source' => 'nullable|string|max:50',
            'search_context' => 'nullable|string|max:255',
            'additional_data' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $savedSearch = SavedSearch::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$savedSearch) {
            return response()->json([
                'success' => false,
                'message' => 'Saved search not found'
            ], 404);
        }

        try {
            // Check if name is being changed and if new name already exists
            if ($request->has('search_name') && $request->input('search_name') !== $savedSearch->search_name) {
                $existingSearch = SavedSearch::where('user_id', $user->id)
                    ->where('search_name', $request->input('search_name'))
                    ->where('id', '!=', $id)
                    ->first();

                if ($existingSearch) {
                    return response()->json([
                        'success' => false,
                        'message' => 'A saved search with this name already exists.'
                    ], 409);
                }
            }

            $savedSearch->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Saved search updated successfully',
                'data' => $savedSearch
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update saved search',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a saved search
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $savedSearch = SavedSearch::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$savedSearch) {
            return response()->json([
                'success' => false,
                'message' => 'Saved search not found'
            ], 404);
        }

        try {
            $savedSearch->delete();

            return response()->json([
                'success' => true,
                'message' => 'Saved search deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete saved search',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get saved search statistics
     */
    public function statistics(Request $request)
    {
        $user = $request->user();
        $stats = SavedSearch::getSavedSearchStatistics($user->id);

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get public saved searches
     */
    public function public(Request $request)
    {
        $limit = $request->get('limit', 20);
        $searchType = $request->get('search_type');
        $searchCategory = $request->get('search_category');
        $tag = $request->get('tag');
        $search = $request->get('search');

        $query = SavedSearch::public()->with('user');

        // Filter by search type
        if ($searchType) {
            $query->where('search_type', $searchType);
        }

        // Filter by search category
        if ($searchCategory) {
            $query->where('search_category', $searchCategory);
        }

        // Filter by tag
        if ($tag) {
            $query->whereJsonContains('tags', $tag);
        }

        // Search in public saved searches
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('search_name', 'like', '%' . $search . '%')
                  ->orWhere('search_query', 'like', '%' . $search . '%')
                  ->orWhere('search_description', 'like', '%' . $search . '%')
                  ->orWhereJsonContains('tags', $search);
            });
        }

        $publicSearches = $query->orderBy('search_count', 'desc')->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data' => $publicSearches
        ]);
    }

    /**
     * Get trending saved searches
     */
    public function trending(Request $request)
    {
        $limit = $request->get('limit', 10);
        $days = $request->get('days', 7);
        $trendingSearches = SavedSearch::getTrendingSearches($limit, $days);

        return response()->json([
            'success' => true,
            'data' => $trendingSearches
        ]);
    }

    /**
     * Get recommended saved searches
     */
    public function recommended(Request $request)
    {
        $user = $request->user();
        $limit = $request->get('limit', 10);
        $recommendedSearches = SavedSearch::getRecommendedSearches($user->id, $limit);

        return response()->json([
            'success' => true,
            'data' => $recommendedSearches
        ]);
    }

    /**
     * Execute a saved search
     */
    public function execute(Request $request, $id)
    {
        $user = $request->user();
        $savedSearch = SavedSearch::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$savedSearch) {
            return response()->json([
                'success' => false,
                'message' => 'Saved search not found'
            ], 404);
        }

        try {
            // Update search statistics
            $resultsCount = $request->input('results_count', 0);
            $duration = $request->input('duration', 0);
            $isSuccessful = $request->input('is_successful', true);

            $savedSearch->updateSearchStatistics($resultsCount, $duration, $isSuccessful);

            return response()->json([
                'success' => true,
                'message' => 'Saved search executed successfully',
                'data' => [
                    'search_query' => $savedSearch->search_query,
                    'search_type' => $savedSearch->search_type,
                    'search_category' => $savedSearch->search_category,
                    'search_filters' => $savedSearch->search_filters,
                    'search_source' => $savedSearch->search_source,
                    'search_context' => $savedSearch->search_context,
                    'additional_data' => $savedSearch->additional_data
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to execute saved search',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle favorite status
     */
    public function toggleFavorite(Request $request, $id)
    {
        $user = $request->user();
        $savedSearch = SavedSearch::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$savedSearch) {
            return response()->json([
                'success' => false,
                'message' => 'Saved search not found'
            ], 404);
        }

        try {
            $savedSearch->is_favorite = !$savedSearch->is_favorite;
            $savedSearch->save();

            return response()->json([
                'success' => true,
                'message' => 'Favorite status updated successfully',
                'data' => $savedSearch
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update favorite status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle notification status
     */
    public function toggleNotification(Request $request, $id)
    {
        $user = $request->user();
        $savedSearch = SavedSearch::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$savedSearch) {
            return response()->json([
                'success' => false,
                'message' => 'Saved search not found'
            ], 404);
        }

        try {
            $savedSearch->notification_enabled = !$savedSearch->notification_enabled;
            $savedSearch->save();

            return response()->json([
                'success' => true,
                'message' => 'Notification status updated successfully',
                'data' => $savedSearch
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update notification status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Duplicate a saved search
     */
    public function duplicate(Request $request, $id)
    {
        $user = $request->user();
        $savedSearch = SavedSearch::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$savedSearch) {
            return response()->json([
                'success' => false,
                'message' => 'Saved search not found'
            ], 404);
        }

        try {
            $newSavedSearch = SavedSearch::create([
                'user_id' => $user->id,
                'search_name' => $savedSearch->search_name . ' (Copy)',
                'search_query' => $savedSearch->search_query,
                'search_type' => $savedSearch->search_type,
                'search_category' => $savedSearch->search_category,
                'search_filters' => $savedSearch->search_filters,
                'search_description' => $savedSearch->search_description,
                'is_public' => false, // Duplicates are private by default
                'is_favorite' => false,
                'tags' => $savedSearch->tags,
                'notification_enabled' => false,
                'notification_frequency' => $savedSearch->notification_frequency,
                'search_source' => $savedSearch->search_source,
                'search_context' => $savedSearch->search_context,
                'additional_data' => $savedSearch->additional_data
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Saved search duplicated successfully',
                'data' => $newSavedSearch
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate saved search',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search saved searches
     */
    public function search(Request $request)
    {
        $user = $request->user();
        $query = $request->get('q', '');
        $isPublic = $request->get('is_public');
        $limit = $request->get('limit', 20);

        $results = SavedSearch::searchSavedSearches($query, $user->id, $isPublic);
        
        if ($limit) {
            $results = $results->take($limit);
        }

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }
}


