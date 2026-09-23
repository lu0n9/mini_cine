<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'approved',
                'spam',
                'hidden',
            ])
                ->default('approved')
                ->after('is_approved');

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex([
                'status',
            ]);

            $table->dropColumn('status');
        });
    }
};