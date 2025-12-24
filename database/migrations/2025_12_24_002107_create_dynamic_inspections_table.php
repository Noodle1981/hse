<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla para almacenar cada EJECUCIÓN/LLENADO de un checklist.
     * Una inspección es una instancia de un checklist completado por un usuario.
     */
    public function up(): void
    {
        Schema::create('dynamic_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_checklist_id')
                  ->constrained('dynamic_checklists')
                  ->cascadeOnDelete();
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();
            
            $table->timestamp('completed_at')->nullable();  // Cuándo se completó
            $table->text('notes')->nullable();              // Observaciones generales
            $table->string('status')->default('draft');     // draft, completed
            $table->timestamps();

            // Índices para consultas frecuentes
            $table->index(['dynamic_checklist_id', 'user_id']);
            $table->index('completed_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dynamic_inspections');
    }
};
