<?php

namespace App\DiagnosticEcosystem\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeDiagnosticScan extends Model
{
    protected $table = 'de_diagnostic_scans';

    protected $fillable = [
        'vehicle_profile_id',
        'connector_pairing_id',
        'scan_date',
        'mileage',
        'source',
        'engine_dtcs',
        'abs_errors',
        'airbag_errors',
        'transmission_errors',
        'battery_health',
        'oil_life',
        'tire_pressure',
        'live_sensor_data',
        'ecu_info',
        'vehicle_status',
        'raw_payload',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scan_date' => 'datetime',
            'mileage' => 'integer',
            'engine_dtcs' => 'array',
            'abs_errors' => 'array',
            'airbag_errors' => 'array',
            'transmission_errors' => 'array',
            'battery_health' => 'array',
            'oil_life' => 'array',
            'tire_pressure' => 'array',
            'live_sensor_data' => 'array',
            'ecu_info' => 'array',
            'vehicle_status' => 'array',
            'raw_payload' => 'array',
        ];
    }

    public function vehicleProfile(): BelongsTo
    {
        return $this->belongsTo(DeVehicleProfile::class, 'vehicle_profile_id');
    }

    public function connectorPairing(): BelongsTo
    {
        return $this->belongsTo(DeConnectorPairing::class, 'connector_pairing_id');
    }

    public function aiAnalyses(): HasMany
    {
        return $this->hasMany(DeAiAnalysis::class, 'diagnostic_scan_id');
    }
}
