<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumComment extends Model
{
    use HasFactory;

    protected $table = 'forum_comments';

    protected $fillable = [
        'user_id',
        'post_id',
        'parent_id',
        'content',
        'status',
        'likes_count',
    ];

    protected $casts = [
        'likes_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(
            ForumPost::class,
            'post_id'
        );
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            ForumComment::class,
            'parent_id'
        );
    }

    public function replies(): HasMany
    {
        return $this->hasMany(
            ForumComment::class,
            'parent_id'
        );
    }

    public function likes(): HasMany
    {
        return $this->hasMany(
            ForumCommentLike::class,
            'comment_id'
        );
    }
}