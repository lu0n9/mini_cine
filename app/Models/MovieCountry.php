<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MovieCountry extends Pivot
{
    use HasFactory;

    protected $table = 'movie_countries';

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}