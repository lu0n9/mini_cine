<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movie_sources', function (Blueprint $table) {
            $table->id();

            $table->foreignId('episode_id')
                ->constrained('episodes')
                ->cascadeOnDelete();

            $table->string('name');

            $table->string('url');

            $table->string('type')->default('mp4');

            $table->enum('quality', [
                '360p',
                '480p',
                '720p',
                '1080p',
                '1440p',
                '2160p'
            ])->default('720p');

            $table->unsignedSmallInteger('priority')
                ->default(0);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index([
                'episode_id',
                'quality'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movie_sources');
    }
};