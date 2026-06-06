<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('epp_delivery_items', function (Blueprint $table) {
            // Trazabilidad del lote del fabricante (Res. SRT 299/11 y Res. 18/2025)
            $table->string('lote')->nullable()->after('tipo_entrega');
            // Fecha de fabricación — base para calcular vida útil de fábrica
            $table->date('fecha_fabricacion')->nullable()->after('lote');
            // Fecha de vencimiento explícita marcada por el fabricante
            $table->date('fecha_vencimiento_fabricante')->nullable()->after('fecha_fabricacion');
            // Estado del ciclo de vida de este lote entregado
            $table->enum('estado', ['activo', 'dado_de_baja', 'vencido', 'reemplazado'])
                ->default('activo')
                ->after('fecha_vencimiento_fabricante');
            // Motivo por el que se dio de baja (nullable cuando está activo)
            $table->string('motivo_baja')->nullable()->after('estado');
            // Fecha exacta de la baja
            $table->dateTime('fecha_baja')->nullable()->after('motivo_baja');
        });
    }

    public function down(): void
    {
        Schema::table('epp_delivery_items', function (Blueprint $table) {
            $table->dropColumn([
                'lote',
                'fecha_fabricacion',
                'fecha_vencimiento_fabricante',
                'estado',
                'motivo_baja',
                'fecha_baja',
            ]);
        });
    }
};
