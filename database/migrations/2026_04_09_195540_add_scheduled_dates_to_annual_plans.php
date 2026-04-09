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
        Schema::table('annual_plans', function (Blueprint $table) {
            $table->json('scheduled_dates')->nullable()->after('scheduled_months');
        });
    }

    public function down(): void
    {
        Schema::table('annual_plans', function (Blueprint $table) {
            $table->dropColumn('scheduled_dates');
        });
    }
};
