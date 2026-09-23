<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $table = 'people';

    protected $fillable = [
        'name',
        'slug',
        'avatar',
        'biography',
    ];

    public function movies()
    {
        return $this->belongsToMany(
            Movie::class,
            'movie_people',
            'person_id',
            'movie_id'
        )->withPivot('role');
    }
    
}