<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();

            $table->foreignId('movie_id')
                ->nullable()
                ->constrained('movies')
                ->nullOnDelete();

            $table->string('title');

            $table->text('description')->nullable();

            $table->string('image');

            $table->string('link')->nullable();

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamp('start_at')->nullable();

            $table->timestamp('end_at')->nullable();

            $table->timestamps();

            $table->index([
                'is_active',
                'sort_order'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};