<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Agrega 'aprobado' al enum de estado en master_reports.
     * En SQLite los enums son CHECK constraints, por lo que debemos
     * recrear la columna con los nuevos valores permitidos.
     */
    public function up(): void
    {
        // SQLite no soporta ALTER COLUMN directamente, así que usamos
        // una columna temporal para preservar los datos existentes.
        Schema::table('master_reports', function (Blueprint $table) {
            // 1. Crear columna temporal sin restricción
            $table->string('estado_new')->default('solicitado');
        });

        // 2. Copiar los datos actuales a la columna temporal
        DB::statement('UPDATE master_reports SET estado_new = estado');

        Schema::table('master_reports', function (Blueprint $table) {
            // 3. Eliminar la columna original con el CHECK constraint antiguo
            $table->dropColumn('estado');
        });

        Schema::table('master_reports', function (Blueprint $table) {
            // 4. Crear la columna con el nuevo enum que incluye 'aprobado'
            $table->enum('estado', ['solicitado', 'en_redaccion', 'finalizado', 'aprobado', 'archivado'])
                  ->default('solicitado');
        });

        // 5. Restaurar los datos desde la columna temporal
        DB::statement('UPDATE master_reports SET estado = estado_new');

        Schema::table('master_reports', function (Blueprint $table) {
            // 6. Eliminar la columna temporal
            $table->dropColumn('estado_new');
        });
    }

    public function down(): void
    {
        Schema::table('master_reports', function (Blueprint $table) {
            $table->string('estado_new')->default('solicitado');
        });

        DB::statement('UPDATE master_reports SET estado_new = CASE WHEN estado = \'aprobado\' THEN \'finalizado\' ELSE estado END');

        Schema::table('master_reports', function (Blueprint $table) {
            $table->dropColumn('estado');
        });

        Schema::table('master_reports', function (Blueprint $table) {
            $table->enum('estado', ['solicitado', 'en_redaccion', 'finalizado', 'archivado'])
                  ->default('solicitado');
        });

        DB::statement('UPDATE master_reports SET estado = estado_new');

        Schema::table('master_reports', function (Blueprint $table) {
            $table->dropColumn('estado_new');
        });
    }
};
