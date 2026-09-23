<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovieGenre extends Model
{
    use HasFactory;

    protected $table = 'movie_genre';

    protected $fillable = [
        'movie_id',
        'genre_id',
    ];

    /**
     * Thể loại thuộc về bộ phim
     */
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    /**
     * Bộ phim thuộc về thể loại
     */
    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }
}