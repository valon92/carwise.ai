<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_id',
        'provider',
        'type',
        'status',
        'amount',
        'currency',
        'description',
        'metadata',
        'related_type',
        'related_id',
        'payment_method',
        'payment_method_id',
        'payment_method_details',
        'provider_data',
        'provider_transaction_id',
        'provider_fee_id',
        'refunded_amount',
        'refund_data',
        'refunded_at',
        'paid_at',
        'failed_at',
        'cancelled_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'metadata' => 'array',
        'payment_method_details' => 'array',
        'provider_data' => 'array',
        'refund_data' => 'array',
        'refunded_at' => 'datetime',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
        'cancelled_at' => 'datetime'
    ];

    /**
     * Get the user that owns this payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related entity (polymorphic).
     */
    public function related(): MorphTo
    {
        return $this->morphTo('related');
    }

    /**
     * Scope for successful payments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for failed payments.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for refunded payments.
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    /**
     * Scope for payments by provider.
     */
    public function scopeByProvider($query, $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Scope for payments by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for recent payments.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Check if payment is successful.
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if payment is refunded.
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * Check if payment can be refunded.
     */
    public function canBeRefunded(): bool
    {
        return $this->isSuccessful() && $this->refunded_amount < $this->amount;
    }

    /**
     * Get the remaining refundable amount.
     */
    public function getRefundableAmount(): float
    {
        return $this->amount - $this->refunded_amount;
    }

    /**
     * Get formatted amount with currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }

    /**
     * Get formatted refunded amount with currency.
     */
    public function getFormattedRefundedAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->refunded_amount, 2);
    }

    /**
     * Get payment status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completed' => 'green',
            'pending' => 'yellow',
            'processing' => 'blue',
            'failed' => 'red',
            'refunded' => 'gray',
            'cancelled' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Get payment method display name.
     */
    public function getPaymentMethodDisplayAttribute(): string
    {
        if (!$this->payment_method_details) {
            return ucfirst($this->payment_method ?? 'Unknown');
        }

        $details = $this->payment_method_details;

        return match($this->payment_method) {
            'card' => "**** **** **** {$details['last4']}",
            'paypal' => 'PayPal',
            'bank_transfer' => 'Bank Transfer',
            default => ucfirst($this->payment_method ?? 'Unknown')
        };
    }

    /**
     * Mark payment as completed.
     */
    public function markAsCompleted(): bool
    {
        $this->status = 'completed';
        $this->paid_at = now();
        return $this->save();
    }

    /**
     * Mark payment as failed.
     */
    public function markAsFailed(string $reason = null): bool
    {
        $this->status = 'failed';
        $this->failed_at = now();
        
        if ($reason) {
            $this->metadata = array_merge($this->metadata ?? [], ['failure_reason' => $reason]);
        }
        
        return $this->save();
    }

    /**
     * Mark payment as cancelled.
     */
    public function markAsCancelled(): bool
    {
        $this->status = 'cancelled';
        $this->cancelled_at = now();
        return $this->save();
    }

    /**
     * Process a refund.
     */
    public function processRefund(float $amount = null, string $reason = null): bool
    {
        if (!$this->canBeRefunded()) {
            return false;
        }

        $refundAmount = $amount ?? $this->getRefundableAmount();
        
        if ($refundAmount > $this->getRefundableAmount()) {
            return false;
        }

        $this->refunded_amount += $refundAmount;
        
        if ($this->refunded_amount >= $this->amount) {
            $this->status = 'refunded';
        }

        $this->refunded_at = now();
        
        $refundData = [
            'amount' => $refundAmount,
            'reason' => $reason,
            'refunded_at' => now()->toISOString()
        ];

        $this->refund_data = array_merge($this->refund_data ?? [], [$refundData]);

        return $this->save();
    }

    /**
     * Get total revenue for a period.
     */
    public static function getTotalRevenue($startDate = null, $endDate = null): float
    {
        $query = static::completed();
        
        if ($startDate) {
            $query->where('paid_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('paid_at', '<=', $endDate);
        }
        
        return $query->sum('amount');
    }

    /**
     * Get payment statistics.
     */
    public static function getPaymentStats($period = 30): array
    {
        $startDate = now()->subDays($period);
        
        return [
            'total_revenue' => static::getTotalRevenue($startDate),
            'total_payments' => static::completed()->where('paid_at', '>=', $startDate)->count(),
            'average_payment' => static::completed()->where('paid_at', '>=', $startDate)->avg('amount') ?? 0,
            'success_rate' => static::where('created_at', '>=', $startDate)->count() > 0 
                ? (static::completed()->where('created_at', '>=', $startDate)->count() / static::where('created_at', '>=', $startDate)->count()) * 100 
                : 0,
            'refund_rate' => static::where('created_at', '>=', $startDate)->count() > 0 
                ? (static::refunded()->where('created_at', '>=', $startDate)->count() / static::where('created_at', '>=', $startDate)->count()) * 100 
                : 0,
        ];
    }
}