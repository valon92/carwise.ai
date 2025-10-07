<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'part_id',
        'part_name',
        'part_brand',
        'part_number',
        'part_description',
        'part_image_url',
        'part_category',
        'quantity',
        'unit_price',
        'total_price',
        'source',
        'affiliate_url',
        'tracking_data'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'tracking_data' => 'array'
    ];

    /**
     * Get the order that owns this item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the part that this item represents.
     */
    public function part(): BelongsTo
    {
        return $this->belongsTo(CarPart::class, 'part_id');
    }

    /**
     * Get formatted unit price.
     */
    public function getFormattedUnitPriceAttribute()
    {
        return '$' . number_format($this->unit_price, 2);
    }

    /**
     * Get formatted total price.
     */
    public function getFormattedTotalPriceAttribute()
    {
        return '$' . number_format($this->total_price, 2);
    }

    /**
     * Get the part image URL or default.
     */
    public function getImageUrlAttribute()
    {
        return $this->part_image_url ?: 'https://via.placeholder.com/150x150?text=No+Image';
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
}


