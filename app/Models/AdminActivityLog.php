<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'admin_id',
        'admin_name',
        'admin_email',
        'action',
        'route_name',
        'subject',
        'ip_address',
        'status_code',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'status_code' => 'integer',
    ];
}
