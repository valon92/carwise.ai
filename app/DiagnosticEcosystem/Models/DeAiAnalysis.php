<?php

namespace App\DiagnosticEcosystem\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeAiAnalysis extends Model
{
    protected $table = 'de_ai_analyses';

    protected $fillable = [
        'diagnostic_scan_id',
        'vehicle_profile_id',
        'provider',
        'problem_description',
        'severity',
        'possible_causes',
        'repair_procedure',
        'estimated_repair_cost_min',
        'estimated_repair_cost_max',
        'estimated_repair_time_hours',
        'recommended_parts',
        'safety_recommendation',
        'can_continue_driving',
        'confidence_score',
        'raw_response',
    ];

    protected function casts(): array
    {
        return [
            'possible_causes' => 'array',
            'recommended_parts' => 'array',
            'raw_response' => 'array',
            'can_continue_driving' => 'boolean',
            'estimated_repair_cost_min' => 'float',
            'estimated_repair_cost_max' => 'float',
            'estimated_repair_time_hours' => 'float',
            'confidence_score' => 'float',
        ];
    }

    public function diagnosticScan(): BelongsTo
    {
        return $this->belongsTo(DeDiagnosticScan::class, 'diagnostic_scan_id');
    }

    public function vehicleProfile(): BelongsTo
    {
        return $this->belongsTo(DeVehicleProfile::class, 'vehicle_profile_id');
    }
}
