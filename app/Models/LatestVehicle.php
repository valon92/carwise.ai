<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class LatestVehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'manufacturer',
        'model',
        'year',
        'name',
        'description',
        'image_url',
        'gallery_images',
        'price',
        'currency',
        'engine_type',
        'engine_size',
        'horsepower',
        'torque',
        'transmission',
        'drivetrain',
        'seats',
        'doors',
        'fuel_type',
        'fuel_consumption',
        'co2_emissions',
        'body_type',
        'features',
        'specifications',
        'status',
        'is_featured',
        'view_count',
        'order',
        'released_at',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'features' => 'array',
        'specifications' => 'array',
        'price' => 'decimal:2',
        'fuel_consumption' => 'decimal:2',
        'is_featured' => 'boolean',
        'released_at' => 'datetime',
        'year' => 'integer',
        'horsepower' => 'integer',
        'torque' => 'integer',
        'seats' => 'integer',
        'doors' => 'integer',
        'co2_emissions' => 'integer',
        'view_count' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Scope për makinat e disponueshme
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope për makinat featured
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope për makinat më të reja
     */
    public function scopeLatest($query, $limit = 10)
    {
        return $query->orderBy('released_at', 'desc')
                    ->orderBy('order', 'asc')
                    ->limit($limit);
    }

    /**
     * Rrit numrin e views
     */
    public function incrementViews()
    {
        $this->increment('view_count');
    }

    /**
     * Formatimi i çmimit
     */
    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->price 
                ? number_format($this->price, 2) . ' ' . $this->currency
                : 'N/A'
        );
    }
}
