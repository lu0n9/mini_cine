<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('movie_id')
                ->constrained('movies')
                ->cascadeOnDelete();

            $table->foreignId('episode_id')
                ->nullable()
                ->constrained('episodes')
                ->nullOnDelete();

            $table->enum('type', [
                'video_error',
                'subtitle_error',
                'wrong_information',
                'broken_link',
                'other'
            ]);

            $table->text('message');

            $table->enum('status', [
                'pending',
                'processing',
                'resolved',
                'rejected'
            ])->default('pending');

            $table->text('admin_note')->nullable();

            $table->timestamps();

            $table->index([
                'movie_id',
                'episode_id'
            ]);

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};