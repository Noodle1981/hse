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
        Schema::create('constancia_actividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('generador_constancia_id')->constrained()->cascadeOnDelete();
            $table->string('actividad_type'); // Polymorphic: DynamicInspection, Training, RiskEvaluation, EppDelivery
            $table->unsignedBigInteger('actividad_id');
            $table->timestamps();

            $table->index(['actividad_type', 'actividad_id']);
            $table->unique(['generador_constancia_id', 'actividad_type', 'actividad_id'], 'constancia_actividad_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('constancia_actividades');
    }
};
