<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_processing_jobs', function (Blueprint $table) {

            $table->id();

            $table->foreignId('movie_id')
                ->constrained('movies')
                ->cascadeOnDelete();

            $table->foreignId('episode_id')
                ->nullable()
                ->constrained('episodes')
                ->cascadeOnDelete();

            /*
             * File video gốc lưu tạm trên server
             */
            $table->string('original_path');

            /*
             * Thư mục HLS tạm trên server
             */
            $table->string('output_path')->nullable();

            /*
             * Đường dẫn master.m3u8 trên Supabase
             */
            $table->string('hls_path')->nullable();

            /*
             * pending
             * processing
             * uploading
             * completed
             * failed
             */
            $table->string('status')->default('pending')->index();

            /*
             * 0 -> 100
             */
            $table->unsignedTinyInteger('progress')->default(0);

            /*
             * 480p / 720p / 1080p
             */
            $table->json('qualities')->nullable();

            /*
             * Lỗi nếu FFmpeg hoặc Supabase fail
             */
            $table->text('error_message')->nullable();

            $table->timestamp('started_at')->nullable();

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_processing_jobs');
    }
};