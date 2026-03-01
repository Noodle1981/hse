<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('epp_items', function (Blueprint $table) {
            // Vida útil desde fabricación (independiente del uso intenso)
            $table->integer('vida_util_fabricante_anios')->nullable()->after('vida_util_meses');
            // Unidad de medida: Unidad, Par, Caja
            $table->string('unidad_medida')->default('Unidad')->after('vida_util_fabricante_anios');
            // Punto de pedido: si stock_actual cae por debajo de esto, se dispara alerta a EPP Exchange
            $table->integer('stock_seguridad')->default(0)->after('stock_actual');
            // Si TRUE: ante un impacto/caída el EPP debe darse de baja obligatoriamente (ej: arnés, casco clase B)
            $table->boolean('requiere_control_critico')->default(false)->after('stock_seguridad');
        });
    }

    public function down(): void
    {
        Schema::table('epp_items', function (Blueprint $table) {
            $table->dropColumn([
                'vida_util_fabricante_anios',
                'unidad_medida',
                'stock_seguridad',
                'requiere_control_critico',
            ]);
        });
    }
};
