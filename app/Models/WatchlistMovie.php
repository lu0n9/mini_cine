<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class WatchlistMovie extends Pivot
{
    use HasFactory;

    protected $table = 'watchlist_movies';

    public function watchlist()
    {
        return $this->belongsTo(Watchlist::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}