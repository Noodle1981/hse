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
        Schema::create('informes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained()->nullable();
            $table->foreignId('user_id')->constrained()->nullable();
            $table->string('titulo');
            $table->string('descripcion', length: 3000);
            $table->integer('capacitacion_asistentes')->nullable();
            $table->text('capacitacion_temas')->nullable();
            $table->date('fecha');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informes');
    }
};
