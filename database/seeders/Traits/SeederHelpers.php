<?php

namespace Database\Seeders\Traits;

use App\Models\AnnualPlan;
use App\Models\Category;
use App\Models\ChecklistSection;
use App\Models\DynamicAnswer;
use App\Models\DynamicChecklist;
use App\Models\DynamicInspection;
use App\Models\DynamicQuestion;
use App\Models\Employee as EmployeeModel;
use App\Models\Hallazgo;
use App\Models\Incidente;
use App\Models\Training;
use App\Models\TrainingAttendance;
use App\Models\VisitCertificate;
use App\Models\VisitChecklistCompletion;
use App\Models\VisitTraining;

trait SeederHelpers
{
    /**
     * Crear checklist CON secciones
     */
    protected function createChecklistWithSections(string $title, string $description, ?Category $category, array $sectionsData): DynamicChecklist
    {
        $checklist = DynamicChecklist::create([
            'title' => $title,
            'description' => $description,
            'category' => $category?->code,
            'category_id' => $category?->id,
            'is_active' => true,
            'is_draft' => false,
        ]);

        $sectionOrder = 0;
        foreach ($sectionsData as $sectionTitle => $questions) {
            $section = ChecklistSection::create([
                'dynamic_checklist_id' => $checklist->id,
                'title' => $sectionTitle,
                'order' => $sectionOrder++,
            ]);

            $questionOrder = 0;
            foreach ($questions as $label => $config) {
                DynamicQuestion::create([
                    'dynamic_checklist_id' => $checklist->id,
                    'section_id' => $section->id,
                    'label' => $label,
                    'type' => $config['type'],
                    'options' => $config['options'] ?? null,
                    'min_value' => $config['min_value'] ?? null,
                    'max_value' => $config['max_value'] ?? null,
                    'failure_values' => $config['failure_values'] ?? null,
                    'is_required' => in_array($config['type'], ['radio', 'select', 'text', 'number']),
                    'order' => $questionOrder++,
                ]);
            }
        }

        return $checklist;
    }

    /**
     * Crear checklist SIN secciones
     */
    protected function createChecklistWithoutSections(string $title, string $description, ?Category $category, array $questionsData): DynamicChecklist
    {
        $checklist = DynamicChecklist::create([
            'title' => $title,
            'description' => $description,
            'category' => $category?->code,
            'category_id' => $category?->id,
            'is_active' => true,
            'is_draft' => false,
        ]);

        $questionOrder = 0;
        foreach ($questionsData as $label => $config) {
            DynamicQuestion::create([
                'dynamic_checklist_id' => $checklist->id,
                'section_id' => null,
                'label' => $label,
                'type' => $config['type'],
                'options' => $config['options'] ?? null,
                'min_value' => $config['min_value'] ?? null,
                'max_value' => $config['max_value'] ?? null,
                'failure_values' => $config['failure_values'] ?? null,
                'is_required' => in_array($config['type'], ['radio', 'select', 'text', 'number']),
                'order' => $questionOrder++,
            ]);
        }

        return $checklist;
    }

    /**
     * Crear inspecciones para checklists
     */
    protected function createInspections(array $checklists, array $tecnicos, $sede, int $perChecklist = 10): void
    {
        foreach ($checklists as $checklist) {
            for ($i = 0; $i < $perChecklist; $i++) {
                $tecnico = $tecnicos[array_rand($tecnicos)];

                $inspection = DynamicInspection::create([
                    'dynamic_checklist_id' => $checklist->id,
                    'user_id' => $tecnico->id,
                    'sede_id' => $sede->id,
                    'completed_at' => now()->subDays(rand(1, 90)),
                    'status' => 'completed',
                    'notes' => $i % 3 == 0 ? 'Todo conforme' : null,
                ]);

                // Generar respuestas
                foreach ($checklist->questions as $question) {
                    $value = $this->generateAnswerValue($question);

                    DynamicAnswer::create([
                        'dynamic_inspection_id' => $inspection->id,
                        'dynamic_question_id' => $question->id,
                        'value' => $value,
                    ]);
                }

                // Evaluar y guardar resultado final (preservando fecha histórica)
                $inspection->update([
                    'result' => $inspection->evaluateResult(),
                ]);
            }
        }
    }

    /**
     * Crear capacitaciones
     */
    protected function createTrainings($sede, array $tecnicos, array $trainingsData, array $employees = [], array $checklists = []): void
    {
        foreach ($trainingsData as $data) {
            $daysOffset = $data[2]; // Negativo = pasado, Positivo = futuro
            $date = now()->addDays($daysOffset);

            $checklistId = ! empty($checklists) ? $checklists[array_rand($checklists)]->id : null;

            $training = Training::create([
                'empresa_id' => $sede->empresa_id,
                'sede_id' => $sede->id,
                'title' => $data[0],
                'description' => $data[1],
                'type' => 'presencial',
                'date' => $date,
                'duration' => rand(2, 8) * 60, // Convert hours to minutes
                'trainer_name' => 'Instructor Externo',
                'status' => $daysOffset < 0 ? 'completed' : 'scheduled',
                'dynamic_checklist_id' => $checklistId,
            ]);

            // Si es pasada, agregar asistentes (usar empleados si existen)
            if ($daysOffset < 0 && ! empty($employees)) {
                $numAttendees = rand(min(5, count($employees)), count($employees));
                $selectedEmployees = array_rand($employees, $numAttendees);
                if (! is_array($selectedEmployees)) {
                    $selectedEmployees = [$selectedEmployees];
                }

                foreach ($selectedEmployees as $empIndex) {
                    $employee = $employees[$empIndex];
                    TrainingAttendance::create([
                        'training_id' => $training->id,
                        'user_id' => null, // Empleado, no usuario del sistema
                        'employee_name' => $employee->name.' '.$employee->last_name,
                        'dni' => $employee->dni,
                        'signature' => $this->getDefaultSignature(),
                    ]);
                }
            }
        }
    }

    /**
     * Crear empleados
     */
    protected function createEmployees($sede, array $employeesData): array
    {
        $employees = [];

        foreach ($employeesData as $data) {
            $employees[] = EmployeeModel::create([
                'name' => $data[0],
                'last_name' => $data[1],
                'dni' => $data[2],
                'cuil' => $data[3],
                'puesto' => $data[4],
                'empresa_id' => $sede->empresa_id,
                'sede_id' => $sede->id,
                'is_active' => true,
            ]);
        }

        return $employees;
    }

    /**
     * Crear contrato anual
     */
    protected function createAnnualContract($sede, array $checklists, string $industry): void
    {
        $year = now()->year;
        $monthColumns = [
            'january', 'february', 'march', 'april', 'may', 'june',
            'july', 'august', 'september', 'october', 'november', 'december',
        ];

        // Crear una actividad por cada checklist
        foreach ($checklists as $index => $checklist) {
            // Seleccionar meses aleatorios para esta actividad (3-6 meses)
            $numMonths = rand(3, 6);
            $selectedMonths = array_rand(array_flip($monthColumns), $numMonths);
            if (! is_array($selectedMonths)) {
                $selectedMonths = [$selectedMonths];
            }

            // Preparar datos base
            $data = [
                'sede_id' => $sede->id,
                'category' => 'Inspección',
                'activity_name' => $checklist->title,
                'activity_code' => 'INSP-'.str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'checklist_id' => $checklist->id,
                'year' => $year,
            ];

            // Agregar meses seleccionados como true
            foreach ($selectedMonths as $month) {
                $data[$month] = true;
            }

            AnnualPlan::create($data);
        }
    }

    /**
     * Generar valor de respuesta según tipo de pregunta
     */
    private function generateAnswerValue($question)
    {
        // 10% de probabilidad de generar un fallo si hay lógica de falla definida
        $shouldFail = rand(1, 10) === 1;

        if ($shouldFail) {
            if (! empty($question->failure_values)) {
                return is_array($question->failure_values) ? $question->failure_values[array_rand($question->failure_values)] : $question->failure_values;
            }
            if ($question->type === 'number') {
                if ($question->min_value !== null) {
                    return (float) $question->min_value - 5.0;
                }
                if ($question->max_value !== null) {
                    return (float) $question->max_value + 5.0;
                }
            }
        }

        return match ($question->type) {
            'text' => $this->generateTextValue($question->label),
            'textarea' => rand(0, 1) ? 'Sin observaciones' : 'Requiere seguimiento',
            'date' => now()->subDays(rand(1, 30))->format('Y-m-d'),
            'number' => $question->min_value !== null && $question->max_value !== null
                        ? rand((int) $question->min_value, (int) $question->max_value)
                        : rand(10, 100),
            'radio' => $question->options ? $question->options[array_rand($question->options)] : 'Sí',
            'select' => $question->options ? $question->options[array_rand($question->options)] : 'Bueno',
            'checkbox' => $question->options ? json_encode(array_slice($question->options, 0, rand(1, count($question->options)))) : '[]',
            'signature' => $this->getDefaultSignature(),
            default => null,
        };
    }

    /**
     * Generar valor de texto contextual
     */
    private function generateTextValue(string $label): string
    {
        $lower = strtolower($label);

        if (str_contains($lower, 'sector') || str_contains($lower, 'área')) {
            return ['Producción', 'Almacén', 'Oficina', 'Taller', 'Depósito'][rand(0, 4)];
        }
        if (str_contains($lower, 'número') || str_contains($lower, 'n°')) {
            return 'N-'.rand(100, 999);
        }
        if (str_contains($lower, 'producto')) {
            return ['Sanitizante A', 'Desinfectante B', 'Limpiador C'][rand(0, 2)];
        }

        return 'Valor-'.rand(1, 100);
    }

    /**
     * Obtener firma por defecto (base64 de 1x1 pixel transparente)
     */
    protected function getDefaultSignature(): string
    {
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
    }

    /**
     * Generar nombre aleatorio
     */
    private function generateRandomName(): string
    {
        $nombres = ['Juan', 'María', 'Carlos', 'Ana', 'Pedro', 'Laura', 'José', 'Sofía', 'Luis', 'Carmen'];
        $apellidos = ['García', 'Rodríguez', 'Martínez', 'López', 'González', 'Pérez', 'Sánchez', 'Ramírez'];

        return $nombres[array_rand($nombres)].' '.$apellidos[array_rand($apellidos)];
    }

    /**
     * Generar puesto aleatorio
     */
    private function generateRandomPosition(): string
    {
        return ['Operario', 'Supervisor', 'Técnico', 'Encargado', 'Ayudante'][rand(0, 4)];
    }

    /**
     * Crear asignaciones de demostración (pendientes y vencidas)
     */
    protected function createDemoAssignments(array $checklists, $tecnico, $sede): void
    {
        if (empty($checklists)) {
            return;
        }

        // 2 Asignaciones VENCIDAS (overdue)
        \App\Models\ChecklistAssignment::create([
            'dynamic_checklist_id' => $checklists[0]->id,
            'user_id' => $tecnico->id,
            'sede_id' => $sede->id,
            'empresa_id' => $sede->empresa_id,
            'due_date' => now()->subDays(5),
            'status' => 'pending',
            'notes' => 'Inspección urgente - vencida hace 5 días',
        ]);

        if (isset($checklists[1])) {
            \App\Models\ChecklistAssignment::create([
                'dynamic_checklist_id' => $checklists[1]->id,
                'user_id' => $tecnico->id,
                'sede_id' => $sede->id,
                'empresa_id' => $sede->empresa_id,
                'due_date' => now()->subDays(2),
                'status' => 'pending',
                'notes' => 'Control pendiente - vencida',
            ]);
        }

        // 5 Asignaciones PENDIENTES (futuras)
        \App\Models\ChecklistAssignment::create([
            'dynamic_checklist_id' => $checklists[0]->id,
            'user_id' => $tecnico->id,
            'sede_id' => $sede->id,
            'empresa_id' => $sede->empresa_id,
            'due_date' => now()->addDays(2),
            'status' => 'pending',
            'notes' => 'Inspección programada próxima',
        ]);

        $thirdIndex = min(2, count($checklists) - 1);
        \App\Models\ChecklistAssignment::create([
            'dynamic_checklist_id' => $checklists[$thirdIndex]->id,
            'user_id' => $tecnico->id,
            'sede_id' => $sede->id,
            'empresa_id' => $sede->empresa_id,
            'due_date' => now()->addDays(3),
            'status' => 'pending',
            'notes' => 'Verificación de rutina',
        ]);

        if (isset($checklists[1])) {
            \App\Models\ChecklistAssignment::create([
                'dynamic_checklist_id' => $checklists[1]->id,
                'user_id' => $tecnico->id,
                'sede_id' => $sede->id,
                'empresa_id' => $sede->empresa_id,
                'due_date' => now()->addDays(5),
                'status' => 'pending',
                'notes' => 'Control semanal programado',
            ]);
        }

        \App\Models\ChecklistAssignment::create([
            'dynamic_checklist_id' => $checklists[0]->id,
            'user_id' => $tecnico->id,
            'sede_id' => $sede->id,
            'empresa_id' => $sede->empresa_id,
            'due_date' => now()->addDays(7),
            'status' => 'pending',
            'notes' => 'Inspección de seguimiento',
        ]);

        \App\Models\ChecklistAssignment::create([
            'dynamic_checklist_id' => $checklists[$thirdIndex]->id,
            'user_id' => $tecnico->id,
            'sede_id' => $sede->id,
            'empresa_id' => $sede->empresa_id,
            'due_date' => now()->addDays(10),
            'status' => 'pending',
            'notes' => 'Verificación mensual',
        ]);

        echo "   📋 7 asignaciones creadas (2 vencidas, 5 pendientes)\n";
    }

    /**
     * Crear tareas con fecha de vencimiento específica (Ej: 31 Enero)
     */
    protected function createDeadlineTasks($tecnico, $sede, array $checklists, $date): void
    {
        // 1. Asignaciones de Checklists (3 tareas)
        $tasksCount = 3;
        for ($i = 0; $i < $tasksCount; $i++) {
            if (empty($checklists)) {
                break;
            }

            $checklist = $checklists[$i % count($checklists)];

            \App\Models\ChecklistAssignment::create([
                'dynamic_checklist_id' => $checklist->id,
                'user_id' => $tecnico->id,
                'sede_id' => $sede->id,
                'empresa_id' => $sede->empresa_id,
                'due_date' => $date,
                'status' => 'pending',
                'notes' => 'Tarea prioritaria con vencimiento: '.$date->format('d/m/Y'),
            ]);
        }
    }

    /**
     * Crear capacitaciones programadas para una fecha
     */
    protected function createScheduledTrainings($tecnico, $sede, $date, array $checklists = []): void
    {
        $topics = [
            'Seguridad Operativa Avanzada',
            'Protocolos de Emergencia 2026',
            'Actualización Normativa ISO 45001',
        ];

        foreach ($topics as $topic) {
            $checklistId = ! empty($checklists) ? $checklists[array_rand($checklists)]->id : null;

            Training::create([
                'empresa_id' => $sede->empresa_id,
                'sede_id' => $sede->id,
                'title' => $topic,
                'description' => 'Capacitación obligatoria de vencimiento mensual',
                'type' => 'presencial',
                'date' => $date->copy()->setTime(rand(9, 14), 0),
                'duration' => 120,
                'trainer_name' => 'Instructor Certificado',
                'status' => 'scheduled',
                'dynamic_checklist_id' => $checklistId,
            ]);
        }
    }

    /**
     * Crear una visita completa con integraciones
     */
    protected function createCompleteVisit($sede, $tecnico, $date, array $checklists, array $employees): VisitCertificate
    {
        $visit = VisitCertificate::create([
            'user_id' => $tecnico->id,
            'sede_id' => $sede->id,
            'empresa_id' => $sede->empresa_id,
            'visit_date' => $date,
            'start_time' => $date->copy()->setTime(9, 0),
            'end_time' => $date->copy()->setTime(17, 0),
            'status' => 'signed',
            'code' => 'VIS-'.$date->format('Ymd').'-'.rand(100, 999),
            'responsible_name' => 'Juan Responsable',
            'general_observations' => 'Visita de control programada 2025.',
            'signature' => $this->getDefaultSignature(),
        ]);

        // 1. Vincular Checklists (Completados)
        foreach ($checklists as $checklist) {
            // Crear una asignación previa para que todo cierre
            $assignment = \App\Models\ChecklistAssignment::create([
                'dynamic_checklist_id' => $checklist->id,
                'user_id' => $tecnico->id,
                'sede_id' => $sede->id,
                'empresa_id' => $sede->empresa_id,
                'due_date' => $date,
                'status' => 'completed',
            ]);

            $inspection = DynamicInspection::create([
                'dynamic_checklist_id' => $checklist->id,
                'checklist_assignment_id' => $assignment->id,
                'user_id' => $tecnico->id,
                'sede_id' => $sede->id,
                'completed_at' => $date->copy()->setTime(11, 0),
                'status' => 'completed',
            ]);

            // Crear la constancia en la visita
            $completion = VisitChecklistCompletion::create([
                'visit_certificate_id' => $visit->id,
                'checklist_assignment_id' => $assignment->id,
                'is_self_assigned' => true,
                'completed_at' => $date->copy()->setTime(11, 0),
                'responses' => [], // Se llenará con DynamicAnswer
            ]);

            // Generar respuestas (algunas pueden fallar si el riesgo es coincidente)
            $responsesForJson = [];
            foreach ($checklist->questions as $question) {
                $value = $this->generateAnswerValue($question);

                DynamicAnswer::create([
                    'dynamic_inspection_id' => $inspection->id,
                    'dynamic_question_id' => $question->id,
                    'value' => $value,
                ]);

                $responsesForJson[$question->id] = $value;

                // Si falló, crear un Hallazgo vinculado
                $tempAnswer = new DynamicAnswer(['dynamic_question_id' => $question->id, 'value' => $value]);
                if ($tempAnswer->isFailure()) {
                    $this->createHallazgo($visit, $tecnico, $question);
                }
            }

            // Actualizar el JSON de respuestas en la constancia
            $completion->update(['responses' => $responsesForJson]);

            // Evaluar y guardar resultado final de la inspección (preservando fecha histórica)
            $inspection->update([
                'result' => $inspection->evaluateResult(),
            ]);
        }

        // 2. Vincular Capacitación
        VisitTraining::create([
            'visit_certificate_id' => $visit->id,
            'training_id' => Training::where('empresa_id', $sede->empresa_id)->inRandomOrder()->first()?->id,
            'is_adhoc' => false,
        ]);

        return $visit;
    }

    /**
     * Crear un Hallazgo vinculado a un fallo
     */
    protected function createHallazgo($visit, $tecnico, $question): Hallazgo
    {
        return Hallazgo::create([
            'sede_id' => $visit->sede_id,
            'user_id' => $tecnico->id,
            'titulo' => 'Desviación detectada: '.$question->label,
            'descripcion' => 'Se detectó un incumplimiento crítico durante la inspección del control operativo.',
            'criticidad' => 'alto',
            'estado' => 'pendiente',
            'fecha_deteccion' => $visit->visit_date,
            'visit_certificate_id' => $visit->id,
            'question_id' => $question->id,
        ]);
    }

    /**
     * Crear un Incidente vinculado a un hallazgo/riesgo
     */
    protected function createIncidente($hallazgo, $date): Incidente
    {
        return Incidente::create([
            'hallazgo_id' => $hallazgo->id,
            'sede_id' => $hallazgo->sede_id,
            'user_id' => $hallazgo->user_id,
            'tipo' => 'incidente',
            'gravedad' => 'moderado',
            'fecha_ocurrencia' => $date,
            'titulo' => 'Evento adverso relacionado con '.$hallazgo->titulo,
            'descripcion' => 'Incidente derivado de un fallo en el control preventivo detectado previamente.',
            'estado' => 'investigacion',
            'visit_certificate_id' => $hallazgo->visit_certificate_id,
        ]);
    }

    private function generateFailureValue($question)
    {
        if (! empty($question->failure_values)) {
            return is_array($question->failure_values) ? $question->failure_values[array_rand($question->failure_values)] : $question->failure_values;
        }
        if ($question->type === 'number') {
            return ($question->min_value !== null) ? (float) $question->min_value - 10 : (float) $question->max_value + 10;
        }

        return 'Fallo';
    }
}
