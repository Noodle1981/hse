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
        Schema::create('hallazgos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->comment('Técnico que detectó el hallazgo');
            $table->string('titulo');
            $table->text('descripcion');
            $table->string('ubicacion_especifica')->nullable()->comment('Ej: Escalera principal, 2do piso');
            $table->enum('criticidad', ['bajo', 'medio', 'alto', 'critico'])->default('medio');
            $table->enum('estado', ['pendiente', 'en_proceso', 'resuelto', 'verificado', 'cerrado'])->default('pendiente');
            $table->date('plazo_correccion')->nullable();
            $table->string('responsable_correccion')->nullable();
            $table->dateTime('fecha_deteccion');
            $table->dateTime('fecha_resolucion')->nullable();
            
            // Relación polimórfica con origen (Inspección, Constancia, Manual)
            $table->string('origen_type')->nullable();
            $table->unsignedBigInteger('origen_id')->nullable();
            
            // Vinculación opcional con matriz de riesgo
            $table->foreignId('risk_id')->nullable()->constrained('risks')->nullOnDelete();
            
            $table->timestamps();
            
            $table->index(['origen_type', 'origen_id']);
            $table->index(['estado', 'criticidad']);
            $table->index('fecha_deteccion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hallazgos');
    }
};
