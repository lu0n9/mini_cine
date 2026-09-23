<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Collection extends Model
{
    protected $table = 'collections';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'poster',
        'backdrop',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(
            Movie::class,
            'collection_movie',
            'collection_id',
            'movie_id'
        )
        ->withPivot('sort_order')
        ->withTimestamps();
    }
}