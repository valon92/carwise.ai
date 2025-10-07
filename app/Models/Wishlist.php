<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'part_id',
        'part_name',
        'part_brand',
        'part_number',
        'part_description',
        'part_image_url',
        'part_category',
        'part_price',
        'part_currency',
        'source',
        'affiliate_url',
        'notes',
        'priority',
        'notification_enabled',
        'price_alert_threshold',
        'added_at'
    ];

    protected $casts = [
        'part_price' => 'decimal:2',
        'price_alert_threshold' => 'decimal:2',
        'notification_enabled' => 'boolean',
        'added_at' => 'datetime'
    ];

    /**
     * Get the user that owns this wishlist item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the part that this wishlist item represents.
     */
    public function part(): BelongsTo
    {
        return $this->belongsTo(CarPart::class, 'part_id');
    }

    /**
     * Scope for high priority items.
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    /**
     * Scope for medium priority items.
     */
    public function scopeMediumPriority($query)
    {
        return $query->where('priority', 'medium');
    }

    /**
     * Scope for low priority items.
     */
    public function scopeLowPriority($query)
    {
        return $query->where('priority', 'low');
    }

    /**
     * Scope for items with price alerts enabled.
     */
    public function scopeWithPriceAlerts($query)
    {
        return $query->where('notification_enabled', true);
    }

    /**
     * Scope for recent additions.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('added_at', '>=', now()->subDays($days));
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute()
    {
        return $this->part_currency . ' ' . number_format($this->part_price, 2);
    }

    /**
     * Get formatted price alert threshold.
     */
    public function getFormattedPriceAlertAttribute()
    {
        return $this->part_currency . ' ' . number_format($this->price_alert_threshold, 2);
    }

    /**
     * Get the part display name.
     */
    public function getDisplayNameAttribute()
    {
        return $this->part_name . ($this->part_brand ? ' - ' . $this->part_brand : '');
    }

    /**
     * Get the part full description.
     */
    public function getFullDescriptionAttribute()
    {
        $description = $this->part_name;
        
        if ($this->part_brand) {
            $description .= ' by ' . $this->part_brand;
        }
        
        if ($this->part_number) {
            $description .= ' (Part #: ' . $this->part_number . ')';
        }
        
        if ($this->part_description) {
            $description .= ' - ' . $this->part_description;
        }
        
        return $description;
    }

    /**
     * Get the part image URL or default.
     */
    public function getImageUrlAttribute()
    {
        return $this->part_image_url ?: 'https://via.placeholder.com/150x150?text=No+Image';
    }

    /**
     * Check if price alert should be triggered.
     */
    public function shouldTriggerPriceAlert($currentPrice)
    {
        return $this->notification_enabled && 
               $this->price_alert_threshold && 
               $currentPrice <= $this->price_alert_threshold;
    }

    /**
     * Get priority badge color.
     */
    public function getPriorityBadgeColorAttribute()
    {
        return match($this->priority) {
            'high' => 'red',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray'
        };
    }

    /**
     * Get priority display name.
     */
    public function getPriorityDisplayAttribute()
    {
        return match($this->priority) {
            'high' => 'High Priority',
            'medium' => 'Medium Priority',
            'low' => 'Low Priority',
            default => 'Normal'
        };
    }
}


