<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Episode extends Model
{
    use HasFactory;

    protected $table = 'episodes';

    protected $fillable = [
        'movie_id',
        'season_id',
        'episode_number',
        'name',
        'slug',
        'description',
        'duration',
        'status',
    ];

    protected $casts = [
        'episode_number' => 'integer',
        'duration' => 'integer',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function sources()
    {
        return $this->hasMany(MovieSource::class);
    }

    public function watchHistories()
    {
        return $this->hasMany(WatchHistory::class);
    }
    public function subtitles()
    {
        return $this->hasMany(Subtitle::class);
    }
    public function videoProcessingJobs(): HasMany
    {
        return $this->hasMany(VideoProcessingJob::class);
    }
}