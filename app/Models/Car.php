<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    protected $fillable = [
        'user_id',
        'brand',
        'model',
        'year',
        'vin',
        'color',
        'license_plate',
    ];

    protected $casts = [
        'year' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function diagnoses(): HasMany
    {
        return $this->hasMany(Diagnosis::class);
    }

    public function getRecentDiagnosesAttribute()
    {
        return $this->diagnoses()->latest()->take(3)->get();
    }

    public function getDiagnosisCountAttribute()
    {
        return $this->diagnoses()->count();
    }

    public function getLastDiagnosisAttribute()
    {
        $lastDiagnosis = $this->diagnoses()->latest()->first();
        return $lastDiagnosis ? $lastDiagnosis->created_at->diffForHumans() : 'Never';
    }
}
