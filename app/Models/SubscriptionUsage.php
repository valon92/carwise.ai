<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionUsage extends Model
{
    use HasFactory;

    protected $table = 'subscription_usage';

    protected $fillable = [
        'user_id',
        'subscription_id',
        'action_type',
        'usage_count',
        'usage_date',
        'metadata',
    ];

    protected $casts = [
        'usage_date' => 'date',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function scopeForAction($query, $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('usage_date', $date);
    }

    public function scopeForMonth($query, $month, $year)
    {
        return $query->whereMonth('usage_date', $month)
                    ->whereYear('usage_date', $year);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionUsage extends Model
{
    use HasFactory;

    protected $table = 'subscription_usage';

    protected $fillable = [
        'user_id',
        'subscription_id',
        'action_type',
        'usage_count',
        'usage_date',
        'metadata',
    ];

    protected $casts = [
        'usage_date' => 'date',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function scopeForAction($query, $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('usage_date', $date);
    }

    public function scopeForMonth($query, $month, $year)
    {
        return $query->whereMonth('usage_date', $month)
                    ->whereYear('usage_date', $year);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}






