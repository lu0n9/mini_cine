<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoProcessingJob extends Model
{
    protected $table = 'video_processing_jobs';

    protected $fillable = [
        'movie_id',
        'episode_id',
        'original_path',
        'output_path',
        'hls_path',
        'status',
        'progress',
        'qualities',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'progress' => 'integer',
        'qualities' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }
}
