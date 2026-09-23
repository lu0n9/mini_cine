<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Watchlist extends Model
{
    use HasFactory;

    protected $table = 'watchlists';

    protected $fillable = [
        'user_id',
        'name',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movies()
    {
        return $this->belongsToMany(
            Movie::class,
            'watchlist_movies',
            'watchlist_id',
            'movie_id'
        );
    }
}