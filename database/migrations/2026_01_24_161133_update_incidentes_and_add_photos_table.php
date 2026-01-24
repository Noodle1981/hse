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
        // 1. Actualizar tabla de incidentes existente
        Schema::table('incidentes', function (Blueprint $table) {
            if (!Schema::hasColumn('incidentes', 'titulo')) {
                $table->string('titulo')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('incidentes', 'ubicacion_especifica')) {
                $table->string('ubicacion_especifica')->nullable()->after('descripcion');
            }
            if (!Schema::hasColumn('incidentes', 'estado')) {
                $table->string('estado')->default('reportado')->after('medidas_tomadas');
            }
            
            // Ajustar enums si es necesario (SQLite no soporta bien el cambio de enum, 
            // pero podemos asegurar que el código maneje los nuevos valores o usar strings)
        });

        // 2. Crear tabla para fotos de incidentes
        if (!Schema::hasTable('incidente_fotos')) {
            Schema::create('incidente_fotos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('incidente_id')->constrained('incidentes')->cascadeOnDelete();
                $table->string('ruta_archivo');
                $table->string('descripcion')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidente_fotos');

        Schema::table('incidentes', function (Blueprint $table) {
            $table->dropColumn(['titulo', 'ubicacion_especifica', 'estado']);
        });
    }
};
