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
        Schema::create('generador_constancias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained()->nullable();
            $table->foreignId('user_id')->constrained()->nullable();
            $table->string('tipo_visita');
            $table->string('observacion', length: 3000);
            $table->text('firma_responsable')->nullable();
            $table->date('date_firma_responsable')->nullable();
            $table->text('firma_instructor')->nullable();
            $table->date('date_firma_instructor')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generador_constancias');
    }
};
