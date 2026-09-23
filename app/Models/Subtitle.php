<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subtitle extends Model
{
    use HasFactory;

    protected $table = 'subtitles';

    protected $fillable = [
        'episode_id',
        'language',
        'label',
        'file_url',
        'format',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }
}