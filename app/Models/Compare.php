<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Compare extends Model
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
        'specifications',
        'features',
        'compatibility',
        'warranty',
        'shipping_info',
        'sort_order',
        'added_at'
    ];

    protected $casts = [
        'part_price' => 'decimal:2',
        'specifications' => 'array',
        'features' => 'array',
        'compatibility' => 'array',
        'added_at' => 'datetime'
    ];

    /**
     * Get the user that owns this compare item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the part that this compare item represents.
     */
    public function part(): BelongsTo
    {
        return $this->belongsTo(CarPart::class, 'part_id');
    }

    /**
     * Scope for recent additions.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('added_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for specific source.
     */
    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Scope for specific category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('part_category', $category);
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute()
    {
        return $this->part_currency . ' ' . number_format($this->part_price, 2);
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
     * Get specifications as formatted text.
     */
    public function getFormattedSpecificationsAttribute()
    {
        if (!$this->specifications) {
            return 'No specifications available';
        }

        $formatted = [];
        foreach ($this->specifications as $key => $value) {
            $formatted[] = ucfirst(str_replace('_', ' ', $key)) . ': ' . $value;
        }

        return implode(', ', $formatted);
    }

    /**
     * Get features as formatted text.
     */
    public function getFormattedFeaturesAttribute()
    {
        if (!$this->features) {
            return 'No features listed';
        }

        return implode(', ', $this->features);
    }

    /**
     * Get compatibility as formatted text.
     */
    public function getFormattedCompatibilityAttribute()
    {
        if (!$this->compatibility) {
            return 'Compatibility not specified';
        }

        $formatted = [];
        if (isset($this->compatibility['brands'])) {
            $formatted[] = 'Brands: ' . implode(', ', $this->compatibility['brands']);
        }
        if (isset($this->compatibility['models'])) {
            $formatted[] = 'Models: ' . implode(', ', $this->compatibility['models']);
        }
        if (isset($this->compatibility['years'])) {
            $formatted[] = 'Years: ' . implode(', ', $this->compatibility['years']);
        }

        return implode(' | ', $formatted);
    }
}
