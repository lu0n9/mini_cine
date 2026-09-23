<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movie_country', function (Blueprint $table) {
            $table->foreignId('movie_id')
                ->constrained('movies')
                ->cascadeOnDelete();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnDelete();

            $table->primary(['movie_id', 'country_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movie_country');
    }
};