<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarBrand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'country',
        'logo_url',
        'website',
        'description',
        'founded_year',
        'headquarters',
        'specialties',
        'is_active',
        'is_popular',
        'sort_order'
    ];

    protected $casts = [
        'specialties' => 'array',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'founded_year' => 'integer',
        'sort_order' => 'integer'
    ];

    /**
     * Get all models for this brand.
     */
    public function models()
    {
        return $this->hasMany(CarModel::class);
    }

    /**
     * Get active models for this brand.
     */
    public function activeModels()
    {
        return $this->hasMany(CarModel::class)->where('is_active', true);
    }

    /**
     * Get popular models for this brand.
     */
    public function popularModels()
    {
        return $this->hasMany(CarModel::class)->where('is_popular', true);
    }

    /**
     * Scope for active brands.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for popular brands.
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    /**
     * Scope for brands by country.
     */
    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Scope for brands by specialty.
     */
    public function scopeBySpecialty($query, $specialty)
    {
        return $query->whereJsonContains('specialties', $specialty);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}