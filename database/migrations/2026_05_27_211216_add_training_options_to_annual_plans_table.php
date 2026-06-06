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
        Schema::table('annual_plans', function (Blueprint $table) {
            $table->integer('duration_minutes')->nullable()->default(15);
            $table->string('modality')->nullable()->default('presencial'); // presencial, virtual, hibrido
            $table->boolean('multi_trainer')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('annual_plans', function (Blueprint $table) {
            $table->dropColumn(['duration_minutes', 'modality', 'multi_trainer']);
        });
    }
};
