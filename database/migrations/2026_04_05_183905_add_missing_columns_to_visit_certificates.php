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
        Schema::table('visit_certificates', function (Blueprint $table) {
            $table->string('visit_type')->nullable();
            $table->text('instructor_signature')->nullable();
            $table->datetime('instructor_signature_date')->nullable();
            $table->string('aclaracion_responsable')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visit_certificates', function (Blueprint $table) {
            $table->dropColumn(['visit_type', 'instructor_signature', 'instructor_signature_date', 'aclaracion_responsable']);
        });
    }
};
