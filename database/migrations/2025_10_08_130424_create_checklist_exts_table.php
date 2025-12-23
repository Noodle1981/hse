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
        Schema::create('checklist_exts', function (Blueprint $table) {
            $table->id();
            $table->string('inspector')->nullable();
            $table->string('establecimiento')->nullable();
            $table->string('area')->nullable();
            $table->string('responsable')->nullable();
            $table->text('acciones')->nullable();
            $table->foreignId('checklist_asign_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_exts');
    }
};
