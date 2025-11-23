<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_brand_id',
        'name',
        'slug',
        'generation',
        'start_year',
        'end_year',
        'body_type',
        'segment',
        'engine_options',
        'transmission_options',
        'fuel_types',
        'specifications',
        'description',
        'image_url',
        'is_active',
        'is_popular',
        'sort_order'
    ];

    protected $casts = [
        'engine_options' => 'array',
        'transmission_options' => 'array',
        'fuel_types' => 'array',
        'specifications' => 'array',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'start_year' => 'integer',
        'end_year' => 'integer',
        'sort_order' => 'integer'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = \Str::slug($model->name);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && empty($model->slug)) {
                $model->slug = \Str::slug($model->name);
            }
        });
    }

    /**
     * Get the brand that owns this model.
     */
    public function brand()
    {
        return $this->belongsTo(CarBrand::class, 'car_brand_id');
    }

    /**
     * Get all cars of this model.
     */
    public function cars()
    {
        return $this->hasMany(Car::class, 'model', 'name');
    }

    /**
     * Scope for active models.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for popular models.
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    /**
     * Scope for models by brand.
     */
    public function scopeByBrand($query, $brandId)
    {
        return $query->where('car_brand_id', $brandId);
    }

    /**
     * Scope for models by body type.
     */
    public function scopeByBodyType($query, $bodyType)
    {
        return $query->where('body_type', $bodyType);
    }

    /**
     * Scope for models by segment.
     */
    public function scopeBySegment($query, $segment)
    {
        return $query->where('segment', $segment);
    }

    /**
     * Scope for models by year range.
     */
    public function scopeByYearRange($query, $startYear, $endYear = null)
    {
        if ($endYear === null) {
            $endYear = $startYear;
        }
        
        return $query->where(function ($q) use ($startYear, $endYear) {
            $q->whereBetween('start_year', [$startYear, $endYear])
              ->orWhereBetween('end_year', [$startYear, $endYear])
              ->orWhere(function ($q2) use ($startYear, $endYear) {
                  $q2->where('start_year', '<=', $startYear)
                     ->where(function ($q3) use ($endYear) {
                         $q3->whereNull('end_year')
                            ->orWhere('end_year', '>=', $endYear);
                     });
              });
        });
    }

    /**
     * Scope for models by fuel type.
     */
    public function scopeByFuelType($query, $fuelType)
    {
        return $query->whereJsonContains('fuel_types', $fuelType);
    }

    /**
     * Get the full name (Brand + Model).
     */
    public function getFullNameAttribute()
    {
        return $this->brand->name . ' ' . $this->name;
    }

    /**
     * Get the production years as a string.
     */
    public function getProductionYearsAttribute()
    {
        if ($this->start_year && $this->end_year) {
            return $this->start_year . ' - ' . $this->end_year;
        } elseif ($this->start_year) {
            return $this->start_year . ' - Present';
        }
        return 'Unknown';
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}