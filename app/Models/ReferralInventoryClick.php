<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralInventoryClick extends Model
{
    protected $table = 'referral_inventory_clicks';

    protected $fillable = [
        'listing_ref',
        'destination_host',
        'user_id',
        'ip_hash',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
