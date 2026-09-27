<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_cron_runs', function (Blueprint $table) {
            $table->id();
            $table->string('job_key', 100)->unique();
            $table->string('status', 20);
            $table->text('message')->nullable();
            $table->unsignedInteger('duration_ms')->default(0);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_cron_runs');
    }
};
