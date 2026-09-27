<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PremiumSubscription extends Model
{
    protected $fillable = ['user_id', 'premium_plan_id', 'status', 'starts_at', 'ends_at'];

    protected $casts = ['starts_at' => 'datetime', 'ends_at' => 'datetime'];

    public function plan()
    {
        return $this->belongsTo(PremiumPlan::class, 'premium_plan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shares()
    {
        return $this->hasMany(PremiumShare::class, 'subscription_id');
    }

    public function transactions()
    {
        return $this->hasMany(PremiumTransaction::class, 'subscription_id');
    }
}
