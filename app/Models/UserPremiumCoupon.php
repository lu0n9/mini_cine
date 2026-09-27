<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPremiumCoupon extends Model
{
    protected $fillable = [
        'user_id',
        'premium_coupon_id',
        'granted_at',
        'reserved_at',
        'redeemed_at',
    ];

    protected $casts = [
        'granted_at' => 'datetime',
        'reserved_at' => 'datetime',
        'redeemed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(PremiumCoupon::class, 'premium_coupon_id');
    }
}
