<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabla para almacenar las PLANTILLAS de checklist dinámicos.
     * Cada checklist es una plantilla reutilizable que contiene preguntas.
     */
    public function up(): void
    {
        Schema::create('dynamic_checklists', function (Blueprint $table) {
            $table->id();
            $table->string('title');                      // Ej: "Control de EPP"
            $table->text('description')->nullable();      // Descripción opcional
            $table->string('category')->nullable();       // 'hys', 'calidad', 'seguimiento'
            $table->boolean('is_active')->default(true);  // Para desactivar sin borrar
            $table->timestamps();

            // Índices para búsquedas frecuentes
            $table->index('category');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dynamic_checklists');
    }
};
