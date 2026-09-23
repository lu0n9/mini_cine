<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchlist_movies', function (Blueprint $table) {
            $table->foreignId('watchlist_id')
                ->constrained('watchlists')
                ->cascadeOnDelete();

            $table->foreignId('movie_id')
                ->constrained('movies')
                ->cascadeOnDelete();

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamps();

            $table->primary([
                'watchlist_id',
                'movie_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchlist_movies');
    }
};