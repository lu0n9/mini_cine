<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('movie_id')
                ->constrained('movies')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('rating');

            $table->text('review')->nullable();

            $table->boolean('is_approved')
                ->default(true);

            $table->timestamps();

            $table->unique([
                'user_id',
                'movie_id'
            ]);

            $table->index('movie_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};