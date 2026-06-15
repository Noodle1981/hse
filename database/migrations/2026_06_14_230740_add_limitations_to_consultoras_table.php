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
        Schema::table('consultoras', function (Blueprint $table) {
            $table->integer('max_gestores')->default(2)->comment('Límite de licencias de gestores');
            $table->integer('max_empresas')->default(5)->comment('Límite de empresas clientes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultoras', function (Blueprint $table) {
            $table->dropColumn(['max_gestores', 'max_empresas']);
        });
    }
};
