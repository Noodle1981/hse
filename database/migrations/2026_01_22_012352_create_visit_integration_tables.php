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
        // 1. Crear tabla de Visitas (Contenedor Principal)
        if (! Schema::hasTable('visit_certificates')) {
            Schema::create('visit_certificates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null')->comment('Técnico responsable');
                $table->foreignId('sede_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('empresa_id')->nullable()->constrained()->onDelete('cascade');
                $table->date('visit_date');
                $table->timestamp('start_time')->nullable();
                $table->timestamp('end_time')->nullable();
                $table->enum('status', ['draft', 'in_progress', 'completed', 'signed'])->default('draft');
                $table->text('general_observations')->nullable();
                $table->text('signature')->nullable();
                $table->timestamps();
            });
        }

        // 2. Tabla para vincular Checklists completados a la Visita
        if (! Schema::hasTable('visit_checklist_completions')) {
            Schema::create('visit_checklist_completions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('visit_certificate_id')->constrained()->onDelete('cascade');
                $table->foreignId('checklist_assignment_id')->nullable()->constrained()->onDelete('set null');
                $table->boolean('is_self_assigned')->default(false)->comment('Si el técnico se auto-asignó el checklist');
                $table->timestamp('completed_at')->useCurrent();
                $table->json('responses')->nullable()->comment('Copia de las respuestas al momento de completar');
                $table->timestamps();
            });
        }

        // 3. Tabla para Capacitaciones impartidas durante la Visita
        if (! Schema::hasTable('visit_trainings')) {
            Schema::create('visit_trainings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('visit_certificate_id')->constrained()->onDelete('cascade');
                $table->foreignId('training_id')->nullable()->constrained()->onDelete('set null')->comment('NULL si es ad-hoc');
                $table->boolean('is_adhoc')->default(false);
                $table->string('title')->nullable()->comment('Requerido si es ad-hoc');
                $table->text('description')->nullable();
                $table->integer('duration_minutes')->nullable();
                $table->json('attendees')->nullable()->comment('Array de asistentes con firma');
                $table->text('materials_delivered')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 4. Tabla para Entregas de EPP durante la Visita
        if (! Schema::hasTable('visit_epp_deliveries')) {
            Schema::create('visit_epp_deliveries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('visit_certificate_id')->constrained()->onDelete('cascade');
                $table->foreignId('employee_id')->nullable()->constrained()->onDelete('set null');
                $table->json('epp_items')->comment('Array de items entregados {item, qty, size}');
                $table->text('employee_signature')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 5. Agregar campos a tabla Hallazgos
        Schema::table('hallazgos', function (Blueprint $table) {
            if (! Schema::hasColumn('hallazgos', 'visit_certificate_id')) {
                $table->foreignId('visit_certificate_id')->nullable()->constrained()->onDelete('set null');
            }
            if (! Schema::hasColumn('hallazgos', 'checklist_completion_id')) {
                $table->foreignId('checklist_completion_id')->nullable()->constrained('visit_checklist_completions')->onDelete('set null');
            }
            if (! Schema::hasColumn('hallazgos', 'question_id')) {
                $table->foreignId('question_id')->nullable()->constrained('dynamic_questions')->onDelete('set null');
            }
        });

        // 6. Agregar campos a tabla Incidentes
        Schema::table('incidentes', function (Blueprint $table) {
            if (! Schema::hasColumn('incidentes', 'visit_certificate_id')) {
                $table->foreignId('visit_certificate_id')->nullable()->constrained()->onDelete('set null');
            }
        });

        // 7. Modificar tabla Trainings para soporte Ad-hoc
        Schema::table('trainings', function (Blueprint $table) {
            if (! Schema::hasColumn('trainings', 'created_by_user_id')) {
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            }
            if (! Schema::hasColumn('trainings', 'is_template')) {
                $table->boolean('is_template')->default(true);
            }
            // status ya existe en la tabla original
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->dropForeign(['created_by_user_id']);
            $table->dropColumn(['created_by_user_id', 'is_template']);
        });

        Schema::table('incidentes', function (Blueprint $table) {
            $table->dropForeign(['visit_certificate_id']);
            $table->dropColumn('visit_certificate_id');
        });

        Schema::table('hallazgos', function (Blueprint $table) {
            $table->dropForeign(['visit_certificate_id']);
            $table->dropForeign(['checklist_completion_id']);
            $table->dropForeign(['question_id']);
            $table->dropColumn(['visit_certificate_id', 'checklist_completion_id', 'question_id']);
        });

        Schema::dropIfExists('visit_epp_deliveries');
        Schema::dropIfExists('visit_trainings');
        Schema::dropIfExists('visit_checklist_completions');
        Schema::dropIfExists('visit_certificates');
    }
};
