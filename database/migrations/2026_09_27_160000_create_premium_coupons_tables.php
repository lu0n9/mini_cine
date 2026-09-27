<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('premium_coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed']);
            $table->unsignedInteger('discount_value');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('usage_count')->default(0);
            $table->unsignedInteger('reserved_count')->default(0);
            $table->enum('eligibility_type', [
                'all',
                'account_age_days',
                'watch_hours',
                'premium_spend',
            ])->default('all');
            $table->unsignedInteger('eligibility_value')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('user_premium_coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('premium_coupon_id')->constrained('premium_coupons')->cascadeOnDelete();
            $table->timestamp('granted_at');
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'premium_coupon_id']);
        });

        Schema::table('premium_transactions', function (Blueprint $table) {
            $table->foreignId('premium_coupon_id')
                ->nullable()
                ->after('subscription_id')
                ->constrained('premium_coupons')
                ->nullOnDelete();
            $table->foreignId('user_premium_coupon_id')
                ->nullable()
                ->after('premium_coupon_id')
                ->constrained('user_premium_coupons')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('premium_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_premium_coupon_id');
            $table->dropConstrainedForeignId('premium_coupon_id');
        });

        Schema::dropIfExists('user_premium_coupons');
        Schema::dropIfExists('premium_coupons');
    }
};
