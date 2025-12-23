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
        Schema::create('checklist_ext_items', function (Blueprint $table) {
            $table->id();
            $table->string('ubicacion')->nullable();
            $table->string('clase', length: 9)->nullable();
            $table->float('capacidad')->nullable();
            $table->date('prueba')->nullable();
            $table->date('vencimiento')->nullable();
            $table->string('manometro', length: 9)->nullable();
            $table->string('recipiente', length: 9)->nullable();
            $table->string('manija', length: 9)->nullable();
            $table->string('manguera', length: 9)->nullable();
            $table->string('tobera', length: 9)->nullable();
            $table->string('precinto', length: 9)->nullable();
            $table->string('pintura', length: 9)->nullable();
            $table->string('senaletica', length: 9)->nullable();
            $table->foreignId('checklist_ext_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_ext_items');
    }
};
