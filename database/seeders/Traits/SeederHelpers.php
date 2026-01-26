<?php

namespace Database\Seeders\Traits;

use App\Models\{
    DynamicChecklist, ChecklistSection, DynamicQuestion,
    DynamicInspection, DynamicAnswer,
    Training, TrainingAttendance,
    Employee, EppItem, EppDelivery,
    RiskMatrix, Risk, RiskControl, RiskEvaluation,
    AnnualPlan, Category
};

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
                    'is_required' => in_array($config['type'], ['radio', 'select', 'text']),
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
                'is_required' => in_array($config['type'], ['radio', 'select', 'text']),
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
            
            $checklistId = !empty($checklists) ? $checklists[array_rand($checklists)]->id : null;

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
            if ($daysOffset < 0 && !empty($employees)) {
                $numAttendees = rand(min(5, count($employees)), count($employees));
                $selectedEmployees = array_rand($employees, $numAttendees);
                if (!is_array($selectedEmployees)) $selectedEmployees = [$selectedEmployees];
                
                foreach ($selectedEmployees as $empIndex) {
                    $employee = $employees[$empIndex];
                    TrainingAttendance::create([
                        'training_id' => $training->id,
                        'user_id' => null, // Empleado, no usuario del sistema
                        'employee_name' => $employee->name . ' ' . $employee->last_name,
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
            $employees[] = Employee::create([
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
     * Crear items de EPP
     */
    protected function createEppItems(array $itemsData): array
    {
        $items = [];
        
        foreach ($itemsData as $data) {
            $items[] = EppItem::firstOrCreate(
                ['nombre' => $data[0]],
                [
                    'tipo' => $data[1],
                    'codigo_norma' => $data[2],
                    'vida_util_meses' => $data[3],
                    'is_active' => true,
                ]
            );
        }
        
        return $items;
    }

    /**
     * Crear entregas de EPP
     */
    protected function createEppDeliveries($sede, array $employees, array $tecnicos, array $eppItems, int $count = 10): void
    {
        for ($i = 0; $i < $count; $i++) {
            $employee = $employees[array_rand($employees)];
            $tecnico = $tecnicos[array_rand($tecnicos)];
            
            $delivery = EppDelivery::create([
                'employee_id' => $employee->id,
                'user_id' => $tecnico->id,
                'delivery_date' => now()->subDays(rand(1, 90)),
                'observaciones' => $i % 2 == 0 ? 'Entrega de renovación' : 'Entrega por ingreso',
                'signature' => $this->getDefaultSignature(),
            ]);

            // Agregar items a la entrega (2-4 items por entrega)
            $itemCount = rand(2, 4);
            $selectedItems = array_rand($eppItems, min($itemCount, count($eppItems)));
            if (!is_array($selectedItems)) $selectedItems = [$selectedItems];
            
            foreach ($selectedItems as $itemIndex) {
                $delivery->items()->attach($eppItems[$itemIndex]->id, [
                    'cantidad' => rand(1, 3),
                ]);
            }
        }
    }

    /**
     * Crear matriz de riesgos
     */
    protected function createRiskMatrix($sede, $tecnico, string $title, string $description, string $area, string $process, array $risksData): void
    {
        $matrix = RiskMatrix::create([
            'title' => $title,
            'description' => $description,
            'area' => $area,
            'process' => $process,
            'empresa_id' => $sede->empresa_id,
            'sede_id' => $sede->id,
            'user_id' => $tecnico->id,
            'status' => 'active',
        ]);

        $order = 1;
        foreach ($risksData as $riskData) {
            $risk = $matrix->risks()->create([
                'hazard' => $riskData['hazard'],
                'type' => $riskData['type'],
                'consequence' => $riskData['consequence'],
                'order' => $order++,
            ]);

            // Crear controles
            foreach ($riskData['controls'] as $controlData) {
                $risk->controls()->create([
                    'measure' => $controlData[0],
                    'type' => $controlData[1],
                ]);
            }

            // Crear evaluación
            if (isset($riskData['probability']) && isset($riskData['severity'])) {
                $probability = $riskData['probability'];
                $severity = $riskData['severity'];
                $score = $probability * $severity;
                
                // Determinar nivel según matriz 5x5
                if ($score >= 15) {
                    $level = 'Crítico';
                } elseif ($score >= 10) {
                    $level = 'Alto';
                } elseif ($score >= 5) {
                    $level = 'Medio';
                } else {
                    $level = 'Bajo';
                }

                // Determinar color según nivel
                $color = match($level) {
                    'Crítico' => '#dc2626', // red-600
                    'Alto' => '#ea580c', // orange-600
                    'Medio' => '#ca8a04', // yellow-600
                    'Bajo' => '#16a34a', // green-600
                    default => '#6b7280', // gray-500
                };

                RiskEvaluation::create([
                    'risk_id' => $risk->id,
                    'user_id' => $tecnico->id,
                    'probability' => $probability,
                    'severity' => $severity,
                    'score' => $score,
                    'level' => $level,
                    'color' => $color,
                    'justification' => 'Evaluación inicial',
                    'evaluation_date' => now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }

    /**
     * Crear contrato anual
     */
    protected function createAnnualContract($sede, array $checklists, string $industry): void
    {
        $year = now()->year;
        $monthColumns = [
            'january', 'february', 'march', 'april', 'may', 'june',
            'july', 'august', 'september', 'october', 'november', 'december'
        ];

        // Crear una actividad por cada checklist
        foreach ($checklists as $index => $checklist) {
            // Seleccionar meses aleatorios para esta actividad (3-6 meses)
            $numMonths = rand(3, 6);
            $selectedMonths = array_rand(array_flip($monthColumns), $numMonths);
            if (!is_array($selectedMonths)) $selectedMonths = [$selectedMonths];
            
            // Preparar datos base
            $data = [
                'sede_id' => $sede->id,
                'category' => 'Inspección',
                'activity_name' => $checklist->title,
                'activity_code' => 'INSP-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
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
        return match($question->type) {
            'text' => $this->generateTextValue($question->label),
            'textarea' => rand(0, 1) ? 'Sin observaciones' : 'Requiere seguimiento',
            'date' => now()->subDays(rand(1, 30))->format('Y-m-d'),
            'number' => rand(10, 100),
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
            return 'N-' . rand(100, 999);
        }
        if (str_contains($lower, 'producto')) {
            return ['Sanitizante A', 'Desinfectante B', 'Limpiador C'][rand(0, 2)];
        }
        
        return 'Valor-' . rand(1, 100);
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
        
        return $nombres[array_rand($nombres)] . ' ' . $apellidos[array_rand($apellidos)];
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
        if (empty($checklists)) return;

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
            if (empty($checklists)) break;
            
            $checklist = $checklists[$i % count($checklists)];
            
            \App\Models\ChecklistAssignment::create([
                'dynamic_checklist_id' => $checklist->id,
                'user_id' => $tecnico->id,
                'sede_id' => $sede->id,
                'empresa_id' => $sede->empresa_id,
                'due_date' => $date,
                'status' => 'pending',
                'notes' => 'Tarea prioritaria con vencimiento: ' . $date->format('d/m/Y'),
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
            'Actualización Normativa ISO 45001'
        ];

        foreach ($topics as $topic) {
            $checklistId = !empty($checklists) ? $checklists[array_rand($checklists)]->id : null;

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
     * Crear solicitudes de EPP pendientes
     */
    protected function createPendingEppRequests($tecnico, $sede, $date): void
    {
        // Solicitudes de EPP (Si existe el modelo EppDeliveryRequest o similar)
        if (!class_exists(\App\Models\EppDeliveryRequest::class)) return;

        // Necesitamos empleados para asignar la solicitud
        $employees = \App\Models\Employee::where('sede_id', $sede->id)->inRandomOrder()->take(3)->get();
        if ($employees->isEmpty()) return;
        
        // Crear 3 solicitudes (o tantas como empleados encontremos hasta 3)
        foreach ($employees as $employee) {
            // Items aleatorios para esta solicitud
            $items = EppItem::inRandomOrder()->take(rand(1, 3))->get();
            if ($items->isEmpty()) continue;

            $itemsJson = $items->map(function($item) {
                return [
                    'item_id' => $item->id,
                    'cantidad' => rand(1, 2),
                    'tipo' => $item->tipo
                ];
            })->toArray();

            \App\Models\EppDeliveryRequest::create([
                 'empresa_id' => $sede->empresa_id,
                 'sede_id' => $sede->id,
                 'employee_id' => $employee->id,
                 'requested_by_user_id' => $tecnico->id,
                 'status' => 'pending',
                 'items' => $itemsJson,
                 'notes' => 'Solicitud urgente. Vencimiento requerido: ' . $date->format('d/m/Y'),
             ]);
        }
    }
}
