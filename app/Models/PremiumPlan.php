<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PremiumPlan extends Model
{
    protected $fillable = ['code', 'name', 'billing_period', 'price', 'share_limit', 'is_active'];

    protected $casts = ['price' => 'integer', 'share_limit' => 'integer', 'is_active' => 'boolean'];

    public function subscriptions()
    {
        return $this->hasMany(PremiumSubscription::class);
    }
}
