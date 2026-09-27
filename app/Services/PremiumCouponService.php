<?php

namespace App\Services;

use App\Models\PremiumCoupon;
use App\Models\User;
use App\Models\UserPremiumCoupon;

class PremiumCouponService
{
    public function grantEligibleCoupons(User $user): void
    {
        PremiumCoupon::query()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->where(function ($query) {
                $query->whereNull('usage_limit')->orWhereRaw('(usage_count + reserved_count) < usage_limit');
            })
            ->get()
            ->each(function (PremiumCoupon $coupon) use ($user) {
                if (!$coupon->isAvailable() || !$coupon->qualifies($user)) {
                    return;
                }

                $now = now();
                UserPremiumCoupon::query()->insertOrIgnore([
                    'user_id' => $user->id,
                    'premium_coupon_id' => $coupon->id,
                    'granted_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            });
    }
}
