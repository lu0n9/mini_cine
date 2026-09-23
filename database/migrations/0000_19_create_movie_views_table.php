<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movie_views', function (Blueprint $table) {
            $table->id();

            $table->foreignId('movie_id')
                ->constrained('movies')
                ->cascadeOnDelete();

            $table->foreignId('episode_id')
                ->nullable()
                ->constrained('episodes')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('ip_address', 45)->nullable();

            $table->string('user_agent')->nullable();

            $table->timestamp('viewed_at');

            $table->timestamps();

            $table->index([
                'movie_id',
                'viewed_at'
            ]);

            $table->index([
                'episode_id',
                'viewed_at'
            ]);

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movie_views');
    }
};