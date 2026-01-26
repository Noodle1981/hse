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
        Schema::create('consultoras', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('cuit')->unique();
            $table->integer('max_tecnicos')->default(5)->comment('Límite de licencias de técnicos');
            $table->json('config')->nullable()->comment('Configuraciones globales de la consultora');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultoras');
    }
};
