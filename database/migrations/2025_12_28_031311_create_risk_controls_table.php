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
        Schema::create('risk_controls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('risk_id')->constrained()->onDelete('cascade');
            $table->string('measure'); // "Uso de guantes", "Instalar barandas"
            $table->enum('type', ['Eliminación', 'Sustitución', 'Ingeniería', 'Administrativo', 'EPP'])->default('Administrativo');
            $table->enum('status', ['pendiente', 'en_proceso', 'implementado'])->default('pendiente');
            $table->date('due_date')->nullable();
            $table->foreignId('checklist_id')->nullable()->constrained('dynamic_checklists')->onDelete('set null'); // Opcional: checklist para verificar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_controls');
    }
};
