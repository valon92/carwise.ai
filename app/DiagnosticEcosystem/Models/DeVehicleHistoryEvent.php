<?php

namespace App\DiagnosticEcosystem\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeVehicleHistoryEvent extends Model
{
    public $timestamps = false;

    protected $table = 'de_vehicle_history_events';

    protected $fillable = [
        'vehicle_profile_id',
        'event_type',
        'event_date',
        'mileage',
        'title',
        'description',
        'metadata',
        'diagnostic_scan_id',
        'ai_analysis_id',
        'created_by_user_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
            'created_at' => 'datetime',
            'mileage' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function vehicleProfile(): BelongsTo
    {
        return $this->belongsTo(DeVehicleProfile::class, 'vehicle_profile_id');
    }

    public function diagnosticScan(): BelongsTo
    {
        return $this->belongsTo(DeDiagnosticScan::class, 'diagnostic_scan_id');
    }

    public function aiAnalysis(): BelongsTo
    {
        return $this->belongsTo(DeAiAnalysis::class, 'ai_analysis_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
