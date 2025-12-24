<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla para almacenar las RESPUESTAS específicas de cada inspección.
     * Cada respuesta corresponde a una pregunta dentro de una inspección.
     */
    public function up(): void
    {
        Schema::create('dynamic_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_inspection_id')
                  ->constrained('dynamic_inspections')
                  ->cascadeOnDelete();
            $table->foreignId('dynamic_question_id')
                  ->constrained('dynamic_questions')
                  ->cascadeOnDelete();
            
            $table->text('value')->nullable();  // La respuesta (texto, JSON para arrays, o base64 para firmas)
            $table->timestamps();

            // Índice compuesto para búsquedas de respuestas por inspección
            $table->index(['dynamic_inspection_id', 'dynamic_question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dynamic_answers');
    }
};
