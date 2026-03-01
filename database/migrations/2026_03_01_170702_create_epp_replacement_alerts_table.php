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
        Schema::create('epp_replacement_alerts', function (Blueprint $table) {
            $table->id();
            
            // Relaciones Mínimas Indispensables (Nuestra DB real)
            $table->foreignId('epp_item_id')->constrained('epp_items')->onDelete('cascade');
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->onDelete('set null');
            
            // Datos del Trigger
            $table->enum('trigger_type', ['vencimiento_vida_util', 'stock_bajo', 'evento_critico']);
            $table->integer('cantidad_requerida');
            $table->enum('urgencia', ['alta', 'media', 'baja'])->default('media');
            $table->text('motivo')->nullable();
            
            // Estado y Respuesta de la Integración (Mock o Real)
            $table->enum('status', ['pendiente', 'cotizada', 'comprada', 'cancelada'])->default('pendiente');
            $table->json('exchange_response')->nullable()->comment('Sugerencias de proveedores obtenidas del Mock/API');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epp_replacement_alerts');
    }
};
