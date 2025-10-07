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
        // Maintenance tracking fields
        'current_mileage',
        'last_service_date',
        'last_service_mileage',
        'last_oil_change_date',
        'last_oil_change_mileage',
        'oil_change_interval',
        'last_tire_change_date',
        'last_tire_change_mileage',
        'tire_change_interval',
        'last_timing_belt_change_date',
        'last_timing_belt_change_mileage',
        'timing_belt_change_interval',
        'last_brake_pad_change_date',
        'last_brake_pad_change_mileage',
        'brake_pad_change_interval',
        'last_air_filter_change_date',
        'last_air_filter_change_mileage',
        'air_filter_change_interval',
        'last_fuel_filter_change_date',
        'last_fuel_filter_change_mileage',
        'fuel_filter_change_interval',
        'last_spark_plugs_change_date',
        'last_spark_plugs_change_mileage',
        'spark_plugs_change_interval',
        'battery_installation_date',
        'battery_life_years',
        'current_tire_type',
        'last_seasonal_tire_change_date',
        'insurance_expiry_date',
        'registration_expiry_date',
        'maintenance_notifications_enabled',
        'notification_advance_days'
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
            // Maintenance tracking casts
            'current_mileage' => 'integer',
            'last_service_date' => 'date',
            'last_service_mileage' => 'integer',
            'last_oil_change_date' => 'date',
            'last_oil_change_mileage' => 'integer',
            'oil_change_interval' => 'integer',
            'last_tire_change_date' => 'date',
            'last_tire_change_mileage' => 'integer',
            'tire_change_interval' => 'integer',
            'last_timing_belt_change_date' => 'date',
            'last_timing_belt_change_mileage' => 'integer',
            'timing_belt_change_interval' => 'integer',
            'last_brake_pad_change_date' => 'date',
            'last_brake_pad_change_mileage' => 'integer',
            'brake_pad_change_interval' => 'integer',
            'last_air_filter_change_date' => 'date',
            'last_air_filter_change_mileage' => 'integer',
            'air_filter_change_interval' => 'integer',
            'last_fuel_filter_change_date' => 'date',
            'last_fuel_filter_change_mileage' => 'integer',
            'fuel_filter_change_interval' => 'integer',
            'last_spark_plugs_change_date' => 'date',
            'last_spark_plugs_change_mileage' => 'integer',
            'spark_plugs_change_interval' => 'integer',
            'battery_installation_date' => 'date',
            'battery_life_years' => 'integer',
            'last_seasonal_tire_change_date' => 'date',
            'insurance_expiry_date' => 'date',
            'registration_expiry_date' => 'date',
            'maintenance_notifications_enabled' => 'boolean',
            'notification_advance_days' => 'integer'
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

    /**
     * Get the maintenance history for the car.
     */
    public function maintenanceHistory(): HasMany
    {
        return $this->hasMany(CarMaintenanceHistory::class);
    }

    /**
     * Get the maintenance notifications for the car.
     */
    public function maintenanceNotifications(): HasMany
    {
        return $this->hasMany(MaintenanceNotification::class);
    }

    /**
     * Get active maintenance notifications for the car.
     */
    public function activeMaintenanceNotifications(): HasMany
    {
        return $this->hasMany(MaintenanceNotification::class)->unread();
    }

    /**
     * Get upcoming maintenance notifications for the car.
     */
    public function upcomingMaintenanceNotifications(): HasMany
    {
        return $this->hasMany(MaintenanceNotification::class)->upcoming();
    }

    /**
     * Get overdue maintenance notifications for the car.
     */
    public function overdueMaintenanceNotifications(): HasMany
    {
        return $this->hasMany(MaintenanceNotification::class)->overdue();
    }

    /**
     * Check if car has any overdue maintenance.
     */
    public function hasOverdueMaintenance(): bool
    {
        return $this->overdueMaintenanceNotifications()->exists();
    }

    /**
     * Check if car has any upcoming maintenance.
     */
    public function hasUpcomingMaintenance(): bool
    {
        return $this->upcomingMaintenanceNotifications()->exists();
    }

    /**
     * Get maintenance statistics for the car.
     */
    public function getMaintenanceStats(): array
    {
        return CarMaintenanceHistory::getMaintenanceStats($this->id);
    }

    /**
     * Update current mileage and check for maintenance notifications.
     */
    public function updateMileage(int $newMileage): void
    {
        $this->update(['current_mileage' => $newMileage]);
        
        // Create maintenance notifications if enabled
        if ($this->maintenance_notifications_enabled) {
            MaintenanceNotification::createForCar($this);
        }
    }

    /**
     * Get next maintenance due date.
     */
    public function getNextMaintenanceDue(): ?string
    {
        $nextNotification = $this->upcomingMaintenanceNotifications()
            ->orderBy('due_date')
            ->first();
            
        return $nextNotification ? $nextNotification->due_date->format('Y-m-d') : null;
    }

    /**
     * Get maintenance status color.
     */
    public function getMaintenanceStatusColor(): string
    {
        if ($this->hasOverdueMaintenance()) {
            return 'red';
        } elseif ($this->hasUpcomingMaintenance()) {
            return 'orange';
        }
        return 'green';
    }


    /**
     * Get the primary image for this car
     */
    public function getPrimaryImage()
    {
        return \App\Models\CarImage::getPrimaryImage(
            $this->brand,
            $this->model,
            $this->year,
            $this->color
        );
    }

    /**
     * Get all images for this car
     */
    public function getAllImages()
    {
        return \App\Models\CarImage::getCarImages(
            $this->brand,
            $this->model,
            $this->year,
            $this->color
        );
    }

    /**
     * Get car image URL with fallback
     */
    public function getImageUrl(): string
    {
        $image = $this->getPrimaryImage();
        return $image ? $image->image_url : '/images/cars/default-car.svg';
    }

    /**
     * Get car thumbnail URL with fallback
     */
    public function getThumbnailUrl(): string
    {
        $image = $this->getPrimaryImage();
        return $image ? $image->thumbnail_url : '/images/cars/default-car.svg';
    }
}