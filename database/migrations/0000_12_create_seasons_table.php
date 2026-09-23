<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();

            $table->foreignId('movie_id')
                ->constrained('movies')
                ->cascadeOnDelete();

            $table->unsignedInteger('season_number');

            $table->string('name')->nullable();

            $table->text('description')->nullable();

            $table->string('poster')->nullable();

            $table->timestamps();

            $table->unique([
                'movie_id',
                'season_number'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
};