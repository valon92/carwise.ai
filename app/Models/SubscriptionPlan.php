<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'description',
        'price',
        'currency',
        'billing_cycle',
        'diagnoses_per_month',
        'features',
        'limits',
        'is_active',
        'is_popular',
    ];

    protected $casts = [
        'features' => 'array',
        'limits' => 'array',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    public function activeSubscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan_id')->where('status', 'active');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2) . ' ' . $this->currency;
    }

    public function getDiagnosesLimitAttribute()
    {
        return $this->diagnoses_per_month === null ? 'unlimited' : $this->diagnoses_per_month;
    }

    public function hasFeature($feature)
    {
        return in_array($feature, $this->features ?? []);
    }

    public function getLimit($limitType)
    {
        return $this->limits[$limitType] ?? null;
    }
}
   
