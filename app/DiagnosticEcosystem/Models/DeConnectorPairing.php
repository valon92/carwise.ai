<?php

namespace App\DiagnosticEcosystem\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeConnectorPairing extends Model
{
    protected $table = 'de_connector_pairings';

    protected $fillable = [
        'vehicle_profile_id',
        'connector_type',
        'device_identifier',
        'pairing_token',
        'capabilities',
        'last_connected_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'capabilities' => 'array',
            'last_connected_at' => 'datetime',
        ];
    }

    public function vehicleProfile(): BelongsTo
    {
        return $this->belongsTo(DeVehicleProfile::class, 'vehicle_profile_id');
    }

    public function diagnosticScans(): HasMany
    {
        return $this->hasMany(DeDiagnosticScan::class, 'connector_pairing_id');
    }
}
