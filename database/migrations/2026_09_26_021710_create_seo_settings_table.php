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
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_title')->nullable()->default('MINI CINE — Xem phim trực tuyến chất lượng 4K');
            $table->string('canonical_url')->nullable();
            $table->text('site_description')->nullable();
            $table->text('keywords')->nullable();
            $table->string('google_verification')->nullable();
            $table->string('bing_verification')->nullable();
            $table->string('favicon')->nullable();
            $table->string('logo')->nullable();
            $table->string('og_image')->nullable();
            $table->string('robots_meta')->default('index, follow');
            $table->text('custom_robots_txt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_settings');
    }
};
