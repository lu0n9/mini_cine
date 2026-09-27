<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGatewayConfig extends Model
{
    protected $fillable = ['provider', 'merchant_code', 'secret_key', 'payment_url', 'is_enabled'];

    protected $hidden = ['secret_key'];

    protected function casts(): array
    {
        return [
            'secret_key' => 'encrypted',
            'is_enabled' => 'boolean',
        ];
    }
}
