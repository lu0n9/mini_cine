<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subtitles', function (Blueprint $table) {
            $table->id();

            // Episode chứa subtitle
            $table->foreignId('episode_id')
                ->constrained('episodes')
                ->cascadeOnDelete();

            // Mã ngôn ngữ: vi, en, zh...
            $table->string('language', 10);

            // Tên hiển thị: Tiếng Việt, English...
            $table->string('label', 100);

            // Đường dẫn file subtitle
            $table->string('file_url', 500);

            // vtt, srt...
            $table->string('format', 20)->default('vtt');

            // Subtitle mặc định
            $table->boolean('is_default')->default(false);

            // Có cho phép sử dụng hay không
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Một episode không có 2 subtitle cùng ngôn ngữ
            $table->unique(['episode_id', 'language']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subtitles');
    }
};