<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateClick extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_id',
        'brand',
        'category',
        'user_agent',
        'referrer',
        'ip_address',
        'session_id',
        'click_id',
        'timestamp',
        'converted',
        'conversion_date'
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'conversion_date' => 'datetime',
        'converted' => 'boolean'
    ];

    /**
     * Get the part that was clicked
     */
    public function part()
    {
        return $this->belongsTo(CarPart::class);
    }

    /**
     * Get the commission for this click
     */
    public function commission()
    {
        return $this->hasOne(AffiliateCommission::class, 'click_id', 'click_id');
    }
}
