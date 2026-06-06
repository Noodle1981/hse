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
        Schema::table('sedes', function (Blueprint $table) {
            $table->text('dias_operativos')->nullable();
            $table->time('hora_apertura')->nullable();
            $table->time('hora_cierre')->nullable();
            $table->boolean('es_roster_24_7')->default(false);
            $table->integer('radio_tolerancia_metros')->default(500);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sedes', function (Blueprint $table) {
            $table->dropColumn([
                'dias_operativos',
                'hora_apertura',
                'hora_cierre',
                'es_roster_24_7',
                'radio_tolerancia_metros',
            ]);
        });
    }
};
