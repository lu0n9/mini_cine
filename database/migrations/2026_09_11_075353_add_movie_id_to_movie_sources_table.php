<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movie_sources', function (Blueprint $table) {
            $table->foreignId('movie_id')
                ->nullable()
                ->after('id')
                ->constrained('movies')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('movie_sources', function (Blueprint $table) {
            $table->dropForeign(['movie_id']);
            $table->dropColumn('movie_id');
        });
    }
};