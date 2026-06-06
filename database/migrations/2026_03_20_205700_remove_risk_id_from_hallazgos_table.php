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
        // Workaround para SQLite: Crear la tabla risks temporalmente si no existe
        // para que SQLite no falle al intentar leer/copiar el esquema de hallazgos
        // que tiene una clave foránea apuntando a ella.
        if (! Schema::hasTable('risks')) {
            Schema::create('risks', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }

        Schema::table('hallazgos', function (Blueprint $table) {
            if (Schema::hasColumn('hallazgos', 'risk_id')) {
                // En SQLite, debemos soltar la clave foránea ANTES de la columna
                // para que la emulación de alter table no intente agregarla de nuevo.
                $table->dropForeign(['risk_id']);
                $table->dropColumn('risk_id');
            }
        });

        // Eliminar la tabla temporal
        Schema::dropIfExists('risks');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hallazgos', function (Blueprint $table) {
            if (! Schema::hasColumn('hallazgos', 'risk_id')) {
                $table->foreignId('risk_id')->nullable()->comment('Remanente')->after('origen_id');
            }
        });
    }
};
