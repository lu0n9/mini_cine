<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movies', function (Blueprint $table) {

            if (!Schema::hasColumn('movies', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('poster');
            }

            if (!Schema::hasColumn('movies', 'tags')) {
                $table->text('tags')->nullable();
            }

            if (!Schema::hasColumn('movies', 'featured')) {
                $table->boolean('featured')->default(false);
            }

            if (!Schema::hasColumn('movies', 'popular')) {
                $table->boolean('popular')->default(false);
            }

            if (!Schema::hasColumn('movies', 'recommended')) {
                $table->boolean('recommended')->default(false);
            }

            if (!Schema::hasColumn('movies', 'release_date')) {
                $table->date('release_date')->nullable();
            }

            if (!Schema::hasColumn('movies', 'quality')) {
                $table->string('quality', 50)->nullable();
            }

            if (!Schema::hasColumn('movies', 'language')) {
                $table->string('language', 100)->nullable();
            }

            if (!Schema::hasColumn('movies', 'imdb_id')) {
                $table->string('imdb_id', 50)->nullable();
            }

            if (!Schema::hasColumn('movies', 'imdb_rating')) {
                $table->decimal('imdb_rating', 3, 1)->nullable();
            }

            if (!Schema::hasColumn('movies', 'tmdb_id')) {
                $table->unsignedBigInteger('tmdb_id')->nullable();
            }

            if (!Schema::hasColumn('movies', 'short_description')) {
                $table->text('short_description')->nullable();
            }

            if (!Schema::hasColumn('movies', 'seo_title')) {
                $table->string('seo_title')->nullable();
            }

            if (!Schema::hasColumn('movies', 'canonical_url')) {
                $table->string('canonical_url', 500)->nullable();
            }

            if (!Schema::hasColumn('movies', 'seo_description')) {
                $table->text('seo_description')->nullable();
            }

            if (!Schema::hasColumn('movies', 'seo_keywords')) {
                $table->text('seo_keywords')->nullable();
            }

            if (!Schema::hasColumn('movies', 'og_title')) {
                $table->string('og_title')->nullable();
            }

            if (!Schema::hasColumn('movies', 'og_image')) {
                $table->string('og_image', 500)->nullable();
            }

            if (!Schema::hasColumn('movies', 'og_description')) {
                $table->text('og_description')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {

            $columns = [
                'thumbnail',
                'tags',
                'featured',
                'popular',
                'recommended',
                'release_date',
                'quality',
                'language',
                'imdb_id',
                'imdb_rating',
                'tmdb_id',
                'short_description',
                'seo_title',
                'canonical_url',
                'seo_description',
                'seo_keywords',
                'og_title',
                'og_image',
                'og_description',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('movies', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};