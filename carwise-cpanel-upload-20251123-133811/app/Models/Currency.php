<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'country',
        'exchange_rate',
        'is_active',
        'is_default',
        'sort_order'
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get the default currency
     */
    public static function getDefault()
    {
        return static::where('is_default', true)->first();
    }

    /**
     * Get active currencies
     */
    public static function getActive()
    {
        return static::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Convert amount from this currency to target currency
     */
    public function convertTo($targetCurrency, $amount)
    {
        if ($this->id === $targetCurrency->id) {
            return $amount;
        }

        // Convert to USD first, then to target currency
        $usdAmount = $amount / $this->exchange_rate;
        return $usdAmount * $targetCurrency->exchange_rate;
    }

    /**
     * Format amount with currency symbol
     */
    public function format($amount, $decimals = 2)
    {
        $formattedAmount = number_format($amount, $decimals);
        
        // Different currency symbol positions
        switch ($this->code) {
            case 'USD':
            case 'CAD':
            case 'AUD':
                return $this->symbol . $formattedAmount;
            case 'EUR':
            case 'GBP':
                return $this->symbol . $formattedAmount;
            case 'JPY':
                return $this->symbol . $formattedAmount;
            default:
                return $formattedAmount . ' ' . $this->symbol;
        }
    }

    /**
     * Get users who prefer this currency
     */
    public function users()
    {
        return $this->hasMany(User::class, 'preferred_currency_id');
    }
}