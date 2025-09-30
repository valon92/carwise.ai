<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mechanic_id',
        'appointment_number',
        'service_type',
        'description',
        'priority',
        'status',
        'scheduled_at',
        'estimated_duration',
        'actual_start_at',
        'actual_end_at',
        'location_type',
        'address',
        'latitude',
        'longitude',
        'vehicle_make',
        'vehicle_model',
        'vehicle_year',
        'vehicle_vin',
        'vehicle_license_plate',
        'vehicle_mileage',
        'estimated_cost',
        'actual_cost',
        'currency',
        'cost_breakdown',
        'related_type',
        'related_id',
        'notes',
        'customer_notes',
        'mechanic_notes',
        'requires_follow_up',
        'follow_up_date',
        'follow_up_notes',
        'cancelled_at',
        'cancellation_reason',
        'cancelled_by',
        'customer_rating',
        'customer_feedback',
        'mechanic_rating',
        'mechanic_feedback'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'estimated_duration' => 'datetime',
        'actual_start_at' => 'datetime',
        'actual_end_at' => 'datetime',
        'follow_up_date' => 'datetime',
        'cancelled_at' => 'datetime',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'vehicle_mileage' => 'integer',
        'vehicle_year' => 'integer',
        'customer_rating' => 'integer',
        'mechanic_rating' => 'integer',
        'requires_follow_up' => 'boolean',
        'cost_breakdown' => 'array'
    ];

    /**
     * Get the user that owns this appointment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the mechanic for this appointment.
     */
    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(Mechanic::class);
    }

    /**
     * Get the related entity (polymorphic).
     */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope for pending appointments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for confirmed appointments.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope for completed appointments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for cancelled appointments.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope for appointments by service type.
     */
    public function scopeByServiceType($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    /**
     * Scope for appointments by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for appointments on a specific date.
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('scheduled_at', $date);
    }

    /**
     * Scope for upcoming appointments.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now())
            ->whereIn('status', ['pending', 'confirmed']);
    }

    /**
     * Scope for past appointments.
     */
    public function scopePast($query)
    {
        return $query->where('scheduled_at', '<', now());
    }

    /**
     * Check if appointment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if appointment is confirmed.
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if appointment is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if appointment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if appointment is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if appointment is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->scheduled_at > now() && in_array($this->status, ['pending', 'confirmed']);
    }

    /**
     * Check if appointment is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->scheduled_at < now() && in_array($this->status, ['pending', 'confirmed']);
    }

    /**
     * Get appointment status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'in_progress' => 'purple',
            'completed' => 'green',
            'cancelled' => 'red',
            'no_show' => 'gray',
            default => 'gray'
        };
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
            default => 'blue'
        };
    }

    /**
     * Get formatted estimated cost.
     */
    public function getFormattedEstimatedCostAttribute(): string
    {
        if (!$this->estimated_cost) {
            return 'TBD';
        }
        return $this->currency . ' ' . number_format($this->estimated_cost, 2);
    }

    /**
     * Get formatted actual cost.
     */
    public function getFormattedActualCostAttribute(): string
    {
        if (!$this->actual_cost) {
            return 'TBD';
        }
        return $this->currency . ' ' . number_format($this->actual_cost, 2);
    }

    /**
     * Get vehicle display name.
     */
    public function getVehicleDisplayNameAttribute(): string
    {
        return "{$this->vehicle_year} {$this->vehicle_make} {$this->vehicle_model}";
    }

    /**
     * Get appointment duration in minutes.
     */
    public function getDurationInMinutesAttribute(): int
    {
        if ($this->actual_start_at && $this->actual_end_at) {
            return $this->actual_start_at->diffInMinutes($this->actual_end_at);
        }
        
        if ($this->scheduled_at && $this->estimated_duration) {
            return $this->scheduled_at->diffInMinutes($this->estimated_duration);
        }
        
        return 0;
    }

    /**
     * Get time until appointment.
     */
    public function getTimeUntilAppointmentAttribute(): string
    {
        if ($this->scheduled_at <= now()) {
            return 'Overdue';
        }
        
        return $this->scheduled_at->diffForHumans();
    }

    /**
     * Confirm the appointment.
     */
    public function confirm(): bool
    {
        if ($this->status === 'pending') {
            $this->status = 'confirmed';
            return $this->save();
        }
        return false;
    }

    /**
     * Start the appointment.
     */
    public function start(): bool
    {
        if (in_array($this->status, ['pending', 'confirmed'])) {
            $this->status = 'in_progress';
            $this->actual_start_at = now();
            return $this->save();
        }
        return false;
    }

    /**
     * Complete the appointment.
     */
    public function complete(array $completionData = []): bool
    {
        if ($this->status === 'in_progress') {
            $this->status = 'completed';
            $this->actual_end_at = now();
            
            if (isset($completionData['actual_cost'])) {
                $this->actual_cost = $completionData['actual_cost'];
            }
            
            if (isset($completionData['mechanic_notes'])) {
                $this->mechanic_notes = $completionData['mechanic_notes'];
            }
            
            if (isset($completionData['requires_follow_up'])) {
                $this->requires_follow_up = $completionData['requires_follow_up'];
            }
            
            if (isset($completionData['follow_up_date'])) {
                $this->follow_up_date = $completionData['follow_up_date'];
            }
            
            return $this->save();
        }
        return false;
    }

    /**
     * Cancel the appointment.
     */
    public function cancel(string $reason, string $cancelledBy): bool
    {
        if (in_array($this->status, ['pending', 'confirmed'])) {
            $this->status = 'cancelled';
            $this->cancelled_at = now();
            $this->cancellation_reason = $reason;
            $this->cancelled_by = $cancelledBy;
            return $this->save();
        }
        return false;
    }

    /**
     * Mark as no show.
     */
    public function markAsNoShow(): bool
    {
        if (in_array($this->status, ['pending', 'confirmed'])) {
            $this->status = 'no_show';
            return $this->save();
        }
        return false;
    }

    /**
     * Add customer rating and feedback.
     */
    public function addCustomerRating(int $rating, string $feedback = null): bool
    {
        if ($this->isCompleted() && $rating >= 1 && $rating <= 5) {
            $this->customer_rating = $rating;
            $this->customer_feedback = $feedback;
            return $this->save();
        }
        return false;
    }

    /**
     * Add mechanic rating and feedback.
     */
    public function addMechanicRating(int $rating, string $feedback = null): bool
    {
        if ($this->isCompleted() && $rating >= 1 && $rating <= 5) {
            $this->mechanic_rating = $rating;
            $this->mechanic_feedback = $feedback;
            return $this->save();
        }
        return false;
    }

    /**
     * Generate unique appointment number.
     */
    public static function generateAppointmentNumber(): string
    {
        do {
            $number = 'APT-' . strtoupper(uniqid());
        } while (static::where('appointment_number', $number)->exists());
        
        return $number;
    }

    /**
     * Create a new appointment.
     */
    public static function createAppointment(array $data): self
    {
        $data['appointment_number'] = static::generateAppointmentNumber();
        
        return static::create($data);
    }

    /**
     * Get appointment statistics.
     */
    public static function getAppointmentStats(): array
    {
        return [
            'total_appointments' => static::count(),
            'pending_appointments' => static::pending()->count(),
            'confirmed_appointments' => static::confirmed()->count(),
            'completed_appointments' => static::completed()->count(),
            'cancelled_appointments' => static::cancelled()->count(),
            'upcoming_appointments' => static::upcoming()->count(),
            'overdue_appointments' => static::where('scheduled_at', '<', now())
                ->whereIn('status', ['pending', 'confirmed'])
                ->count(),
            'avg_rating' => static::completed()->whereNotNull('customer_rating')->avg('customer_rating') ?? 0,
            'completion_rate' => static::count() > 0 
                ? (static::completed()->count() / static::count()) * 100 
                : 0,
        ];
    }

    /**
     * Get appointments for a specific date range.
     */
    public static function getAppointmentsForDateRange($startDate, $endDate, $mechanicId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = static::whereBetween('scheduled_at', [$startDate, $endDate]);
        
        if ($mechanicId) {
            $query->where('mechanic_id', $mechanicId);
        }
        
        return $query->orderBy('scheduled_at')->get();
    }
}