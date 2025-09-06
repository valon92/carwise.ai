<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiagnosisResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'diagnosis_session_id',
        'problem_title',
        'problem_description',
        'severity',
        'confidence_score',
        'likely_causes',
        'recommended_actions',
        'estimated_costs',
        'ai_insights',
        'related_issues',
        'requires_immediate_attention',
        'ai_model_version',
        'analysis_completed_at'
    ];

    protected $casts = [
        'likely_causes' => 'array',
        'recommended_actions' => 'array',
        'estimated_costs' => 'array',
        'ai_insights' => 'array',
        'related_issues' => 'array',
        'requires_immediate_attention' => 'boolean',
        'analysis_completed_at' => 'datetime'
    ];

    /**
     * Get the diagnosis session that owns the result.
     */
    public function diagnosisSession(): BelongsTo
    {
        return $this->belongsTo(DiagnosisSession::class);
    }

    /**
     * Get the severity level as a color class.
     */
    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            'critical' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get the confidence level description.
     */
    public function getConfidenceLevelAttribute(): string
    {
        return match(true) {
            $this->confidence_score >= 90 => 'Very High',
            $this->confidence_score >= 75 => 'High',
            $this->confidence_score >= 60 => 'Medium',
            $this->confidence_score >= 40 => 'Low',
            default => 'Very Low'
        };
    }

    /**
     * Check if the result requires immediate attention.
     */
    public function requiresImmediateAttention(): bool
    {
        return $this->requires_immediate_attention || $this->severity === 'critical';
    }

    /**
     * Get the total estimated cost range.
     */
    public function getTotalCostRangeAttribute(): ?string
    {
        if (!$this->estimated_costs || empty($this->estimated_costs)) {
            return null;
        }

        $minTotal = 0;
        $maxTotal = 0;

        foreach ($this->estimated_costs as $cost) {
            if (isset($cost['min']) && isset($cost['max'])) {
                $minTotal += $cost['min'];
                $maxTotal += $cost['max'];
            }
        }

        if ($minTotal > 0 && $maxTotal > 0) {
            return '$' . number_format($minTotal) . ' - $' . number_format($maxTotal);
        }

        return null;
    }
}
