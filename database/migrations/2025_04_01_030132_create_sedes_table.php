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
        Schema::create('sedes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_sede');
            $table->string('tipo_sede')->nullable();
            $table->string('direccion')->nullable();
            $table->string('correo')->nullable();
            $table->string('telefono')->nullable();
            $table->string('localidad')->nullable();
            $table->string('responsable_1_nombre')->nullable();
            $table->string('responsable_1_cargo')->nullable();
            $table->string('responsable_1_email')->nullable();
            $table->string('responsable_2_nombre')->nullable();
            $table->string('responsable_2_cargo')->nullable();
            $table->string('responsable_2_email')->nullable();
            $table->string('responsable_3_nombre')->nullable();
            $table->string('responsable_3_cargo')->nullable();
            $table->string('responsable_3_email')->nullable();
            $table->foreignId('empresa_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sedes');
    }
};
