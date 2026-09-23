<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovieSource extends Model
{
    use HasFactory;

    protected $table = 'movie_sources';

    protected $fillable = [
        'movie_id',
        'episode_id',
        'server_name',
        'source_url',
        'type',
        'quality',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }
}