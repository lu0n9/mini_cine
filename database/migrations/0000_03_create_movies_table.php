<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();

            $table->string('original_title')->nullable();

            $table->text('description')->nullable();

            $table->string('poster')->nullable();
            $table->string('backdrop')->nullable();

            $table->string('trailer_url')->nullable();

            $table->unsignedSmallInteger('release_year')->nullable();

            $table->unsignedSmallInteger('duration')->nullable();

            $table->decimal('rating', 3, 1)->default(0);

            $table->unsignedInteger('rating_count')->default(0);

            $table->enum('type', [
                'single',
                'series'
            ])->default('single');

            $table->enum('status', [
                'draft',
                'ongoing',
                'completed'
            ])->default('completed');

            $table->enum('quality', [
                'HD',
                'Full HD',
                '2K',
                '4K'
            ])->default('HD');

            $table->enum('language', [
                'vietsub',
                'thuyet_minh',
                'long_tieng'
            ])->default('vietsub');

            $table->boolean('is_featured')->default(false);

            $table->boolean('is_published')->default(true);

            $table->timestamp('published_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('release_year');
            $table->index('rating');
            $table->index('type');
            $table->index('status');
            $table->index('is_featured');
            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};