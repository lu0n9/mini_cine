<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemCronRun extends Model
{
    protected $fillable = [
        'job_key',
        'status',
        'message',
        'duration_ms',
        'last_run_at',
    ];

    protected $casts = [
        'duration_ms' => 'integer',
        'last_run_at' => 'datetime',
    ];
}
