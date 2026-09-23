<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WatchHistory extends Model
{
    use HasFactory;

    protected $table = 'watch_histories';

    protected $fillable = [
        'user_id',
        'movie_id',
        'episode_id',
        'watch_time',
        'duration',
        'last_watched_at',
    ];

    protected $casts = [
        'watch_time' => 'integer',
        'duration' => 'integer',
        'last_watched_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }
}