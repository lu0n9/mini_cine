<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();

            // General Settings
            $table->string('site_name')->default('MINI CINE');
            $table->string('site_url')->default('http://localhost:8000');
            $table->string('contact_email')->nullable()->default('contact@minicine.vn');
            $table->string('contact_hotline')->nullable()->default('1900 0000');
            $table->string('timezone')->default('Asia/Ho_Chi_Minh');
            $table->string('default_locale', 10)->default('vi');
            $table->boolean('maintenance_mode')->default(false);

            // Player Settings
            $table->boolean('player_autoplay')->default(true);
            $table->boolean('player_auto_next')->default(true);
            $table->boolean('player_skip_intro')->default(true);
            $table->boolean('player_pip')->default(true);

            // Registration & Security Settings
            $table->boolean('allow_registration')->default(true);
            $table->boolean('email_verification')->default(false);
            $table->boolean('require_comment_approval')->default(false);
            $table->boolean('two_factor_auth')->default(false);
            $table->boolean('login_rate_limit')->default(true);

            // Social Login Settings
            $table->boolean('google_login')->default(true);
            $table->boolean('facebook_login')->default(false);
            $table->boolean('github_login')->default(false);

            $table->timestamps();
        });

        // Chèn bản ghi cài đặt mặc định ban đầu
        DB::table('system_settings')->insert([
            'site_name'                => 'MINI CINE',
            'site_url'                 => config('app.url', 'http://localhost:8000'),
            'contact_email'            => 'contact@minicine.vn',
            'contact_hotline'          => '1900 0000',
            'timezone'                 => 'Asia/Ho_Chi_Minh',
            'default_locale'           => 'vi',
            'maintenance_mode'         => false,
            'player_autoplay'          => true,
            'player_auto_next'         => true,
            'player_skip_intro'        => true,
            'player_pip'               => true,
            'allow_registration'       => true,
            'email_verification'       => false,
            'require_comment_approval' => false,
            'two_factor_auth'          => false,
            'login_rate_limit'         => true,
            'google_login'             => true,
            'facebook_login'           => false,
            'github_login'             => false,
            'created_at'               => now(),
            'updated_at'               => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
