<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('premium_promotions', 'discount_type')) {
            Schema::table('premium_promotions', function (Blueprint $table) {
                $table->enum('discount_type', ['percentage', 'fixed'])->nullable()->after('action_url');
                $table->unsignedInteger('discount_value')->nullable()->after('discount_type');
            });
        }

        if (!Schema::hasTable('premium_promotion_plan')) {
            Schema::create('premium_promotion_plan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('premium_promotion_id')->constrained('premium_promotions')->cascadeOnDelete();
                $table->foreignId('premium_plan_id')->constrained('premium_plans')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['premium_promotion_id', 'premium_plan_id'], 'prem_promo_plan_unique');
            });
        } elseif (!collect(Schema::getIndexes('premium_promotion_plan'))->contains('name', 'prem_promo_plan_unique')) {
            Schema::table('premium_promotion_plan', function (Blueprint $table) {
                $table->unique(['premium_promotion_id', 'premium_plan_id'], 'prem_promo_plan_unique');
            });
        }

        if (!Schema::hasColumn('premium_transactions', 'premium_promotion_id')) {
            Schema::table('premium_transactions', function (Blueprint $table) {
                $table->foreignId('premium_promotion_id')
                    ->nullable()
                    ->after('user_premium_coupon_id')
                    ->constrained('premium_promotions')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('premium_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('premium_promotion_id');
        });

        Schema::dropIfExists('premium_promotion_plan');

        Schema::table('premium_promotions', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value']);
        });
    }
};
