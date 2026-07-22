<?php

namespace App\DiagnosticEcosystem\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeMaintenanceRecommendation extends Model
{
    protected $table = 'de_maintenance_recommendations';

    protected $fillable = [
        'vehicle_profile_id',
        'recommendation_type',
        'title',
        'description',
        'priority',
        'due_at_mileage',
        'due_at_date',
        'reasoning',
        'status',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'due_at_mileage' => 'integer',
            'due_at_date' => 'date',
        ];
    }

    public function vehicleProfile(): BelongsTo
    {
        return $this->belongsTo(DeVehicleProfile::class, 'vehicle_profile_id');
    }
}
