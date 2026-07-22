<?php

namespace App\DiagnosticEcosystem\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeVinDecode extends Model
{
    protected $table = 'de_vin_decodes';

    public $timestamps = true;

    protected $fillable = [
        'vehicle_profile_id',
        'vin',
        'provider',
        'raw_response',
        'manufacturer',
        'brand',
        'model',
        'year',
        'engine',
        'fuel_type',
        'transmission',
        'horsepower',
        'specifications',
        'factory_equipment',
        'vehicle_options',
        'service_schedule',
        'recalls',
        'warranty',
        'decoded_at',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'horsepower' => 'integer',
            'raw_response' => 'array',
            'specifications' => 'array',
            'factory_equipment' => 'array',
            'vehicle_options' => 'array',
            'service_schedule' => 'array',
            'recalls' => 'array',
            'warranty' => 'array',
            'decoded_at' => 'datetime',
        ];
    }

    public function vehicleProfile(): BelongsTo
    {
        return $this->belongsTo(DeVehicleProfile::class, 'vehicle_profile_id');
    }
}
