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
        Schema::create('capacitacion_virual_asistentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('capacitacion_virtual_id')->constrained()->nullable();
            $table->string('nombre_apellido')->nullable();
            $table->string('dni', length: 50)->nullable();
            $table->string('puesto')->nullable();
            $table->string('evaluacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capacitacion_virual_asistentes');
    }
};
