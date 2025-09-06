<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mechanic extends Model
{
    protected $fillable = [
        'user_id',
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
        'expertise' => 'array',
        'certifications' => 'array',
        'hourly_rate' => 'decimal:2',
        'rating' => 'decimal:2',
        'review_count' => 'integer',
        'experience_years' => 'integer',
        'is_verified' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
