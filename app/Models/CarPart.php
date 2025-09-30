<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CarPart extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'part_number',
        'description',
        'category',
        'subcategory',
        'compatible_brands',
        'compatible_models',
        'compatible_years',
        'engine_type',
        'engine_size',
        'manufacturer',
        'oem_number',
        'aftermarket_brand',
        'aftermarket_number',
        'price',
        'currency',
        'stock_quantity',
        'in_stock',
        'availability_status',
        'quality_grade',
        'is_oem',
        'is_certified',
        'certifications',
        'weight',
        'dimensions',
        'material',
        'color',
        'installation_time_minutes',
        'difficulty_level',
        'installation_notes',
        'warranty_months',
        'image_url',
        'additional_images',
        'manual_url',
        'datasheet_url',
        'slug',
        'search_keywords',
        'meta_description',
        'is_active',
        'is_featured',
        'sort_order',
        'featured_until',
        'supplier_name',
        'supplier_contact',
        'supplier_website',
        'view_count',
        'purchase_count',
        'rating',
        'review_count',
        'authorized_company_id',
        'company_part_number',
        'international_pricing',
        'shipping_info',
        'warranty_details',
        'is_international_shipping',
        'available_countries',
        'discount_percentage',
        'discount_valid_until',
        'is_bulk_available',
        'bulk_minimum_quantity',
        'bulk_discount_percentage',
        'modification_notes',
        'installation_guides',
        'compatibility_matrix',
    ];

    protected $casts = [
        'compatible_brands' => 'array',
        'compatible_models' => 'array',
        'compatible_years' => 'array',
        'certifications' => 'array',
        'additional_images' => 'array',
        'search_keywords' => 'array',
        'international_pricing' => 'array',
        'shipping_info' => 'array',
        'warranty_details' => 'array',
        'available_countries' => 'array',
        'modification_notes' => 'array',
        'installation_guides' => 'array',
        'compatibility_matrix' => 'array',
        'price' => 'decimal:2',
        'rating' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'bulk_discount_percentage' => 'decimal:2',
        'in_stock' => 'boolean',
        'is_oem' => 'boolean',
        'is_certified' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_international_shipping' => 'boolean',
        'is_bulk_available' => 'boolean',
        'featured_until' => 'datetime',
        'discount_valid_until' => 'datetime',
    ];

    protected $appends = [
        'formatted_price',
        'quality_display',
        'availability_text',
        'difficulty_display',
        'warranty_display',
        'installation_time_display',
        'main_image_url',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Scope a query to only include active parts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include in-stock parts.
     */
    public function scopeInStock($query)
    {
        return $query->where('in_stock', true);
    }

    /**
     * Scope a query to only include featured parts.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
                    ->where(function($q) {
                        $q->whereNull('featured_until')
                          ->orWhere('featured_until', '>', now());
                    });
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to filter by manufacturer.
     */
    public function scopeByManufacturer($query, $manufacturer)
    {
        return $query->where('manufacturer', $manufacturer);
    }

    /**
     * Scope a query to filter by quality grade.
     */
    public function scopeByQuality($query, $quality)
    {
        return $query->where('quality_grade', $quality);
    }

    /**
     * Scope a query to filter by price range.
     */
    public function scopeByPriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    /**
     * Scope a query to search parts.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('part_number', 'like', "%{$search}%")
              ->orWhere('manufacturer', 'like', "%{$search}%")
              ->orWhere('aftermarket_brand', 'like', "%{$search}%");
        });
    }

    /**
     * Check if part is compatible with a specific car.
     */
    public function isCompatibleWith($brand, $model = null, $year = null): bool
    {
        // Check brand compatibility
        if ($this->compatible_brands && !in_array($brand, $this->compatible_brands)) {
            return false;
        }

        // Check model compatibility
        if ($model && $this->compatible_models && !in_array($model, $this->compatible_models)) {
            return false;
        }

        // Check year compatibility
        if ($year && $this->compatible_years && !in_array($year, $this->compatible_years)) {
            return false;
        }

        return true;
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->price, 2);
    }

    /**
     * Get main image URL or placeholder.
     */
    public function getMainImageUrlAttribute(): string
    {
        return $this->image_url ?: '/images/parts/placeholder.jpg';
    }

    /**
     * Get availability status text.
     */
    public function getAvailabilityTextAttribute(): string
    {
        return match($this->availability_status) {
            'available' => 'In Stock',
            'limited' => 'Limited Stock',
            'out_of_stock' => 'Out of Stock',
            default => 'Unknown'
        };
    }

    /**
     * Get quality grade display name.
     */
    public function getQualityDisplayAttribute(): string
    {
        return match($this->quality_grade) {
            'oem' => 'OEM (Original)',
            'premium' => 'Premium',
            'standard' => 'Standard',
            'economy' => 'Economy',
            default => ucfirst($this->quality_grade)
        };
    }

    /**
     * Get difficulty level display name.
     */
    public function getDifficultyDisplayAttribute(): string
    {
        return match($this->difficulty_level) {
            'easy' => 'Easy',
            'medium' => 'Medium',
            'hard' => 'Hard',
            'professional' => 'Professional Only',
            default => ucfirst($this->difficulty_level)
        };
    }

    /**
     * Get warranty display text.
     */
    public function getWarrantyDisplayAttribute(): string
    {
        if ($this->warranty_months >= 12) {
            $years = floor($this->warranty_months / 12);
            $months = $this->warranty_months % 12;
            
            if ($months > 0) {
                return "{$years} year" . ($years > 1 ? 's' : '') . " {$months} month" . ($months > 1 ? 's' : '');
            }
            
            return "{$years} year" . ($years > 1 ? 's' : '');
        }
        
        return "{$this->warranty_months} month" . ($this->warranty_months > 1 ? 's' : '');
    }

    /**
     * Get installation time display.
     */
    public function getInstallationTimeDisplayAttribute(): string
    {
        if (!$this->installation_time_minutes) {
            return 'Not specified';
        }

        if ($this->installation_time_minutes < 60) {
            return "{$this->installation_time_minutes} minutes";
        }

        $hours = floor($this->installation_time_minutes / 60);
        $minutes = $this->installation_time_minutes % 60;

        if ($minutes > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$hours} hour" . ($hours > 1 ? 's' : '');
    }

    /**
     * Increment view count.
     */
    public function incrementViews(): void
    {
        $this->increment('view_count');
    }

    /**
     * Increment purchase count.
     */
    public function incrementPurchases(): void
    {
        $this->increment('purchase_count');
    }

    /**
     * Update rating.
     */
    public function updateRating(float $newRating): void
    {
        $totalRating = ($this->rating * $this->review_count) + $newRating;
        $this->review_count++;
        $this->rating = $totalRating / $this->review_count;
        $this->save();
    }

    /**
     * Get related parts (same category or manufacturer).
     */
    public function getRelatedParts($limit = 4)
    {
        return self::active()
            ->where('id', '!=', $this->id)
            ->where(function($query) {
                $query->where('category', $this->category)
                      ->orWhere('manufacturer', $this->manufacturer);
            })
            ->limit($limit)
            ->get();
    }

    /**
     * Get the authorized company that supplies this part.
     */
    public function authorizedCompany(): BelongsTo
    {
        return $this->belongsTo(AuthorizedCompany::class);
    }

    /**
     * Relationship with diagnosis results through suggested parts.
     */
    public function diagnosisResults(): BelongsToMany
    {
        return $this->belongsToMany(DiagnosisResult::class, 'diagnosis_suggested_parts')
                    ->withPivot([
                        'suggestion_reason',
                        'priority',
                        'relevance_score',
                        'is_required',
                        'is_recommended',
                        'is_alternative',
                        'quantity_needed',
                        'usage_notes',
                        'estimated_cost',
                        'cost_currency',
                        'cost_breakdown',
                        'estimated_installation_time',
                        'installation_difficulty',
                        'installation_notes',
                        'user_selected',
                        'user_purchased',
                        'purchased_at',
                        'user_notes'
                    ])
                    ->withTimestamps();
    }
}