<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Hacer user_id nullable para permitir inspecciones sin usuario autenticado
     */
    public function up(): void
    {
        // SQLite no soporta ALTER de foreign keys directamente.
        // En producción con MySQL/PostgreSQL sería:
        // Schema::table('dynamic_inspections', function (Blueprint $table) {
        //     $table->foreignId('user_id')->nullable()->change();
        // });
        
        // Para SQLite, recreamos la tabla (solo desarrollo)
        if (config('database.default') === 'sqlite') {
            Schema::dropIfExists('dynamic_answers');
            Schema::dropIfExists('dynamic_inspections');
            
            Schema::create('dynamic_inspections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('dynamic_checklist_id')
                      ->constrained('dynamic_checklists')
                      ->cascadeOnDelete();
                $table->foreignId('user_id')
                      ->nullable()
                      ->constrained()
                      ->nullOnDelete();
                
                $table->timestamp('completed_at')->nullable();
                $table->text('notes')->nullable();
                $table->string('status')->default('draft');
                $table->timestamps();

                $table->index(['dynamic_checklist_id', 'user_id']);
                $table->index('completed_at');
                $table->index('status');
            });
            
            Schema::create('dynamic_answers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('dynamic_inspection_id')
                      ->constrained('dynamic_inspections')
                      ->cascadeOnDelete();
                $table->foreignId('dynamic_question_id')
                      ->constrained('dynamic_questions')
                      ->cascadeOnDelete();
                $table->text('value')->nullable();
                $table->timestamps();
                
                $table->index(['dynamic_inspection_id', 'dynamic_question_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No hay rollback sencillo para SQLite
    }
};
