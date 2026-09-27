<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (collect(Schema::getIndexes('premium_plans'))->contains('name', 'premium_plans_code_billing_period_unique')) {
            Schema::table('premium_plans', function (Blueprint $table) {
                $table->dropUnique('premium_plans_code_billing_period_unique');
            });
        }

        if (Schema::hasTable('premium_promotions') && !Schema::hasColumn('premium_promotions', 'applies_to_upgrades')) {
            Schema::table('premium_promotions', function (Blueprint $table) {
                $table->boolean('applies_to_upgrades')->default(false)->after('discount_value');
            });
        }

        if (Schema::hasTable('premium_transactions')) {
            Schema::table('premium_transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('premium_transactions', 'operation_type')) {
                    $table->string('operation_type', 20)->default('purchase')->after('discount_description');
                }
                if (!Schema::hasColumn('premium_transactions', 'upgrade_credit')) {
                    $table->unsignedInteger('upgrade_credit')->default(0)->after('operation_type');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('premium_transactions')) {
            $columns = array_values(array_filter(['operation_type', 'upgrade_credit'], fn ($column) => Schema::hasColumn('premium_transactions', $column)));
            if ($columns) {
                Schema::table('premium_transactions', function (Blueprint $table) use ($columns) {
                    $table->dropColumn($columns);
                });
            }
        }

        if (Schema::hasTable('premium_promotions') && Schema::hasColumn('premium_promotions', 'applies_to_upgrades')) {
            Schema::table('premium_promotions', function (Blueprint $table) {
                $table->dropColumn('applies_to_upgrades');
            });
        }
    }
};
