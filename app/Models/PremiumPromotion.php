<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PremiumPromotion extends Model
{
    protected $fillable = [
        'title',
        'message',
        'action_url',
        'discount_type',
        'discount_value',
        'applies_to_upgrades',
        'starts_at',
        'ends_at',
        'available_days',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'available_days' => 'array',
        'discount_value' => 'integer',
        'applies_to_upgrades' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function isAvailableNow(): bool
    {
        return $this->is_active
            && (!$this->starts_at || $this->starts_at->isPast())
            && (!$this->ends_at || $this->ends_at->isFuture())
            && (empty($this->available_days)
                || in_array((int) now()->dayOfWeekIso, array_map('intval', $this->available_days), true));
    }

    public function plans()
    {
        return $this->belongsToMany(PremiumPlan::class, 'premium_promotion_plan');
    }

    public function discountFor(int $price): int
    {
        if (!$this->discount_type || !$this->discount_value) {
            return 0;
        }

        $discount = $this->discount_type === 'percentage'
            ? (int) floor($price * $this->discount_value / 100)
            : $this->discount_value;

        return min($price, max(0, $discount));
    }
}
