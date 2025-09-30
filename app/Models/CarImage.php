<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand',
        'model',
        'year',
        'body_type',
        'color',
        'image_url',
        'thumbnail_url',
        'image_type',
        'angle',
        'width',
        'height',
        'source',
        'is_primary',
        'is_active',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
        'year' => 'integer',
        'width' => 'integer',
        'height' => 'integer'
    ];

    /**
     * Get the primary image for a specific car
     */
    public static function getPrimaryImage($brand, $model, $year = null, $color = null)
    {
        $query = static::where('brand', $brand)
            ->where('model', $model)
            ->where('is_primary', true)
            ->where('is_active', true)
            ->where('image_type', 'exterior')
            ->where('angle', 'front');

        if ($year) {
            $query->where('year', $year);
        }

        if ($color) {
            $query->where('color', $color);
        }

        return $query->first();
    }

    /**
     * Get all images for a specific car
     */
    public static function getCarImages($brand, $model, $year = null, $color = null)
    {
        $query = static::where('brand', $brand)
            ->where('model', $model)
            ->where('is_active', true);

        if ($year) {
            $query->where('year', $year);
        }

        if ($color) {
            $query->where('color', $color);
        }

        return $query->orderBy('is_primary', 'desc')
            ->orderBy('image_type', 'asc')
            ->orderBy('angle', 'asc')
            ->get();
    }

    /**
     * Get fallback image for a brand
     */
    public static function getBrandFallbackImage($brand)
    {
        return static::where('brand', $brand)
            ->where('is_active', true)
            ->where('image_type', 'exterior')
            ->where('angle', 'front')
            ->orderBy('is_primary', 'desc')
            ->first();
    }

    /**
     * Get image URL with fallback
     */
    public function getImageUrlAttribute($value)
    {
        if ($value && filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // Return a default car image if URL is invalid
        return '/images/cars/default-car.svg';
    }

    /**
     * Get thumbnail URL with fallback
     */
    public function getThumbnailUrlAttribute($value)
    {
        if ($value && filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // Return the main image URL as thumbnail if no thumbnail is set
        return $this->image_url;
    }

    /**
     * Scope for active images
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for primary images
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope for exterior images
     */
    public function scopeExterior($query)
    {
        return $query->where('image_type', 'exterior');
    }

    /**
     * Scope for front angle images
     */
    public function scopeFront($query)
    {
        return $query->where('angle', 'front');
    }
}