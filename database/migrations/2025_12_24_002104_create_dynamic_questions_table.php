<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla para almacenar las PREGUNTAS de cada checklist.
     * Las preguntas son dinámicas y configurables por tipo.
     */
    public function up(): void
    {
        Schema::create('dynamic_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_checklist_id')
                  ->constrained('dynamic_checklists')
                  ->cascadeOnDelete();
            
            $table->string('label', 500);                 // Texto de la pregunta
            $table->string('type', 50);                   // text, textarea, select, radio, checkbox, number, date, signature
            $table->json('options')->nullable();          // Opciones para select/radio/checkbox: ["Sí", "No", "N/A"]
            $table->integer('order')->default(0);         // Orden de aparición
            $table->boolean('is_required')->default(false);
            $table->text('help_text')->nullable();        // Texto de ayuda para el usuario
            $table->timestamps();

            // Índice compuesto para ordenar preguntas por checklist
            $table->index(['dynamic_checklist_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dynamic_questions');
    }
};
