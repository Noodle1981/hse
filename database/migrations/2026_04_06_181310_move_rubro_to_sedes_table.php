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
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('rubro');
        });

        Schema::table('sedes', function (Blueprint $table) {
            $table->string('rubro')->nullable()->after('tipo_sede');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sedes', function (Blueprint $table) {
            $table->dropColumn('rubro');
        });

        Schema::table('empresas', function (Blueprint $table) {
            $table->string('rubro')->nullable()->after('cuit');
        });
    }
};
