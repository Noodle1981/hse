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
        Schema::create('capacitacion_virtuals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained()->nullable();
            $table->foreignId('user_id')->constrained()->nullable();
            $table->string('tema');
            $table->string('foto')->nullable();
            $table->string('link', length: 2000)->nullable();
            $table->string('lugar')->nullable();
            $table->string('instructor')->nullable();
            $table->unsignedSmallInteger('duracion')->nullable();
            $table->string('email_responsable', length: 50)->nullable();
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capacitacion_viruals');
    }
};
