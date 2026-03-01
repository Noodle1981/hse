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
        Schema::table('epp_replacement_alerts', function (Blueprint $table) {
            $table->string('exchange_order_id')->nullable()->after('exchange_response');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('epp_replacement_alerts', function (Blueprint $table) {
            $table->dropColumn('exchange_order_id');
        });
    }
};
