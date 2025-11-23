<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'activity_type',
        'activity_description',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'request_data',
        'response_data',
        'response_status',
        'execution_time_ms',
        'device_type',
        'browser',
        'operating_system',
        'country',
        'city',
        'metadata'
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'metadata' => 'array'
    ];

    /**
     * Get the user that performed the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the session associated with the activity.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(UserSession::class, 'session_id', 'session_id');
    }

    /**
     * Get the activity type as a human-readable string.
     */
    public function getActivityTypeLabelAttribute(): string
    {
        return match($this->activity_type) {
            'login' => 'User Login',
            'logout' => 'User Logout',
            'page_view' => 'Page View',
            'diagnosis_submit' => 'Diagnosis Submitted',
            'diagnosis_view' => 'Diagnosis Viewed',
            'car_add' => 'Car Added',
            'car_update' => 'Car Updated',
            'car_delete' => 'Car Deleted',
            'profile_update' => 'Profile Updated',
            'password_change' => 'Password Changed',
            'email_verification' => 'Email Verified',
            'api_call' => 'API Call',
            'error' => 'Error Occurred',
            default => ucwords(str_replace('_', ' ', $this->activity_type))
        };
    }

    /**
     * Get the execution time in a human-readable format.
     */
    public function getExecutionTimeFormattedAttribute(): string
    {
        if (!$this->execution_time_ms) {
            return 'N/A';
        }

        if ($this->execution_time_ms < 1000) {
            return $this->execution_time_ms . 'ms';
        }

        return round($this->execution_time_ms / 1000, 2) . 's';
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
        
        return implode(' on ', $parts) ?: 'Unknown Device';
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

    /**
     * Scope for filtering by activity type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Scope for filtering by user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for filtering by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
