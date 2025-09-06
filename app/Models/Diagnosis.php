<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diagnosis extends Model
{
    protected $fillable = [
        'car_id',
        'user_id',
        'media_file',
        'media_type',
        'description',
        'ai_analysis',
        'problem',
        'confidence',
        'solutions',
        'next_steps',
        'status',
    ];

    protected $casts = [
        'ai_analysis' => 'array',
        'solutions' => 'array',
        'confidence' => 'integer',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
