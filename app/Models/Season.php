<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;

    protected $table = 'seasons';

    protected $fillable = [
        'movie_id',
        'name',
        'season_number',
        'description',
    ];

    protected $casts = [
        'season_number' => 'integer',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }
}