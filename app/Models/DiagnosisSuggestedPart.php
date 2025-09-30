<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiagnosisSuggestedPart extends Model
{
    use HasFactory;

    protected $fillable = [
        'diagnosis_result_id',
        'car_part_id',
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
        'user_notes',
    ];

    protected $casts = [
        'relevance_score' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'is_required' => 'boolean',
        'is_recommended' => 'boolean',
        'is_alternative' => 'boolean',
        'user_selected' => 'boolean',
        'user_purchased' => 'boolean',
        'purchased_at' => 'datetime',
    ];

    /**
     * Get the diagnosis result that owns this suggested part.
     */
    public function diagnosisResult(): BelongsTo
    {
        return $this->belongsTo(DiagnosisResult::class);
    }

    /**
     * Get the car part that is suggested.
     */
    public function carPart(): BelongsTo
    {
        return $this->belongsTo(CarPart::class);
    }

    /**
     * Get priority display name.
     */
    public function getPriorityDisplayAttribute(): string
    {
        return match($this->priority) {
            1 => 'High Priority',
            2 => 'Medium Priority',
            3 => 'Low Priority',
            default => 'Unknown Priority'
        };
    }

    /**
     * Get priority color class.
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            1 => 'text-red-600 bg-red-100',
            2 => 'text-yellow-600 bg-yellow-100',
            3 => 'text-green-600 bg-green-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }

    /**
     * Get relevance score percentage.
     */
    public function getRelevancePercentageAttribute(): int
    {
        return (int) ($this->relevance_score * 100);
    }

    /**
     * Get formatted estimated cost.
     */
    public function getFormattedEstimatedCostAttribute(): string
    {
        if (!$this->estimated_cost) {
            return 'Not specified';
        }

        return $this->cost_currency . ' ' . number_format($this->estimated_cost, 2);
    }

    /**
     * Get installation difficulty display.
     */
    public function getInstallationDifficultyDisplayAttribute(): string
    {
        return match($this->installation_difficulty) {
            'easy' => 'Easy',
            'medium' => 'Medium',
            'hard' => 'Hard',
            'professional' => 'Professional Only',
            default => ucfirst($this->installation_difficulty)
        };
    }

    /**
     * Get installation difficulty color.
     */
    public function getInstallationDifficultyColorAttribute(): string
    {
        return match($this->installation_difficulty) {
            'easy' => 'text-green-600 bg-green-100',
            'medium' => 'text-yellow-600 bg-yellow-100',
            'hard' => 'text-orange-600 bg-orange-100',
            'professional' => 'text-red-600 bg-red-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }

    /**
     * Get installation time display.
     */
    public function getInstallationTimeDisplayAttribute(): string
    {
        if (!$this->estimated_installation_time) {
            return 'Not specified';
        }

        if ($this->estimated_installation_time < 60) {
            return "{$this->estimated_installation_time} minutes";
        }

        $hours = floor($this->estimated_installation_time / 60);
        $minutes = $this->estimated_installation_time % 60;

        if ($minutes > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$hours} hour" . ($hours > 1 ? 's' : '');
    }

    /**
     * Get status display.
     */
    public function getStatusDisplayAttribute(): string
    {
        if ($this->user_purchased) {
            return 'Purchased';
        }

        if ($this->user_selected) {
            return 'Selected';
        }

        return 'Suggested';
    }

    /**
     * Get status color.
     */
    public function getStatusColorAttribute(): string
    {
        if ($this->user_purchased) {
            return 'text-green-600 bg-green-100';
        }

        if ($this->user_selected) {
            return 'text-blue-600 bg-blue-100';
        }

        return 'text-gray-600 bg-gray-100';
    }

    /**
     * Scope a query to only include required parts.
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope a query to only include recommended parts.
     */
    public function scopeRecommended($query)
    {
        return $query->where('is_recommended', true);
    }

    /**
     * Scope a query to only include alternative parts.
     */
    public function scopeAlternative($query)
    {
        return $query->where('is_alternative', true);
    }

    /**
     * Scope a query to order by priority.
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority')->orderBy('relevance_score', 'desc');
    }

    /**
     * Scope a query to filter by high priority.
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 1);
    }

    /**
     * Scope a query to filter by user selected parts.
     */
    public function scopeUserSelected($query)
    {
        return $query->where('user_selected', true);
    }

    /**
     * Scope a query to filter by user purchased parts.
     */
    public function scopeUserPurchased($query)
    {
        return $query->where('user_purchased', true);
    }

    /**
     * Mark part as selected by user.
     */
    public function markAsSelected(): void
    {
        $this->update(['user_selected' => true]);
    }

    /**
     * Mark part as purchased by user.
     */
    public function markAsPurchased(): void
    {
        $this->update([
            'user_purchased' => true,
            'purchased_at' => now()
        ]);
    }

    /**
     * Add user notes.
     */
    public function addUserNotes(string $notes): void
    {
        $this->update(['user_notes' => $notes]);
    }
}