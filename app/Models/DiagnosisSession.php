<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DiagnosisSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'make',
        'model',
        'year',
        'mileage',
        'engine_type',
        'engine_size',
        'description',
        'symptoms',
        'status',
        'ai_response',
        'confidence_score',
        'severity',
        'processed_at'
    ];

    protected $casts = [
        'symptoms' => 'array',
        'ai_response' => 'array',
        'processed_at' => 'datetime'
    ];

    /**
     * Get the user that owns the diagnosis session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the media files for the diagnosis session.
     */
    public function media(): HasMany
    {
        return $this->hasMany(DiagnosisMedia::class);
    }

    /**
     * Get the diagnosis result for the session.
     */
    public function result(): HasOne
    {
        return $this->hasOne(DiagnosisResult::class);
    }

    /**
     * Generate a unique session ID.
     */
    public static function generateSessionId(): string
    {
        return 'diag_' . uniqid() . '_' . time();
    }

    /**
     * Check if the session is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the session is processing.
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * Get the vehicle information as a string.
     */
    public function getVehicleInfoAttribute(): string
    {
        return "{$this->year} {$this->make} {$this->model}";
    }
}
