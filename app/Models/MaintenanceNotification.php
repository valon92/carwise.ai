<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'user_id',
        'maintenance_type',
        'title',
        'message',
        'priority',
        'due_date',
        'due_mileage',
        'current_mileage',
        'is_read',
        'read_at',
        'is_sent',
        'sent_at',
        'in_app',
        'email',
        'push',
        'sms',
        'action_taken',
        'action_taken_at',
        'action_notes',
        'is_recurring',
        'recurring_interval_days',
        'next_notification_at'
    ];

    protected $casts = [
        'due_date' => 'date',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
        'action_taken_at' => 'datetime',
        'next_notification_at' => 'datetime',
        'is_read' => 'boolean',
        'is_sent' => 'boolean',
        'in_app' => 'boolean',
        'email' => 'boolean',
        'push' => 'boolean',
        'sms' => 'boolean',
        'action_taken' => 'boolean',
        'is_recurring' => 'boolean'
    ];

    /**
     * Get the car that owns this notification.
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * Get the user that owns this notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications.
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for high priority notifications.
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    /**
     * Scope for overdue notifications.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now()->toDateString());
    }

    /**
     * Scope for upcoming notifications.
     */
    public function scopeUpcoming($query, $days = 30)
    {
        return $query->where('due_date', '<=', now()->addDays($days))
                    ->where('due_date', '>=', now()->toDateString());
    }

    /**
     * Scope for notifications by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('maintenance_type', $type);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * Mark notification as sent.
     */
    public function markAsSent(): void
    {
        $this->update([
            'is_sent' => true,
            'sent_at' => now()
        ]);
    }

    /**
     * Mark action as taken.
     */
    public function markActionTaken(?string $notes = null): void
    {
        $this->update([
            'action_taken' => true,
            'action_taken_at' => now(),
            'action_notes' => $notes
        ]);
    }

    /**
     * Check if notification is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date < now()->toDateString();
    }

    /**
     * Check if notification is due soon.
     */
    public function isDueSoon(int $days = 7): bool
    {
        return $this->due_date <= now()->addDays($days)->toDateString() 
               && $this->due_date >= now()->toDateString();
    }

    /**
     * Get days until due.
     */
    public function getDaysUntilDue(): int
    {
        return max(0, now()->diffInDays($this->due_date, false));
    }

    /**
     * Get priority color for UI.
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'urgent' => 'red',
            'high' => 'orange',
            'normal' => 'blue',
            'low' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Get maintenance type display name.
     */
    public function getTypeDisplayNameAttribute(): string
    {
        return match($this->maintenance_type) {
            'oil_change' => 'Oil Change',
            'tire_change' => 'Tire Change',
            'timing_belt' => 'Timing Belt',
            'brake_pad' => 'Brake Pad',
            'air_filter' => 'Air Filter',
            'fuel_filter' => 'Fuel Filter',
            'spark_plugs' => 'Spark Plugs',
            'battery' => 'Battery',
            'insurance' => 'Insurance',
            'registration' => 'Registration',
            'seasonal_tire' => 'Seasonal Tire Change',
            default => ucfirst(str_replace('_', ' ', $this->maintenance_type))
        };
    }

    /**
     * Create maintenance notifications for a car.
     */
    public static function createForCar(Car $car): void
    {
        $user = $car->user;
        $currentMileage = $car->current_mileage ?? $car->mileage ?? 0;
        $advanceDays = $car->notification_advance_days ?? 30;

        // Oil change notification
        if ($car->last_oil_change_mileage && $car->oil_change_interval) {
            $nextOilChangeMileage = $car->last_oil_change_mileage + $car->oil_change_interval;
            if ($currentMileage >= $nextOilChangeMileage - ($car->oil_change_interval * 0.2)) {
                static::create([
                    'car_id' => $car->id,
                    'user_id' => $user->id,
                    'maintenance_type' => 'oil_change',
                    'title' => 'Oil Change Due',
                    'message' => "Your {$car->brand} {$car->model} is due for an oil change. Current mileage: {$currentMileage} km",
                    'priority' => $currentMileage >= $nextOilChangeMileage ? 'urgent' : 'normal',
                    'due_date' => now()->addDays($advanceDays),
                    'due_mileage' => $nextOilChangeMileage,
                    'current_mileage' => $currentMileage
                ]);
            }
        }

        // Tire change notification
        if ($car->last_tire_change_mileage && $car->tire_change_interval) {
            $nextTireChangeMileage = $car->last_tire_change_mileage + $car->tire_change_interval;
            if ($currentMileage >= $nextTireChangeMileage - ($car->tire_change_interval * 0.2)) {
                static::create([
                    'car_id' => $car->id,
                    'user_id' => $user->id,
                    'maintenance_type' => 'tire_change',
                    'title' => 'Tire Change Due',
                    'message' => "Your {$car->brand} {$car->model} tires need to be replaced. Current mileage: {$currentMileage} km",
                    'priority' => $currentMileage >= $nextTireChangeMileage ? 'urgent' : 'normal',
                    'due_date' => now()->addDays($advanceDays),
                    'due_mileage' => $nextTireChangeMileage,
                    'current_mileage' => $currentMileage
                ]);
            }
        }

        // Insurance expiry notification
        if ($car->insurance_expiry_date) {
            if ($car->insurance_expiry_date <= now()->addDays($advanceDays)) {
                static::create([
                    'car_id' => $car->id,
                    'user_id' => $user->id,
                    'maintenance_type' => 'insurance',
                    'title' => 'Insurance Expiring Soon',
                    'message' => "Your {$car->brand} {$car->model} insurance expires on {$car->insurance_expiry_date->format('M d, Y')}",
                    'priority' => $car->insurance_expiry_date <= now()->addDays(7) ? 'urgent' : 'high',
                    'due_date' => $car->insurance_expiry_date,
                    'current_mileage' => $currentMileage
                ]);
            }
        }

        // Registration expiry notification
        if ($car->registration_expiry_date) {
            if ($car->registration_expiry_date <= now()->addDays($advanceDays)) {
                static::create([
                    'car_id' => $car->id,
                    'user_id' => $user->id,
                    'maintenance_type' => 'registration',
                    'title' => 'Registration Expiring Soon',
                    'message' => "Your {$car->brand} {$car->model} registration expires on {$car->registration_expiry_date->format('M d, Y')}",
                    'priority' => $car->registration_expiry_date <= now()->addDays(7) ? 'urgent' : 'high',
                    'due_date' => $car->registration_expiry_date,
                    'current_mileage' => $currentMileage
                ]);
            }
        }

        // Seasonal tire change notification
        $currentMonth = now()->month;
        if (($currentMonth >= 10 || $currentMonth <= 3) && $car->current_tire_type !== 'winter') {
            static::create([
                'car_id' => $car->id,
                'user_id' => $user->id,
                'maintenance_type' => 'seasonal_tire',
                'title' => 'Winter Tires Recommended',
                'message' => "Consider switching to winter tires for your {$car->brand} {$car->model} for better safety",
                'priority' => 'normal',
                'due_date' => now()->addDays(7),
                'current_mileage' => $currentMileage
            ]);
        } elseif ($currentMonth >= 4 && $currentMonth <= 9 && $car->current_tire_type !== 'summer') {
            static::create([
                'car_id' => $car->id,
                'user_id' => $user->id,
                'maintenance_type' => 'seasonal_tire',
                'title' => 'Summer Tires Recommended',
                'message' => "Consider switching to summer tires for your {$car->brand} {$car->model} for better performance",
                'priority' => 'normal',
                'due_date' => now()->addDays(7),
                'current_mileage' => $currentMileage
            ]);
        }
    }
}