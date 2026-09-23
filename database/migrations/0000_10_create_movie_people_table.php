<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movie_people', function (Blueprint $table) {
            $table->id();

            $table->foreignId('movie_id')
                ->constrained('movies')
                ->cascadeOnDelete();

            $table->foreignId('person_id')
                ->constrained('people')
                ->cascadeOnDelete();

            $table->enum('role', [
                'actor',
                'director',
                'writer',
                'producer',
                'cinematographer',
                'composer'
            ]);

            // Tên nhân vật mà diễn viên đóng
            $table->string('character_name')->nullable();

            $table->unsignedSmallInteger('display_order')
                ->default(0);

            $table->timestamps();

            $table->unique([
                'movie_id',
                'person_id',
                'role'
            ]);

            $table->index([
                'movie_id',
                'role'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movie_people');
    }
};