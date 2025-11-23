<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'click_id',
        'part_id',
        'brand',
        'category',
        'order_id',
        'customer_email',
        'purchase_amount',
        'currency',
        'commission_rate',
        'commission_amount',
        'status',
        'purchase_date',
        'payment_date'
    ];

    protected $casts = [
        'purchase_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'purchase_date' => 'datetime',
        'payment_date' => 'datetime'
    ];

    /**
     * Get the part that was purchased
     */
    public function part()
    {
        return $this->belongsTo(CarPart::class);
    }

    /**
     * Get the click that led to this commission
     */
    public function click()
    {
        return $this->belongsTo(AffiliateClick::class, 'click_id', 'click_id');
    }

    /**
     * Scope for pending commissions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for paid commissions
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for cancelled commissions
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}
