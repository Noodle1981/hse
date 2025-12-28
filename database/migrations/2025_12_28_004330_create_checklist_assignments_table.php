<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklist_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_checklist_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('sede_id')->constrained()->onDelete('cascade');
            $table->foreignId('empresa_id')->constrained()->onDelete('cascade');
            
            $table->enum('frequency', ['once', 'weekly', 'monthly', 'quarterly', 'yearly'])->default('once');
            $table->date('due_date')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Índices para mejorar performance
            $table->index(['empresa_id', 'sede_id']);
            $table->index('status');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_assignments');
    }
};
