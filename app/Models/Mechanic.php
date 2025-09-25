<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mechanic extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'address',
        'city',
        'country',
        'lat',
        'lng',
        'geohash',
        'services',
        'logo_path',
        'gallery',
        'experience_years',
        'expertise',
        'location',
        'hourly_rate',
        'rating',
        'review_count',
        'availability',
        'bio',
        'certifications',
        'is_verified',
    ];

    protected $casts = [
        'services' => 'array',
        'expertise' => 'array',
        'certifications' => 'array',
        'gallery' => 'array',
        'hourly_rate' => 'decimal:2',
        'rating' => 'decimal:2',
        'review_count' => 'integer',
        'experience_years' => 'integer',
        'is_verified' => 'boolean',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
