<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SearchHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SearchHistoryController extends Controller
{
    /**
     * Get user's search history
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 15);
        $searchType = $request->get('search_type');
        $searchCategory = $request->get('search_category');
        $isSuccessful = $request->get('is_successful');
        $hasResults = $request->get('has_results');
        $deviceType = $request->get('device_type');
        $browser = $request->get('browser');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $search = $request->get('search');

        $query = SearchHistory::where('user_id', $user->id)
            ->orderBy('search_timestamp', 'desc');

        // Filter by search type
        if ($searchType) {
            $query->where('search_type', $searchType);
        }

        // Filter by search category
        if ($searchCategory) {
            $query->where('search_category', $searchCategory);
        }

        // Filter by success status
        if ($isSuccessful !== null) {
            $query->where('is_successful', $isSuccessful);
        }

        // Filter by results
        if ($hasResults !== null) {
            if ($hasResults) {
                $query->where('results_count', '>', 0);
            } else {
                $query->where('results_count', 0);
            }
        }

        // Filter by device type
        if ($deviceType) {
            $query->where('device_type', $deviceType);
        }

        // Filter by browser
        if ($browser) {
            $query->where('browser', $browser);
        }

        // Filter by date range
        if ($dateFrom) {
            $query->where('search_timestamp', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('search_timestamp', '<=', $dateTo);
        }

        // Search in query
        if ($search) {
            $query->where('search_query', 'like', '%' . $search . '%');
        }

        $searchHistory = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $searchHistory->items(),
            'pagination' => [
                'current_page' => $searchHistory->currentPage(),
                'last_page' => $searchHistory->lastPage(),
                'per_page' => $searchHistory->perPage(),
                'total' => $searchHistory->total(),
                'has_more' => $searchHistory->hasMorePages()
            ]
        ]);
    }

    /**
     * Store a new search history entry
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search_query' => 'required|string|max:255',
            'search_type' => 'nullable|string|max:50',
            'search_category' => 'nullable|string|max:50',
            'search_filters' => 'nullable|array',
            'results_count' => 'nullable|integer|min:0',
            'search_duration' => 'nullable|numeric|min:0',
            'search_source' => 'nullable|string|max:50',
            'search_context' => 'nullable|string|max:255',
            'additional_data' => 'nullable|array',
            'is_successful' => 'nullable|boolean',
            'error_message' => 'nullable|string'
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
            $searchHistory = SearchHistory::create([
                'user_id' => $user->id,
                'search_query' => $request->input('search_query'),
                'search_type' => $request->input('search_type', 'car_parts'),
                'search_category' => $request->input('search_category'),
                'search_filters' => $request->input('search_filters'),
                'results_count' => $request->input('results_count', 0),
                'search_duration' => $request->input('search_duration'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referrer' => $request->header('referer'),
                'session_id' => $request->session()->getId(),
                'device_type' => $this->getDeviceType($request->userAgent()),
                'browser' => $this->getBrowser($request->userAgent()),
                'operating_system' => $this->getOperatingSystem($request->userAgent()),
                'search_timestamp' => now(),
                'is_successful' => $request->input('is_successful', true),
                'error_message' => $request->input('error_message'),
                'search_source' => $request->input('search_source', 'web'),
                'search_context' => $request->input('search_context'),
                'additional_data' => $request->input('additional_data')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Search history recorded successfully',
                'data' => $searchHistory
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record search history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get search history statistics
     */
    public function statistics(Request $request)
    {
        $user = $request->user();
        $days = $request->get('days', 30);

        $stats = SearchHistory::getSearchStatistics($user->id, $days);

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get popular searches
     */
    public function popular(Request $request)
    {
        $user = $request->user();
        $limit = $request->get('limit', 10);
        $days = $request->get('days', 30);

        $popularSearches = SearchHistory::getPopularSearches($limit, $user->id, $days);

        return response()->json([
            'success' => true,
            'data' => $popularSearches
        ]);
    }

    /**
     * Get search trends
     */
    public function trends(Request $request)
    {
        $user = $request->user();
        $days = $request->get('days', 30);

        $trends = SearchHistory::getSearchTrends($days, $user->id);

        return response()->json([
            'success' => true,
            'data' => $trends
        ]);
    }

    /**
     * Get recent searches
     */
    public function recent(Request $request)
    {
        $user = $request->user();
        $limit = $request->get('limit', 10);

        $recentSearches = SearchHistory::where('user_id', $user->id)
            ->orderBy('search_timestamp', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $recentSearches
        ]);
    }

    /**
     * Clear search history
     */
    public function clear(Request $request)
    {
        $user = $request->user();
        $days = $request->get('days'); // Clear history older than X days

        try {
            if ($days) {
                $deleted = SearchHistory::where('user_id', $user->id)
                    ->where('search_timestamp', '<', now()->subDays($days))
                    ->delete();
            } else {
                $deleted = SearchHistory::where('user_id', $user->id)->delete();
            }

            return response()->json([
                'success' => true,
                'message' => "Cleared {$deleted} search history entries"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear search history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete specific search history entry
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        
        $searchHistory = SearchHistory::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$searchHistory) {
            return response()->json([
                'success' => false,
                'message' => 'Search history entry not found'
            ], 404);
        }

        try {
            $searchHistory->delete();

            return response()->json([
                'success' => true,
                'message' => 'Search history entry deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete search history entry',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get search analytics
     */
    public function analytics(Request $request)
    {
        $user = $request->user();
        $days = $request->get('days', 30);

        $analytics = [
            'overview' => SearchHistory::getSearchStatistics($user->id, $days),
            'popular_searches' => SearchHistory::getPopularSearches(10, $user->id, $days),
            'trends' => SearchHistory::getSearchTrends($days, $user->id),
            'performance' => $this->getPerformanceAnalytics($user->id, $days),
            'categories' => $this->getCategoryAnalytics($user->id, $days),
            'devices' => $this->getDeviceAnalytics($user->id, $days)
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Get performance analytics
     */
    private function getPerformanceAnalytics($userId, $days)
    {
        return SearchHistory::where('user_id', $userId)
            ->where('search_timestamp', '>=', now()->subDays($days))
            ->selectRaw('
                AVG(search_duration) as avg_duration,
                MIN(search_duration) as min_duration,
                MAX(search_duration) as max_duration,
                COUNT(CASE WHEN search_duration < 0.5 THEN 1 END) as excellent_count,
                COUNT(CASE WHEN search_duration >= 0.5 AND search_duration < 1.0 THEN 1 END) as good_count,
                COUNT(CASE WHEN search_duration >= 1.0 AND search_duration < 2.0 THEN 1 END) as average_count,
                COUNT(CASE WHEN search_duration >= 2.0 THEN 1 END) as slow_count
            ')
            ->first();
    }

    /**
     * Get category analytics
     */
    private function getCategoryAnalytics($userId, $days)
    {
        return SearchHistory::where('user_id', $userId)
            ->where('search_timestamp', '>=', now()->subDays($days))
            ->selectRaw('search_category, COUNT(*) as count, AVG(results_count) as avg_results, AVG(search_duration) as avg_duration')
            ->groupBy('search_category')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Get device analytics
     */
    private function getDeviceAnalytics($userId, $days)
    {
        return SearchHistory::where('user_id', $userId)
            ->where('search_timestamp', '>=', now()->subDays($days))
            ->selectRaw('device_type, browser, COUNT(*) as count, AVG(search_duration) as avg_duration')
            ->groupBy('device_type', 'browser')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Get device type from user agent
     */
    private function getDeviceType($userAgent)
    {
        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/Tablet|iPad/', $userAgent)) {
            return 'tablet';
        } else {
            return 'desktop';
        }
    }

    /**
     * Get browser from user agent
     */
    private function getBrowser($userAgent)
    {
        if (preg_match('/Chrome/', $userAgent)) {
            return 'Chrome';
        } elseif (preg_match('/Firefox/', $userAgent)) {
            return 'Firefox';
        } elseif (preg_match('/Safari/', $userAgent)) {
            return 'Safari';
        } elseif (preg_match('/Edge/', $userAgent)) {
            return 'Edge';
        } else {
            return 'Other';
        }
    }

    /**
     * Get operating system from user agent
     */
    private function getOperatingSystem($userAgent)
    {
        if (preg_match('/Windows/', $userAgent)) {
            return 'Windows';
        } elseif (preg_match('/Mac/', $userAgent)) {
            return 'macOS';
        } elseif (preg_match('/Linux/', $userAgent)) {
            return 'Linux';
        } elseif (preg_match('/Android/', $userAgent)) {
            return 'Android';
        } elseif (preg_match('/iOS/', $userAgent)) {
            return 'iOS';
        } else {
            return 'Other';
        }
    }
}


