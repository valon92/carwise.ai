<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuthorizedCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'logo_url', 'website', 'email', 'phone',
        'address', 'city', 'country', 'postal_code', 'languages_supported',
        'currencies_supported', 'countries_served', 'specializations',
        'brands_authorized', 'certification_body', 'certification_number',
        'certification_date', 'certification_expiry', 'is_verified', 'is_active',
        'is_featured', 'sort_order', 'rating', 'review_count', 'parts_count',
        'orders_count', 'total_sales', 'payment_methods', 'shipping_methods',
        'shipping_time_days', 'shipping_cost_base', 'shipping_cost_currency',
        'offers_warranty', 'warranty_months', 'offers_installation',
        'installation_cost_base', 'installation_cost_currency', 'return_policy',
        'terms_conditions', 'social_media', 'contact_hours', 'timezone',
        'last_activity'
    ];

    protected $casts = [
        'languages_supported' => 'array',
        'currencies_supported' => 'array',
        'countries_served' => 'array',
        'specializations' => 'array',
        'brands_authorized' => 'array',
        'payment_methods' => 'array',
        'shipping_methods' => 'array',
        'social_media' => 'array',
        'contact_hours' => 'array',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'offers_warranty' => 'boolean',
        'offers_installation' => 'boolean',
        'certification_date' => 'date',
        'certification_expiry' => 'date',
        'last_activity' => 'datetime',
        'rating' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'shipping_cost_base' => 'decimal:2',
        'installation_cost_base' => 'decimal:2',
    ];

    /**
     * Get the car parts for this company.
     */
    public function carParts(): HasMany
    {
        return $this->hasMany(CarPart::class);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get formatted rating.
     */
    public function getFormattedRatingAttribute(): string
    {
        return $this->rating ? number_format($this->rating, 1) . '/5.0' : 'N/A';
    }

    /**
     * Get formatted total sales.
     */
    public function getFormattedTotalSalesAttribute(): string
    {
        if (!$this->total_sales) {
            return 'N/A';
        }

        $currency = $this->currencies_supported[0] ?? 'USD';
        return $currency . ' ' . number_format($this->total_sales, 2);
    }

    /**
     * Get logo URL or placeholder.
     */
    public function getLogoUrlAttribute($value): string
    {
        return $value ?: '/images/companies/placeholder-logo.png';
    }

    /**
     * Get certification status.
     */
    public function getCertificationStatusAttribute(): string
    {
        if (!$this->certification_expiry) {
            return 'Not Certified';
        }

        if ($this->certification_expiry->isPast()) {
            return 'Expired';
        }

        if ($this->certification_expiry->diffInDays() <= 30) {
            return 'Expiring Soon';
        }

        return 'Valid';
    }

    /**
     * Get business hours display.
     */
    public function getBusinessHoursDisplayAttribute(): string
    {
        if (!$this->contact_hours) {
            return 'Not specified';
        }

        $hours = $this->contact_hours;
        if (isset($hours['monday'])) {
            return $hours['monday'] . ' - ' . ($hours['friday'] ?? $hours['sunday']);
        }

        return 'Contact for hours';
    }

    /**
     * Scope a query to only include active companies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include verified companies.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope a query to only include featured companies.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to filter by country.
     */
    public function scopeCountry($query, string $country)
    {
        return $query->whereJsonContains('countries_served', $country);
    }

    /**
     * Scope a query to filter by specialization.
     */
    public function scopeSpecialization($query, string $specialization)
    {
        return $query->whereJsonContains('specializations', $specialization);
    }

    /**
     * Scope a query to search by name or description.
     */
    public function scopeSearch($query, string $keywords)
    {
        return $query->where(function ($q) use ($keywords) {
            $q->where('name', 'like', '%' . $keywords . '%')
                ->orWhere('description', 'like', '%' . $keywords . '%');
        });
    }

    /**
     * Increment parts count.
     */
    public function incrementPartsCount(): void
    {
        $this->increment('parts_count');
    }

    /**
     * Increment orders count.
     */
    public function incrementOrdersCount(): void
    {
        $this->increment('orders_count');
    }

    /**
     * Update total sales.
     */
    public function updateTotalSales(float $amount): void
    {
        $this->increment('total_sales', $amount);
    }

    /**
     * Update last activity.
     */
    public function updateLastActivity(): void
    {
        $this->update(['last_activity' => now()]);
    }
}