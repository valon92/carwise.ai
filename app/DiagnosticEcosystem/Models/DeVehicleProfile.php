<?php

namespace App\DiagnosticEcosystem\Models;

use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeVehicleProfile extends Model
{
    protected $table = 'de_vehicle_profiles';

    protected $fillable = [
        'user_id',
        'vin',
        'license_plate',
        'nickname',
        'current_mileage',
        'legacy_car_id',
        'manufacturer',
        'brand',
        'model',
        'year',
        'engine',
        'fuel_type',
        'transmission',
        'horsepower',
        'factory_equipment',
        'vehicle_options',
        'last_vin_decode_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'horsepower' => 'integer',
            'current_mileage' => 'integer',
            'factory_equipment' => 'array',
            'vehicle_options' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function legacyCar(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'legacy_car_id');
    }

    public function vinDecodes(): HasMany
    {
        return $this->hasMany(DeVinDecode::class, 'vehicle_profile_id');
    }

    public function latestVinDecode(): BelongsTo
    {
        return $this->belongsTo(DeVinDecode::class, 'last_vin_decode_id');
    }

    public function connectorPairings(): HasMany
    {
        return $this->hasMany(DeConnectorPairing::class, 'vehicle_profile_id');
    }

    public function diagnosticScans(): HasMany
    {
        return $this->hasMany(DeDiagnosticScan::class, 'vehicle_profile_id');
    }

    public function aiAnalyses(): HasMany
    {
        return $this->hasMany(DeAiAnalysis::class, 'vehicle_profile_id');
    }

    public function historyEvents(): HasMany
    {
        return $this->hasMany(DeVehicleHistoryEvent::class, 'vehicle_profile_id');
    }

    public function maintenanceRecommendations(): HasMany
    {
        return $this->hasMany(DeMaintenanceRecommendation::class, 'vehicle_profile_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->nickname) {
            return $this->nickname;
        }

        $parts = array_filter([$this->year, $this->brand, $this->model]);

        return $parts ? implode(' ', $parts) : 'Vehicle '.$this->vin;
    }
}
