<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    /**
     * Get the suggested parts for this diagnosis result.
     */
    public function suggestedParts(): BelongsToMany
    {
        return $this->belongsToMany(CarPart::class, 'diagnosis_suggested_parts')
                    ->withPivot([
                        'suggestion_reason',
                        'priority',
                        'relevance_score',
                        'is_required',
                        'is_recommended',
                        'is_alternative',
                        'quantity_needed',
                        'usage_notes',
                        'estimated_cost',
                        'cost_currency',
                        'cost_breakdown',
                        'estimated_installation_time',
                        'installation_difficulty',
                        'installation_notes',
                        'user_selected',
                        'user_purchased',
                        'purchased_at',
                        'user_notes'
                    ])
                    ->withTimestamps();
    }

    /**
     * Get the diagnosis suggested parts pivot records.
     */
    public function diagnosisSuggestedParts()
    {
        return $this->hasMany(DiagnosisSuggestedPart::class);
    }

    /**
     * Get required parts only.
     */
    public function getRequiredParts()
    {
        return $this->suggestedParts()->wherePivot('is_required', true)->get();
    }

    /**
     * Get recommended parts only.
     */
    public function getRecommendedParts()
    {
        return $this->suggestedParts()->wherePivot('is_recommended', true)->get();
    }

    /**
     * Get alternative parts only.
     */
    public function getAlternativeParts()
    {
        return $this->suggestedParts()->wherePivot('is_alternative', true)->get();
    }

    /**
     * Get total estimated cost for all suggested parts.
     */
    public function getTotalPartsCost(): float
    {
        return $this->diagnosisSuggestedParts()
                    ->sum('estimated_cost') ?? 0.0;
    }

    /**
     * Get formatted total parts cost.
     */
    public function getFormattedTotalPartsCost(): string
    {
        $total = $this->getTotalPartsCost();
        return $total > 0 ? '$' . number_format($total, 2) : 'Not specified';
    }
}
