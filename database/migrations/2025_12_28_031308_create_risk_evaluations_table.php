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
        Schema::create('risk_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('risk_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Técnico que realiza la evaluación
            $table->integer('probability'); // 1-5
            $table->integer('severity'); // 1-5 (Impacto)
            $table->integer('score'); // prob * severity (1-25)
            $table->string('level'); // "Bajo", "Medio", "Alto", "Crítico"
            $table->string('color'); // hex color para el badge
            $table->text('justification')->nullable();
            $table->timestamp('evaluation_date')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_evaluations');
    }
};
