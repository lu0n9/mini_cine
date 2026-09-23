<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $table = 'tags';

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(
            Movie::class,
            'movie_tag',
            'tag_id',
            'movie_id'
        )->withTimestamps();
    }
}