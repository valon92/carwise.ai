<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'search_name',
        'search_query',
        'search_type',
        'search_category',
        'search_filters',
        'search_description',
        'is_public',
        'is_favorite',
        'tags',
        'notification_enabled',
        'notification_frequency',
        'last_searched_at',
        'search_count',
        'results_count',
        'average_duration',
        'success_rate',
        'search_source',
        'search_context',
        'additional_data'
    ];

    protected $casts = [
        'search_filters' => 'array',
        'is_public' => 'boolean',
        'is_favorite' => 'boolean',
        'tags' => 'array',
        'notification_enabled' => 'boolean',
        'last_searched_at' => 'datetime',
        'search_count' => 'integer',
        'results_count' => 'integer',
        'average_duration' => 'decimal:3',
        'success_rate' => 'decimal:2',
        'additional_data' => 'array'
    ];

    /**
     * Get the user that owns this saved search.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for public saved searches.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for private saved searches.
     */
    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    /**
     * Scope for favorite saved searches.
     */
    public function scopeFavorite($query)
    {
        return $query->where('is_favorite', true);
    }

    /**
     * Scope for searches by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('search_type', $type);
    }

    /**
     * Scope for searches by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('search_category', $category);
    }

    /**
     * Scope for searches with notifications enabled.
     */
    public function scopeWithNotifications($query)
    {
        return $query->where('notification_enabled', true);
    }

    /**
     * Scope for searches by tag.
     */
    public function scopeByTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    /**
     * Scope for recently searched.
     */
    public function scopeRecentlySearched($query, $days = 7)
    {
        return $query->where('last_searched_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for most searched.
     */
    public function scopeMostSearched($query, $limit = 10)
    {
        return $query->orderBy('search_count', 'desc')->limit($limit);
    }

    /**
     * Scope for most successful.
     */
    public function scopeMostSuccessful($query, $limit = 10)
    {
        return $query->orderBy('success_rate', 'desc')->limit($limit);
    }

    /**
     * Get formatted search count.
     */
    public function getFormattedSearchCountAttribute()
    {
        if ($this->search_count >= 1000) {
            return round($this->search_count / 1000, 1) . 'k';
        }
        return $this->search_count;
    }

    /**
     * Get formatted results count.
     */
    public function getFormattedResultsCountAttribute()
    {
        if ($this->results_count >= 1000) {
            return round($this->results_count / 1000, 1) . 'k';
        }
        return $this->results_count;
    }

    /**
     * Get formatted average duration.
     */
    public function getFormattedAverageDurationAttribute()
    {
        if ($this->average_duration < 1) {
            return round($this->average_duration * 1000) . 'ms';
        }
        return round($this->average_duration, 2) . 's';
    }

    /**
     * Get formatted success rate.
     */
    public function getFormattedSuccessRateAttribute()
    {
        return round($this->success_rate, 1) . '%';
    }

    /**
     * Get search performance rating.
     */
    public function getPerformanceRatingAttribute()
    {
        if ($this->average_duration < 0.5) {
            return 'Excellent';
        } elseif ($this->average_duration < 1.0) {
            return 'Good';
        } elseif ($this->average_duration < 2.0) {
            return 'Average';
        } else {
            return 'Slow';
        }
    }

    /**
     * Get search performance color.
     */
    public function getPerformanceColorAttribute()
    {
        return match($this->performance_rating) {
            'Excellent' => 'green',
            'Good' => 'blue',
            'Average' => 'yellow',
            'Slow' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get search type icon.
     */
    public function getSearchTypeIconAttribute()
    {
        $icons = [
            'car_parts' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'diagnosis' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'vehicles' => 'M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2-2v11a2 2 0 002 2h11a2 2 0 002-2v-2',
            'brands' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
            'categories' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'
        ];
        return $icons[$this->search_type] ?? $icons['car_parts'];
    }

    /**
     * Get search type color.
     */
    public function getSearchTypeColorAttribute()
    {
        $colors = [
            'car_parts' => 'blue',
            'diagnosis' => 'green',
            'vehicles' => 'purple',
            'brands' => 'orange',
            'categories' => 'pink'
        ];
        return $colors[$this->search_type] ?? 'gray';
    }

    /**
     * Get notification frequency display.
     */
    public function getNotificationFrequencyDisplayAttribute()
    {
        $frequencies = [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'instant' => 'Instant'
        ];
        return $frequencies[$this->notification_frequency] ?? 'Disabled';
    }

    /**
     * Get tags display.
     */
    public function getTagsDisplayAttribute()
    {
        if (!$this->tags || empty($this->tags)) {
            return 'No tags';
        }
        return implode(', ', $this->tags);
    }

    /**
     * Get search filters display.
     */
    public function getFiltersDisplayAttribute()
    {
        if (!$this->search_filters || empty($this->search_filters)) {
            return 'No filters';
        }
        
        $filters = [];
        foreach ($this->search_filters as $key => $value) {
            if ($value) {
                $filters[] = ucfirst($key) . ': ' . $value;
            }
        }
        
        return implode(', ', $filters);
    }

    /**
     * Get last searched display.
     */
    public function getLastSearchedDisplayAttribute()
    {
        if (!$this->last_searched_at) {
            return 'Never';
        }
        
        $diff = now()->diffInDays($this->last_searched_at);
        
        if ($diff === 0) {
            return 'Today';
        } elseif ($diff === 1) {
            return 'Yesterday';
        } elseif ($diff < 7) {
            return $diff . ' days ago';
        } elseif ($diff < 30) {
            return round($diff / 7) . ' weeks ago';
        } else {
            return round($diff / 30) . ' months ago';
        }
    }

    /**
     * Update search statistics.
     */
    public function updateSearchStatistics($resultsCount, $duration, $isSuccessful)
    {
        $this->search_count++;
        $this->last_searched_at = now();
        
        // Update average duration
        if ($this->average_duration) {
            $this->average_duration = ($this->average_duration + $duration) / 2;
        } else {
            $this->average_duration = $duration;
        }
        
        // Update results count
        if ($this->results_count) {
            $this->results_count = ($this->results_count + $resultsCount) / 2;
        } else {
            $this->results_count = $resultsCount;
        }
        
        // Update success rate
        $successfulSearches = $this->search_count * ($this->success_rate / 100);
        if ($isSuccessful) {
            $successfulSearches++;
        }
        $this->success_rate = ($successfulSearches / $this->search_count) * 100;
        
        $this->save();
    }

    /**
     * Get public saved searches.
     */
    public static function getPublicSearches($limit = 20)
    {
        return static::public()
            ->with('user')
            ->orderBy('search_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get trending saved searches.
     */
    public static function getTrendingSearches($limit = 10, $days = 7)
    {
        return static::public()
            ->recentlySearched($days)
            ->orderBy('search_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recommended saved searches for user.
     */
    public static function getRecommendedSearches($userId, $limit = 10)
    {
        $user = User::find($userId);
        if (!$user) {
            return collect();
        }
        
        // Get user's search history to find similar searches
        $userSearchTypes = SearchHistory::where('user_id', $userId)
            ->selectRaw('search_type, COUNT(*) as count')
            ->groupBy('search_type')
            ->orderBy('count', 'desc')
            ->pluck('search_type');
        
        $userCategories = SearchHistory::where('user_id', $userId)
            ->selectRaw('search_category, COUNT(*) as count')
            ->groupBy('search_category')
            ->orderBy('count', 'desc')
            ->pluck('search_category');
        
        return static::public()
            ->where(function($query) use ($userSearchTypes, $userCategories) {
                $query->whereIn('search_type', $userSearchTypes)
                      ->orWhereIn('search_category', $userCategories);
            })
            ->orderBy('search_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Search saved searches.
     */
    public static function searchSavedSearches($query, $userId = null, $isPublic = null)
    {
        $searchQuery = static::query();
        
        if ($userId) {
            $searchQuery->where('user_id', $userId);
        }
        
        if ($isPublic !== null) {
            $searchQuery->where('is_public', $isPublic);
        }
        
        if ($query) {
            $searchQuery->where(function($q) use ($query) {
                $q->where('search_name', 'like', '%' . $query . '%')
                  ->orWhere('search_query', 'like', '%' . $query . '%')
                  ->orWhere('search_description', 'like', '%' . $query . '%')
                  ->orWhereJsonContains('tags', $query);
            });
        }
        
        return $searchQuery->orderBy('search_count', 'desc')->get();
    }

    /**
     * Get saved search statistics.
     */
    public static function getSavedSearchStatistics($userId = null)
    {
        $query = static::query();
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        return [
            'total_saved_searches' => $query->count(),
            'public_saved_searches' => $query->where('is_public', true)->count(),
            'private_saved_searches' => $query->where('is_public', false)->count(),
            'favorite_saved_searches' => $query->where('is_favorite', true)->count(),
            'searches_with_notifications' => $query->where('notification_enabled', true)->count(),
            'total_search_count' => $query->sum('search_count'),
            'average_search_count' => $query->avg('search_count'),
            'most_searched' => $query->orderBy('search_count', 'desc')->first()?->search_name,
            'search_types' => $query->selectRaw('search_type, COUNT(*) as count')
                ->groupBy('search_type')
                ->pluck('count', 'search_type'),
            'search_categories' => $query->selectRaw('search_category, COUNT(*) as count')
                ->groupBy('search_category')
                ->pluck('count', 'search_category'),
            'notification_frequencies' => $query->selectRaw('notification_frequency, COUNT(*) as count')
                ->groupBy('notification_frequency')
                ->pluck('count', 'notification_frequency')
        ];
    }
}


