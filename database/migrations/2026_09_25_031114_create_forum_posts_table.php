<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained('forum_categories')
                ->cascadeOnDelete();

            $table->string('title');
            $table->string('slug')->unique();

            $table->longText('content');

            $table->string('thumbnail')->nullable();

            $table->enum('status', [
                'draft',
                'published',
                'hidden',
            ])->default('published');

            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);

            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);

            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->index(['category_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['is_pinned', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_posts');
    }
};
