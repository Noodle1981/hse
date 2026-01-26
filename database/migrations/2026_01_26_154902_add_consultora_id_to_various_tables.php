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
        // Añadir a Empresas
        Schema::table('empresas', function (Blueprint $table) {
            $table->foreignId('consultora_id')->nullable()->after('id')->constrained('consultoras')->onDelete('set null');
            $table->string('logo_path')->nullable()->after('cuit');
            $table->string('primary_color', 7)->default('#F69239')->after('logo_path');
            $table->json('branding_config')->nullable()->after('primary_color');
        });

        // Añadir a Users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('consultora_id')->nullable()->after('id')->constrained('consultoras')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['consultora_id']);
            $table->dropColumn('consultora_id');
        });

        Schema::table('empresas', function (Blueprint $table) {
            $table->dropForeign(['consultora_id']);
            $table->dropColumn(['consultora_id', 'logo_path', 'primary_color', 'branding_config']);
        });
    }
};
