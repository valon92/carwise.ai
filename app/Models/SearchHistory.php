<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'search_query',
        'search_type',
        'search_category',
        'search_filters',
        'results_count',
        'search_duration',
        'ip_address',
        'user_agent',
        'referrer',
        'session_id',
        'device_type',
        'browser',
        'operating_system',
        'search_timestamp',
        'is_successful',
        'error_message',
        'search_source',
        'search_context',
        'additional_data'
    ];

    protected $casts = [
        'search_filters' => 'array',
        'search_duration' => 'decimal:3',
        'search_timestamp' => 'datetime',
        'is_successful' => 'boolean',
        'additional_data' => 'array'
    ];

    /**
     * Get the user that owns this search history.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for successful searches.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('is_successful', true);
    }

    /**
     * Scope for failed searches.
     */
    public function scopeFailed($query)
    {
        return $query->where('is_successful', false);
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
     * Scope for recent searches.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('search_timestamp', '>=', now()->subDays($days));
    }

    /**
     * Scope for searches with results.
     */
    public function scopeWithResults($query)
    {
        return $query->where('results_count', '>', 0);
    }

    /**
     * Scope for searches without results.
     */
    public function scopeWithoutResults($query)
    {
        return $query->where('results_count', 0);
    }

    /**
     * Scope for searches by device type.
     */
    public function scopeByDeviceType($query, $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }

    /**
     * Scope for searches by browser.
     */
    public function scopeByBrowser($query, $browser)
    {
        return $query->where('browser', $browser);
    }

    /**
     * Get formatted search duration.
     */
    public function getFormattedDurationAttribute()
    {
        if ($this->search_duration < 1) {
            return round($this->search_duration * 1000) . 'ms';
        }
        return round($this->search_duration, 2) . 's';
    }

    /**
     * Get search success status.
     */
    public function getSuccessStatusAttribute()
    {
        return $this->is_successful ? 'Successful' : 'Failed';
    }

    /**
     * Get search result status.
     */
    public function getResultStatusAttribute()
    {
        if ($this->results_count > 0) {
            return "Found {$this->results_count} results";
        }
        return 'No results found';
    }

    /**
     * Get search context display.
     */
    public function getContextDisplayAttribute()
    {
        $context = [];
        
        if ($this->search_type) {
            $context[] = ucfirst($this->search_type);
        }
        
        if ($this->search_category) {
            $context[] = ucfirst($this->search_category);
        }
        
        if ($this->search_source) {
            $context[] = ucfirst($this->search_source);
        }
        
        return implode(' • ', $context);
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
     * Get search query with highlighting.
     */
    public function getHighlightedQueryAttribute()
    {
        return $this->search_query;
    }

    /**
     * Get search performance rating.
     */
    public function getPerformanceRatingAttribute()
    {
        if ($this->search_duration < 0.5) {
            return 'Excellent';
        } elseif ($this->search_duration < 1.0) {
            return 'Good';
        } elseif ($this->search_duration < 2.0) {
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
     * Get search frequency for a query.
     */
    public static function getSearchFrequency($query, $userId = null)
    {
        $queryBuilder = static::where('search_query', $query);
        
        if ($userId) {
            $queryBuilder->where('user_id', $userId);
        }
        
        return $queryBuilder->count();
    }

    /**
     * Get popular searches.
     */
    public static function getPopularSearches($limit = 10, $userId = null, $days = 30)
    {
        $queryBuilder = static::selectRaw('search_query, COUNT(*) as frequency, AVG(results_count) as avg_results, AVG(search_duration) as avg_duration')
            ->where('search_timestamp', '>=', now()->subDays($days))
            ->where('is_successful', true)
            ->groupBy('search_query')
            ->orderBy('frequency', 'desc')
            ->limit($limit);
        
        if ($userId) {
            $queryBuilder->where('user_id', $userId);
        }
        
        return $queryBuilder->get();
    }

    /**
     * Get search trends.
     */
    public static function getSearchTrends($days = 30, $userId = null)
    {
        $queryBuilder = static::selectRaw('DATE(search_timestamp) as date, COUNT(*) as searches, AVG(results_count) as avg_results, AVG(search_duration) as avg_duration')
            ->where('search_timestamp', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date', 'desc');
        
        if ($userId) {
            $queryBuilder->where('user_id', $userId);
        }
        
        return $queryBuilder->get();
    }

    /**
     * Get search statistics.
     */
    public static function getSearchStatistics($userId = null, $days = 30)
    {
        $queryBuilder = static::where('search_timestamp', '>=', now()->subDays($days));
        
        if ($userId) {
            $queryBuilder->where('user_id', $userId);
        }
        
        return [
            'total_searches' => $queryBuilder->count(),
            'successful_searches' => $queryBuilder->where('is_successful', true)->count(),
            'failed_searches' => $queryBuilder->where('is_successful', false)->count(),
            'searches_with_results' => $queryBuilder->where('results_count', '>', 0)->count(),
            'searches_without_results' => $queryBuilder->where('results_count', 0)->count(),
            'average_results' => $queryBuilder->avg('results_count'),
            'average_duration' => $queryBuilder->avg('search_duration'),
            'unique_queries' => $queryBuilder->distinct('search_query')->count('search_query'),
            'most_searched_query' => $queryBuilder->selectRaw('search_query, COUNT(*) as frequency')
                ->groupBy('search_query')
                ->orderBy('frequency', 'desc')
                ->first()?->search_query,
            'search_types' => $queryBuilder->selectRaw('search_type, COUNT(*) as count')
                ->groupBy('search_type')
                ->pluck('count', 'search_type'),
            'search_categories' => $queryBuilder->selectRaw('search_category, COUNT(*) as count')
                ->groupBy('search_category')
                ->pluck('count', 'search_category'),
            'device_types' => $queryBuilder->selectRaw('device_type, COUNT(*) as count')
                ->groupBy('device_type')
                ->pluck('count', 'device_type'),
            'browsers' => $queryBuilder->selectRaw('browser, COUNT(*) as count')
                ->groupBy('browser')
                ->pluck('count', 'browser')
        ];
    }

    /**
     * Clean old search history.
     */
    public static function cleanOldHistory($days = 90)
    {
        return static::where('search_timestamp', '<', now()->subDays($days))->delete();
    }
}


