<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watch_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('movie_id')
                ->constrained('movies')
                ->cascadeOnDelete();

            $table->foreignId('episode_id')
                ->nullable()
                ->constrained('episodes')
                ->nullOnDelete();

            // Số giây đã xem
            $table->unsignedInteger('watch_time')
                ->default(0);

            // Tổng thời lượng video
            $table->unsignedInteger('duration')
                ->nullable();

            $table->timestamp('last_watched_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'user_id',
                'movie_id'
            ]);

            $table->index([
                'user_id',
                'last_watched_at'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watch_histories');
    }
};