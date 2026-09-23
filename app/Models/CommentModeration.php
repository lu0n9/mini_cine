<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentModeration extends Model
{
    protected $table = 'comment_moderations';

    protected $fillable = [
        'comment_id',
        'source',
        'score',
        'category',
        'reason',
        'decision',
    ];

    protected $casts = [
        'score' => 'decimal:4',
    ];

    /**
     * Moderation thuộc về comment.
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }
}