<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'comments';

    protected $fillable = [
        'user_id',
        'movie_id',
        'parent_id',
        'content',
        'is_approved',
        'status',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function parent()
    {
        return $this->belongsTo(
            Comment::class,
            'parent_id'
        );
    }

    public function replies()
    {
        return $this->hasMany(
            Comment::class,
            'parent_id'
        );
    }

    public function likes()
    {
        return $this->hasMany(CommentLike::class);
    }

    public function moderations(): HasMany
    {
        return $this->hasMany(CommentModeration::class);
    }
    public function latestModeration()
    {
        return $this->hasOne(CommentModeration::class)->latestOfMany();
    }
}