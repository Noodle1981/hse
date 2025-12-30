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
        Schema::create('incidentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hallazgo_id')->nullable()->constrained()->nullOnDelete()->comment('Hallazgo que causó el incidente');
            $table->foreignId('sede_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->comment('Usuario que reporta');
            $table->enum('tipo', ['accidente', 'incidente', 'cuasi_accidente'])->default('incidente');
            $table->enum('gravedad', ['leve', 'moderado', 'grave', 'fatal'])->default('leve');
            $table->dateTime('fecha_ocurrencia');
            $table->text('descripcion');
            $table->integer('personas_afectadas')->default(0);
            $table->integer('dias_perdidos')->nullable();
            $table->text('medidas_tomadas')->nullable();
            $table->timestamps();
            
            $table->index('hallazgo_id');
            $table->index('sede_id');
            $table->index(['tipo', 'gravedad']);
            $table->index('fecha_ocurrencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidentes');
    }
};
