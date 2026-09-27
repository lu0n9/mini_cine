<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PremiumCoupon extends Model
{
    protected $fillable = [
        'code',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'starts_at',
        'ends_at',
        'available_days',
        'usage_limit',
        'usage_count',
        'reserved_count',
        'eligibility_type',
        'eligibility_value',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'available_days' => 'array',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'reserved_count' => 'integer',
        'eligibility_value' => 'integer',
        'is_active' => 'boolean',
    ];

    public function userGrants()
    {
        return $this->hasMany(UserPremiumCoupon::class);
    }

    public function isAvailable(): bool
    {
        return $this->is_active
            && (!$this->starts_at || $this->starts_at->isPast())
            && (!$this->ends_at || $this->ends_at->isFuture())
            && $this->isAvailableToday()
            && (!$this->usage_limit || ($this->usage_count + $this->reserved_count) < $this->usage_limit);
    }

    public function isAvailableToday(): bool
    {
        if (empty($this->available_days)) {
            return true;
        }

        return in_array((int) now()->dayOfWeekIso, array_map('intval', $this->available_days), true);
    }

    public function qualifies(User $user): bool
    {
        return match ($this->eligibility_type) {
            'account_age_days' => $user->created_at
                && $user->created_at->diffInDays(now()) >= $this->eligibility_value,
            'watch_hours' => WatchHistory::where('user_id', $user->id)
                ->sum('watch_time') >= $this->eligibility_value * 3600,
            'premium_spend' => PremiumTransaction::whereHas('subscription', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'paid')->sum('amount') >= $this->eligibility_value,
            default => true,
        };
    }

    public function discountFor(int $price): int
    {
        $discount = $this->discount_type === 'percentage'
            ? (int) floor($price * $this->discount_value / 100)
            : $this->discount_value;

        return min($price, max(0, $discount));
    }
}
