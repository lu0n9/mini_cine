<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('api_keys', function (Blueprint $table) {
            $table->string('type')->default('custom')->after('name');
            $table->string('endpoint')->nullable()->after('key_suffix');
        });

        // Add server_id to video_processing_jobs to track which server was used
        Schema::table('video_processing_jobs', function (Blueprint $table) {
            $table->unsignedBigInteger('server_id')->nullable()->after('episode_id');
        });
    }

    public function down(): void
    {
        Schema::table('api_keys', function (Blueprint $table) {
            $table->dropColumn(['type', 'endpoint']);
        });

        Schema::table('video_processing_jobs', function (Blueprint $table) {
            $table->dropColumn('server_id');
        });
    }
};
