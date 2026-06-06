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
        Schema::table('epp_delivery_items', function (Blueprint $table) {
            $table->uuid('codigo_qr_trazabilidad')->nullable()->unique()->after('fecha_baja');
            $table->boolean('tiene_qr_res18')->default(false)->after('codigo_qr_trazabilidad');
            $table->string('qr_fabricante_url')->nullable()->after('tiene_qr_res18');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('epp_delivery_items', function (Blueprint $table) {
            $table->dropColumn(['qr_fabricante_url', 'tiene_qr_res18', 'codigo_qr_trazabilidad']);
        });
    }
};
