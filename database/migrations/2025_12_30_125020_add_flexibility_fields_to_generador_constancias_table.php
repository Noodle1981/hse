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
        Schema::table('generador_constancias', function (Blueprint $table) {
            $table->date('fecha_visita')->nullable()->after('user_id');
            $table->text('justificacion_firma')->nullable()->after('firma_responsable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('generador_constancias', function (Blueprint $table) {
            $table->dropColumn(['fecha_visita', 'justificacion_firma']);
        });
    }
};
