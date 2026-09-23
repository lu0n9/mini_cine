<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $table = 'countries';

    protected $fillable = [
        'name',
        'slug',
        'code',
    ];

    public function movies()
    {
        return $this->belongsToMany(
            Movie::class,
            'movie_country',
            'country_id',
            'movie_id'
        );
    }
}