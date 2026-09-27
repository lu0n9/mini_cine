<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('premium_transactions', function (Blueprint $table) {
            $table->unsignedInteger('base_amount')->nullable()->after('amount');
            $table->unsignedInteger('discount_amount')->default(0)->after('base_amount');
            $table->string('discount_description')->nullable()->after('discount_amount');
        });

        DB::table('premium_transactions')
            ->whereNull('base_amount')
            ->update(['base_amount' => DB::raw('amount')]);
    }

    public function down(): void
    {
        Schema::table('premium_transactions', function (Blueprint $table) {
            $table->dropColumn(['base_amount', 'discount_amount', 'discount_description']);
        });
    }
};
