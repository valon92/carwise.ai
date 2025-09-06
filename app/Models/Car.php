<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'brand',
        'model',
        'year',
        'vin',
        'license_plate',
        'color',
        'fuel_type',
        'transmission',
        'mileage',
        'purchase_date',
        'purchase_price',
        'notes',
        'specifications',
        'maintenance_history',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'mileage' => 'integer',
            'purchase_date' => 'date',
            'purchase_price' => 'decimal:2',
            'specifications' => 'array',
            'maintenance_history' => 'array',
        ];
    }

    /**
     * Get the user that owns the car.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the diagnosis sessions for the car.
     */
    public function diagnosisSessions(): HasMany
    {
        return $this->hasMany(DiagnosisSession::class);
    }

    /**
     * Get the car's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->year} {$this->brand} {$this->model}";
    }

    /**
     * Get the car's display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->brand} {$this->model}";
    }

    /**
     * Get the car's age in years.
     */
    public function getAgeAttribute(): int
    {
        return now()->year - $this->year;
    }

    /**
     * Get the car's status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'green',
            'sold' => 'blue',
            'scrapped' => 'red',
            default => 'gray',
        };
    }

    /**
     * Scope for active cars.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for cars by brand.
     */
    public function scopeByBrand($query, $brand)
    {
        return $query->where('brand', $brand);
    }

    /**
     * Scope for cars by year range.
     */
    public function scopeByYearRange($query, $startYear, $endYear)
    {
        return $query->whereBetween('year', [$startYear, $endYear]);
    }
}