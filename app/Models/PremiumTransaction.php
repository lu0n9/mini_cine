<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PremiumTransaction extends Model
{
    protected $fillable = ['subscription_id', 'premium_coupon_id', 'user_premium_coupon_id', 'premium_promotion_id', 'order_reference', 'amount', 'base_amount', 'discount_amount', 'discount_description', 'operation_type', 'upgrade_credit', 'status', 'provider', 'provider_transaction_id', 'provider_response', 'paid_at'];

    protected $casts = ['amount' => 'integer', 'base_amount' => 'integer', 'discount_amount' => 'integer', 'upgrade_credit' => 'integer', 'provider_response' => 'array', 'paid_at' => 'datetime'];

    public function subscription()
    {
        return $this->belongsTo(PremiumSubscription::class, 'subscription_id');
    }

    public function coupon()
    {
        return $this->belongsTo(PremiumCoupon::class, 'premium_coupon_id');
    }

    public function userCoupon()
    {
        return $this->belongsTo(UserPremiumCoupon::class, 'user_premium_coupon_id');
    }

    public function promotion()
    {
        return $this->belongsTo(PremiumPromotion::class, 'premium_promotion_id');
    }
}
