<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PremiumShare extends Model
{
    protected $fillable = ['subscription_id', 'shared_user_id'];

    public function subscription()
    {
        return $this->belongsTo(PremiumSubscription::class, 'subscription_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'shared_user_id');
    }
}
