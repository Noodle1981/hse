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
            $table->string('email_responsable')->nullable()->after('date_firma_responsable');
            $table->string('aclaracion_responsable')->nullable()->after('email_responsable');
            $table->string('token_responsable')->nullable()->after('aclaracion_responsable');
            $table->string('code', 50)->nullable()->after('token_responsable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('generador_constancias', function (Blueprint $table) {
            $table->dropColumn(['email_responsable', 'aclaracion_responsable', 'token_responsable', 'code']);
        });
    }
};
