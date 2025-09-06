<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'operating_system',
        'country',
        'city',
        'latitude',
        'longitude',
        'is_active',
        'last_activity_at',
        'expires_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_activity_at' => 'datetime',
        'expires_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    /**
     * Get the user that owns the session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the session is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the session is valid (active and not expired).
     */
    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    /**
     * Get the session duration in minutes.
     */
    public function getDurationInMinutes(): int
    {
        return $this->created_at->diffInMinutes($this->last_activity_at);
    }

    /**
     * Get the device information as a string.
     */
    public function getDeviceInfoAttribute(): string
    {
        $parts = [];
        
        if ($this->browser) {
            $parts[] = $this->browser;
        }
        
        if ($this->operating_system) {
            $parts[] = $this->operating_system;
        }
        
        if ($this->device_type) {
            $parts[] = ucfirst($this->device_type);
        }
        
        return implode(' on ', $parts);
    }

    /**
     * Get the location as a string.
     */
    public function getLocationAttribute(): string
    {
        $parts = [];
        
        if ($this->city) {
            $parts[] = $this->city;
        }
        
        if ($this->country) {
            $parts[] = $this->country;
        }
        
        return implode(', ', $parts) ?: 'Unknown Location';
    }
}
