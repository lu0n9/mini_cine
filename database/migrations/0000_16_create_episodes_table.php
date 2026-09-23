<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('movie_id')
                ->constrained('movies')
                ->cascadeOnDelete();

            $table->foreignId('season_id')
                ->nullable()
                ->constrained('seasons')
                ->nullOnDelete();

            $table->unsignedInteger('episode_number');

            $table->string('name')->nullable();

            $table->text('description')->nullable();

            $table->string('thumbnail')->nullable();

            $table->unsignedInteger('duration')->nullable();

            $table->string('video_url');

            $table->string('subtitle_url')->nullable();

            $table->enum('quality', [
                'HD',
                'Full HD',
                '2K',
                '4K'
            ])->default('HD');

            $table->boolean('is_free')->default(true);

            $table->boolean('is_published')->default(true);

            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->unique([
                'movie_id',
                'season_id',
                'episode_number'
            ]);

            $table->index([
                'movie_id',
                'season_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};