<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comment_moderations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('comment_id')
                ->constrained('comments')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Nguồn kiểm duyệt
            |--------------------------------------------------------------------------
            | rule = bộ lọc Laravel
            | ai   = AI
            | admin = Admin quyết định
            */
            $table->string('source', 20);

            /*
            |--------------------------------------------------------------------------
            | Điểm AI
            |--------------------------------------------------------------------------
            | Ví dụ:
            | 0.20 = ít khả năng vi phạm
            | 0.95 = khả năng vi phạm cao
            */
            $table->decimal('score', 5, 4)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Loại vi phạm
            |--------------------------------------------------------------------------
            | profanity
            | insult
            | spam
            | hate
            | sexual
            | threat
            | clean
            */
            $table->string('category', 50)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Lý do
            |--------------------------------------------------------------------------
            */
            $table->text('reason')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Quyết định
            |--------------------------------------------------------------------------
            */
            $table->string('decision', 20)->nullable();

            $table->timestamps();

            $table->index('comment_id');
            $table->index('source');
            $table->index('decision');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_moderations');
    }
};