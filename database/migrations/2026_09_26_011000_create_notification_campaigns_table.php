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
        if (!Schema::hasTable('notification_campaigns')) {
            Schema::create('notification_campaigns', function (Blueprint $table) {
                $table->id();
                $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
                $table->string('title');
                $table->text('message');
                $table->string('type')->default('system');
                $table->string('target_type')->default('all');
                $table->string('target_description')->nullable();
                $table->string('action_text')->nullable();
                $table->string('action_url')->nullable();
                $table->unsignedInteger('recipient_count')->default(0);
                $table->boolean('channel_email')->default(true);
                $table->boolean('channel_web')->default(true);
                $table->string('status')->default('success');
                $table->text('error_message')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_campaigns');
    }
};

