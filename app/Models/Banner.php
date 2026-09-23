<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $table = 'banners';

    protected $fillable = [
        'title',
        'description',
        'image',
        'link',
        'movie_id',
        'position',
        'status',
        'sort_order',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}