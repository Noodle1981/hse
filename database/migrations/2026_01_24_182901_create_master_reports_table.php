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
        Schema::create('master_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('sedes')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('solicitado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->string('titulo');
            $table->string('periodo', 7); // YYYY-MM
            $table->text('resumen_ejecutivo')->nullable();
            $table->text('observaciones_matriz')->nullable();
            $table->enum('estado', ['solicitado', 'en_redaccion', 'finalizado', 'archivado'])->default('solicitado');
            $table->json('datos_anexo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_reports');
    }
};
