<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarMaintenanceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'user_id',
        'maintenance_type',
        'title',
        'description',
        'service_date',
        'service_mileage',
        'cost',
        'currency',
        'service_provider',
        'service_provider_contact',
        'service_provider_address',
        'parts_used',
        'materials_used',
        'next_service_due_date',
        'next_service_due_mileage',
        'attachments',
        'notes',
        'status',
        'is_warranty_work'
    ];

    protected $casts = [
        'service_date' => 'date',
        'next_service_due_date' => 'date',
        'cost' => 'decimal:2',
        'parts_used' => 'array',
        'materials_used' => 'array',
        'attachments' => 'array',
        'is_warranty_work' => 'boolean'
    ];

    /**
     * Get the car that owns this maintenance record.
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * Get the user that owns this maintenance record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for completed maintenance.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for scheduled maintenance.
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope for maintenance by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('maintenance_type', $type);
    }

    /**
     * Scope for recent maintenance.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('service_date', '>=', now()->subDays($days));
    }

    /**
     * Get formatted cost.
     */
    public function getFormattedCostAttribute(): string
    {
        if (!$this->cost) {
            return 'N/A';
        }
        return $this->currency . ' ' . number_format($this->cost, 2);
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
            'general_service' => 'General Service',
            'inspection' => 'Inspection',
            default => ucfirst(str_replace('_', ' ', $this->maintenance_type))
        };
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completed' => 'green',
            'scheduled' => 'blue',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    /**
     * Check if maintenance is overdue.
     */
    public function isOverdue(): bool
    {
        if (!$this->next_service_due_date) {
            return false;
        }
        
        return $this->next_service_due_date < now()->toDateString();
    }

    /**
     * Get days until next service.
     */
    public function getDaysUntilNextService(): int
    {
        if (!$this->next_service_due_date) {
            return 0;
        }
        
        return max(0, now()->diffInDays($this->next_service_due_date, false));
    }

    /**
     * Get maintenance statistics for a car.
     */
    public static function getMaintenanceStats(int $carId): array
    {
        $maintenance = static::where('car_id', $carId)->completed();
        
        return [
            'total_records' => $maintenance->count(),
            'total_cost' => $maintenance->sum('cost'),
            'average_cost' => $maintenance->avg('cost') ?? 0,
            'last_service_date' => $maintenance->latest('service_date')->value('service_date'),
            'next_due_count' => static::where('car_id', $carId)
                ->where('next_service_due_date', '<=', now()->addDays(30))
                ->count(),
            'overdue_count' => static::where('car_id', $carId)
                ->where('next_service_due_date', '<', now())
                ->count()
        ];
    }
}