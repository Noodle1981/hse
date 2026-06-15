<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultoras', function (Blueprint $table) {
            $table->string('estado_suscripcion')->default('activa')->after('locality')->comment('activa, morosa, baja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultoras', function (Blueprint $table) {
            $table->dropColumn('estado_suscripcion');
        });
    }
};
