<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'provider',
        'plan_name',
        'status',
        'amount',
        'currency',
        'interval',
        'interval_count',
        'current_period_start',
        'current_period_end',
        'trial_start',
        'trial_end',
        'canceled_at',
        'ended_at',
        'features',
        'limits',
        'provider_data',
        'provider_customer_id',
        'payment_method_id',
        'payment_method_details'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'trial_start' => 'datetime',
        'trial_end' => 'datetime',
        'canceled_at' => 'datetime',
        'ended_at' => 'datetime',
        'features' => 'array',
        'limits' => 'array',
        'provider_data' => 'array',
        'payment_method_details' => 'array',
        'interval_count' => 'integer'
    ];

    /**
     * Get the user that owns this subscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'trialing']);
    }

    /**
     * Scope for cancelled subscriptions.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope for expired subscriptions.
     */
    public function scopeExpired($query)
    {
        return $query->where('current_period_end', '<', now());
    }

    /**
     * Scope for subscriptions by plan.
     */
    public function scopeByPlan($query, $planName)
    {
        return $query->where('plan_name', $planName);
    }

    /**
     * Scope for subscriptions by provider.
     */
    public function scopeByProvider($query, $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Check if subscription is active.
     */
    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'trialing']) && $this->current_period_end > now();
    }

    /**
     * Check if subscription is in trial.
     */
    public function isTrialing(): bool
    {
        return $this->status === 'trialing' && $this->trial_end && $this->trial_end > now();
    }

    /**
     * Check if subscription is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if subscription is expired.
     */
    public function isExpired(): bool
    {
        return $this->current_period_end < now();
    }

    /**
     * Check if subscription is past due.
     */
    public function isPastDue(): bool
    {
        return $this->status === 'past_due';
    }

    /**
     * Get days until next billing.
     */
    public function getDaysUntilNextBilling(): int
    {
        return max(0, now()->diffInDays($this->current_period_end, false));
    }

    /**
     * Get days remaining in trial.
     */
    public function getTrialDaysRemaining(): int
    {
        if (!$this->isTrialing()) {
            return 0;
        }
        
        return max(0, now()->diffInDays($this->trial_end, false));
    }

    /**
     * Get formatted amount with currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }

    /**
     * Get formatted interval.
     */
    public function getFormattedIntervalAttribute(): string
    {
        if ($this->interval_count > 1) {
            return "Every {$this->interval_count} {$this->interval}s";
        }
        
        return "Every {$this->interval}";
    }

    /**
     * Get subscription status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'green',
            'trialing' => 'blue',
            'past_due' => 'orange',
            'cancelled' => 'red',
            'unpaid' => 'red',
            default => 'gray'
        };
    }

    /**
     * Check if user has a specific feature.
     */
    public function hasFeature(string $feature): bool
    {
        if (!$this->isActive()) {
            return false;
        }
        
        return in_array($feature, $this->features ?? []);
    }

    /**
     * Check if user is within a specific limit.
     */
    public function isWithinLimit(string $limit, int $currentUsage = 0): bool
    {
        if (!$this->isActive()) {
            return false;
        }
        
        $limitValue = $this->limits[$limit] ?? null;
        
        if ($limitValue === null) {
            return true; // No limit set
        }
        
        return $currentUsage < $limitValue;
    }

    /**
     * Get remaining usage for a limit.
     */
    public function getRemainingUsage(string $limit, int $currentUsage = 0): int
    {
        if (!$this->isActive()) {
            return 0;
        }
        
        $limitValue = $this->limits[$limit] ?? null;
        
        if ($limitValue === null) {
            return PHP_INT_MAX; // No limit set
        }
        
        return max(0, $limitValue - $currentUsage);
    }

    /**
     * Cancel the subscription.
     */
    public function cancel(bool $immediately = false): bool
    {
        $this->status = 'cancelled';
        $this->canceled_at = now();
        
        if ($immediately) {
            $this->ended_at = now();
        }
        
        return $this->save();
    }

    /**
     * Reactivate a cancelled subscription.
     */
    public function reactivate(): bool
    {
        if (!$this->isCancelled()) {
            return false;
        }
        
        $this->status = 'active';
        $this->canceled_at = null;
        $this->ended_at = null;
        
        return $this->save();
    }

    /**
     * Update subscription period.
     */
    public function updatePeriod(\DateTime $start, \DateTime $end): bool
    {
        $this->current_period_start = $start;
        $this->current_period_end = $end;
        
        return $this->save();
    }

    /**
     * Get subscription statistics.
     */
    public static function getSubscriptionStats(): array
    {
        return [
            'total_active' => static::active()->count(),
            'total_cancelled' => static::cancelled()->count(),
            'total_trialing' => static::where('status', 'trialing')->count(),
            'total_past_due' => static::where('status', 'past_due')->count(),
            'monthly_revenue' => static::active()->where('interval', 'month')->sum('amount'),
            'yearly_revenue' => static::active()->where('interval', 'year')->sum('amount'),
            'churn_rate' => static::count() > 0 
                ? (static::cancelled()->count() / static::count()) * 100 
                : 0,
        ];
    }

    /**
     * Get plan statistics.
     */
    public static function getPlanStats(): array
    {
        return static::selectRaw('plan_name, COUNT(*) as count, SUM(amount) as revenue')
            ->active()
            ->groupBy('plan_name')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    }
}