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
        Schema::table('master_reports', function (Blueprint $table) {
            $table->text('observaciones_visitas')->nullable()->after('periodo');
            $table->text('observaciones_checklists')->nullable()->after('observaciones_visitas');
            $table->text('observaciones_capacitaciones')->nullable()->after('observaciones_checklists');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_reports', function (Blueprint $table) {
            $table->dropColumn(['observaciones_visitas', 'observaciones_checklists', 'observaciones_capacitaciones']);
        });
    }
};
