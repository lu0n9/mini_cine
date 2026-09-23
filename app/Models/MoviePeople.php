<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MoviePeople extends Pivot
{
    use HasFactory;

    protected $table = 'movie_people';

    protected $fillable = [
        'movie_id',
        'person_id',
        'role',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}