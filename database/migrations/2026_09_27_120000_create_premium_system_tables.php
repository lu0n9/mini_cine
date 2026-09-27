<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('premium_plans', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->enum('billing_period', ['monthly', 'yearly']);
            $table->unsignedInteger('price');
            $table->unsignedTinyInteger('share_limit')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['code', 'billing_period']);
        });

        Schema::create('premium_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('premium_plan_id')->constrained()->restrictOnDelete();
            $table->enum('status', ['pending', 'active', 'expired', 'cancelled'])->default('pending');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status', 'ends_at']);
        });

        Schema::create('premium_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('premium_subscriptions')->cascadeOnDelete();
            $table->string('order_reference')->unique();
            $table->unsignedInteger('amount');
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('provider')->default('vnpay');
            $table->string('provider_transaction_id')->nullable()->index();
            $table->json('provider_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('premium_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('premium_subscriptions')->cascadeOnDelete();
            $table->foreignId('shared_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['subscription_id', 'shared_user_id']);
            $table->unique('shared_user_id');
        });

        DB::table('premium_plans')->insert([
            ['code' => 'premium', 'name' => 'Premium', 'billing_period' => 'monthly', 'price' => 99000, 'share_limit' => 0, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'premium', 'name' => 'Premium', 'billing_period' => 'yearly', 'price' => 990000, 'share_limit' => 0, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'premium_extra', 'name' => 'Premium Extra', 'billing_period' => 'monthly', 'price' => 149000, 'share_limit' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'premium_extra', 'name' => 'Premium Extra', 'billing_period' => 'yearly', 'price' => 1490000, 'share_limit' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('premium_shares');
        Schema::dropIfExists('premium_transactions');
        Schema::dropIfExists('premium_subscriptions');
        Schema::dropIfExists('premium_plans');
    }
};
