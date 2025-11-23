<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'billing_cycle',
        'price',
        'currency',
        'next_billing_date',
        'trial_ends_at',
        'cancelled_at',
        'cancellation_reason',
        'payment_method_id',
        'stripe_subscription_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'next_billing_date' => 'date',
        'trial_ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function usage()
    {
        return $this->hasMany(SubscriptionUsage::class);
    }

    public function billingHistory()
    {
        return $this->hasMany(BillingHistory::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeTrial($query)
    {
        return $query->where('status', 'trial');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isTrial()
    {
        return $this->status === 'trial';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isExpired()
    {
        return $this->status === 'expired';
    }

    public function isInTrial()
    {
        return $this->isTrial() && $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2) . ' ' . $this->currency;
    }

    public function getDaysUntilBillingAttribute()
    {
        if (!$this->next_billing_date) {
            return null;
        }

        return Carbon::now()->diffInDays($this->next_billing_date, false);
    }

    public function getTrialDaysRemainingAttribute()
    {
        if (!$this->isInTrial()) {
            return 0;
        }

        return Carbon::now()->diffInDays($this->trial_ends_at, false);
    }

    public function getUsageForMonth($month = null, $year = null)
    {
        $date = $month && $year ? Carbon::create($year, $month, 1) : Carbon::now();
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        return $this->usage()
            ->whereBetween('usage_date', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy('action_type')
            ->map(function ($items) {
                return $items->sum('usage_count');
            });
    }

    public function getUsageForAction($actionType, $month = null, $year = null)
    {
        $date = $month && $year ? Carbon::create($year, $month, 1) : Carbon::now();
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        return $this->usage()
            ->where('action_type', $actionType)
            ->whereBetween('usage_date', [$startOfMonth, $endOfMonth])
            ->sum('usage_count');
    }

    public function canPerformAction($actionType)
    {
        if (!$this->isActive() && !$this->isInTrial()) {
            return false;
        }

        $plan = $this->plan;
        if (!$plan) {
            return false;
        }

        $limit = $plan->getLimit($actionType);
        if ($limit === 'unlimited' || $limit === null) {
            return true;
        }

        $currentUsage = $this->getUsageForAction($actionType);
        return $currentUsage < $limit;
    }

    public function getRemainingUsage($actionType)
    {
        $plan = $this->plan;
        if (!$plan) {
            return 0;
        }

        $limit = $plan->getLimit($actionType);
        if ($limit === 'unlimited' || $limit === null) {
            return 'unlimited';
        }

        $currentUsage = $this->getUsageForAction($actionType);
        return max(0, $limit - $currentUsage);
    }

    public function recordUsage($actionType, $count = 1, $metadata = null)
    {
        $today = Carbon::today();

        $usage = $this->usage()
            ->where('action_type', $actionType)
            ->where('usage_date', $today)
            ->first();

        if ($usage) {
            $usage->increment('usage_count', $count);
            if ($metadata) {
                $usage->update(['metadata' => $metadata]);
            }
        } else {
            $this->usage()->create([
                'action_type' => $actionType,
                'usage_count' => $count,
                'usage_date' => $today,
                'metadata' => $metadata,
            ]);
        }

        return $this;
    }
}