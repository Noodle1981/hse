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
        Schema::table('epp_items', function (Blueprint $table) {
            $table->integer('stock_actual')->default(0)->after('codigo_norma');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('epp_items', function (Blueprint $table) {
            $table->dropColumn('stock_actual');
        });
    }
};
