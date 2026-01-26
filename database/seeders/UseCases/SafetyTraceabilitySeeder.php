<?php

namespace Database\Seeders\UseCases;

use Illuminate\Database\Seeder;
use Database\Seeders\Traits\SeederHelpers;
use App\Models\{User, Empresa, Sede, Category, Risk};

class SafetyTraceabilitySeeder extends Seeder
{
    use SeederHelpers;

    private $tecnico;
    private $gestor;
    private $sede;

    public function run($gestor = null, $tecnico = null): void
    {
        $this->gestor = $gestor ?? User::where('rol', 'Administrador')->first();
        $this->tecnico = $tecnico ?? User::where('rol', 'Técnico')->first();

        echo "🛡️  Refinando Caso de Uso Maestro: Trazabilidad Total 2026...\n";

        $this->createCompanyAndSede();
        
        // 1. Crear Matriz IPERC
        $this->seedRiskMatrix();
        
        // 2. Crear 10 Tareas de Checklist (5 Completadas, 5 Pendientes)
        // Necesitamos definiciones de Checklists (Plantillas)
        $checklistDefinitions = $this->createTraceableChecklists();
        
        // 3. Empleados
        $employees = $this->seedEmployees();

        // 4. Checklists Reales (Asignaciones)
        $assignments = $this->createCustomChecklistAssignments($checklistDefinitions);
        
        // 5. Capacitaciones (Vinculadas a los completados)
        $this->seedCustomTrainings($employees);

        // 6. Entregas EPP (2 Entregadas, 3 Pendientes)
        $this->seedCustomEpp($employees);

        // 7. Generar constancias de visitas (5 Visitas)
        $this->seedCustomVisits($assignments['completed'], $assignments['failed']);

        // 8. Plan Anual 2026
        // Usamos solo las definiciones para el contrato
        $this->createAnnualContract($this->sede, $checklistDefinitions, 'Seguridad Proactiva 2026');

        echo "✅ Caso Maestro Refinado Completado.\n";
    }

    private function createCustomChecklistAssignments(array $definitions): array
    {
        $completed = [];
        $pending = [];
        $failed = null;

        // Generar 10 Asignaciones: 5 Completas (1 fallida), 5 Pendientes
        for ($i = 0; $i < 10; $i++) {
            $def = $definitions[$i % count($definitions)]; // Ciclar entre las definiciones
            $status = $i < 5 ? 'completed' : 'pending';
            
            // Crear asignación
            $assignment = \App\Models\ChecklistAssignment::create([
                'dynamic_checklist_id' => $def->id,
                'user_id' => $this->tecnico->id,
                'sede_id' => $this->sede->id,
                'empresa_id' => $this->sede->empresa_id,
                'status' => $status,
                'due_date' => $status === 'completed' ? now()->subDays(rand(1, 30)) : now()->addDays(rand(1, 15)),
                // 'completed_at' no existe en Assignment, SOLO en Inspection
            ]);
            $completedDate = $status === 'completed' ? now()->subDays(rand(1, 30)) : null;

            if ($status === 'completed') {
                // Completar la inspección
                $inspection = \App\Models\DynamicInspection::create([
                    'dynamic_checklist_id' => $def->id,
                    'user_id' => $this->tecnico->id,
                    'sede_id' => $this->sede->id,
                    'checklist_assignment_id' => $assignment->id,
                    'completed_at' => $completedDate,
                    // 'data' y 'score' no existen en la tabla base
                    'status' => 'completed', // STATUS_COMPLETED
                    'result' => 'Pass'
                ]);

                // Hacer que la 3ra completada falle (índice 2)
                if ($i === 2) {
                    $inspection->update(['result' => 'Fail']);
                    $failed = $inspection;
                }
                
                $completed[] = $inspection;
            } else {
                $pending[] = $assignment;
            }
        }

        return ['completed' => $completed, 'pending' => $pending, 'failed' => $failed];
    }

    private function seedCustomEpp(array $employees): void
    {
        // Crear Items EPP
        $casco = \App\Models\EppItem::create([
            'name' => 'Casco de Seguridad',
            'code' => 'EPP-001',
            'stock' => 50,
            'is_active' => true,
            'consultora_id' => null
        ]);

        // 2 Entregadas
        for ($i = 0; $i < 2; $i++) {
            \App\Models\EppDelivery::create([
                'epp_item_id' => $casco->id,
                'employee_id' => $employees[$i]->id,
                'user_id' => $this->tecnico->id, // Quien entrega
                'sede_id' => $this->sede->id,
                'quantity' => 1,
                'delivery_date' => now()->subDays(rand(1, 10)),
                'status' => 'delivered', // Entregado
                'signature_path' => 'dummy_signature.png'
            ]);
        }

        // 3 Pendientes (Solicitudes)
        for ($i = 2; $i < 5; $i++) {
             // Usar empleado si existe, sino ciclar
             $emp = $employees[$i % count($employees)];
             \App\Models\EppDelivery::create([
                'epp_item_id' => $casco->id,
                'employee_id' => $emp->id,
                'user_id' => $this->tecnico->id,
                'sede_id' => $this->sede->id,
                'quantity' => 1,
                'delivery_date' => null,
                'status' => 'pending', // Pendiente
            ]);
        }
    }

    private function seedCustomTrainings(array $employees): void
    {
        // Crear capacitaciones relacionadas a los checklists (Altura y Autoelevadores)
        $topics = [
            'Seguridad en Altura y Uso de Arnés', 
            'Operación Segura de Montacargas'
        ];

        foreach ($topics as $index => $topic) {
            $isCompleted = $index === 0; // La primera completada, la segunda pendiente/futura

            $training = \App\Models\Training::create([
                'title' => $topic,
                'description' => 'Capacitación técnica obligatoria',
                'user_id' => $this->tecnico->id, // Instructor
                'sede_id' => $this->sede->id,
                'date' => $isCompleted ? now()->subDays(15) : now()->addDays(10),
                'duration_minutes' => 60,
                'status' => $isCompleted ? 'completed' : 'scheduled'
            ]);

            if ($isCompleted) {
                // Asistencia
                foreach ($employees as $emp) {
                    \App\Models\TrainingAttendance::create([
                        'training_id' => $training->id,
                        'employee_id' => $emp->id,
                        'attended' => true,
                        'signature_path' => 'dummy.png'
                    ]);
                }
            }
        }
    }

    private function seedCustomVisits(array $completedInspections, $failedInspection): void
    {
        // 5 Constancias de Visita
        for ($i = 0; $i < 5; $i++) {
            $date = now()->subDays((5 - $i) * 3); // Espaciadas
            
            $constancia = \App\Models\GeneradorConstancia::create([
                'user_id' => $this->tecnico->id,
                'sede_id' => $this->sede->id,
                'fecha' => $date,
                'hora_ingreso' => '09:00',
                'hora_egreso' => '13:00',
                'observaciones' => 'Visita de rutina nro ' . ($i + 1),
                'firma_tecnico' => 'signed',
                // Las constancias suelen firmarse por el responsable
                'firma_responsable' => $i < 3 ? 'signed_resp' : null, // 3 firmadas, 2 pendientes
                'date_firma_responsable' => $i < 3 ? $date : null
            ]);

            // Asociar inspecciones a la constancia (Agrupar)
            // Distribuir las 5 inspecciones completadas entre las visitas
            if (isset($completedInspections[$i])) {
                 // Nota: En la estructura actual, no hay tabla pivote directa constancia_inspeccion en el modelo básico
                 // a menos que uses `attach` si existe relación. 
                 // Asumiré que quieres que existan en el tiempo de la visita.
                 // Si hay relación directa, la usamos:
                 // $constancia->inspections()->attach($completedInspections[$i]->id);
            }
        }
        
        // El checklist NO completado (fallido) ya está en $failedInspection (es una inspección rejected)
        if ($failedInspection) {
             echo "   ❌ Checklist Fallido ID: " . $failedInspection->id . "\n";
        }
    }

    // --- Helpers originales necesarios ---
    private function createCompanyAndSede(): void
    {
        $empresa = Empresa::firstOrCreate(
            ['cuit' => '30-77889900-1'],
            [
                'nombre' => 'TechIndustry Global',
                'razon_social' => 'TechIndustry Global S.R.L.',
            ]
        );

        $this->sede = Sede::firstOrCreate(
            ['empresa_id' => $empresa->id, 'nombre_sede' => 'Planta Logística Central'],
            [
                'tipo_sede' => 'Centro de Distribución',
                'direccion' => 'Av. Industrial 500',
                'localidad' => 'Pilar, Buenos Aires',
                'telefono' => '0230-4455667',
                'correo' => 'seguridad@techindustry.com',
            ]
        );

        $this->sede->users()->syncWithoutDetaching([$this->gestor->id, $this->tecnico->id]);
    }

    private function seedRiskMatrix(): int
    {
        // Crear matriz básica si no existe
        $matrix = \App\Models\RiskMatrix::create([
             'user_id' => $this->tecnico->id,
             'sede_id' => $this->sede->id,
             'empresa_id' => $this->sede->empresa_id,
             'title' => 'Matriz IPERC - Operaciones Logísticas',
             'description' => 'Control de riesgos en altura y movimiento de cargas',
             'area' => 'Logística',
             'process' => 'Almacenamiento y Despacho',
             'status' => 'active'
        ]);

        $riesgosData = [
                [
                    'hazard' => 'Caída de personas a distinto nivel',
                    'type' => 'Físico',
                    'probability' => 3, 'severity' => 5, // Crítico
                ],
                [
                    'hazard' => 'Atropello por autoelevadores',
                    'type' => 'Mecánico',
                    'probability' => 4, 'severity' => 5, // Muy Crítico
                ],
                [
                    'hazard' => 'Caída de objetos desde altura',
                    'type' => 'Mecánico',
                    'probability' => 3, 'severity' => 4,
                ]
            ];
            
        foreach ($riesgosData as $r) {
            $risk = \App\Models\Risk::create([
                'risk_matrix_id' => $matrix->id,
                'hazard' => $r['hazard'],
                'type' => $r['type'],
                // 'consequence' => ... (Podríamos agregarlo si está en el array, si no dejar null)
            ]);

            // Crear evaluación inicial
            \App\Models\RiskEvaluation::create([
                'risk_id' => $risk->id,
                'user_id' => $this->tecnico->id,
                'probability' => $r['probability'],
                'severity' => $r['severity'],
                'score' => $r['probability'] * $r['severity'],
                'level' => 'Alto', // Simplificado
                'color' => '#EF4444', // Rojo
                'evaluation_date' => now()
            ]);
        }

        return $matrix->id;
    }

    private function createTraceableChecklists(): array
    {
        $hys = Category::where('code', 'hys')->first();
        
        $c1 = \App\Models\DynamicChecklist::create([
            'title' => 'Pre-Operacional de Autoelevadores',
            'description' => 'Control diario',
            // 'user_id' => $this->gestor->id, // No existe en fillable
            'is_active' => true,
            'category_id' => $hys?->id
        ]);
        
        $c2 = \App\Models\DynamicChecklist::create([
            'title' => 'Inspección de Equipos de Altura',
            'description' => 'Verificación mensual',
            // 'user_id' => $this->gestor->id,
            'is_active' => true,
            'category_id' => $hys?->id
        ]);

        return [$c1, $c2];
    }

    private function seedEmployees(): array
    {
        $data = [
            ['Alberto', 'Fernandez', '20123456', '20-20123456-9', 'Operador Logístico'],
            ['Marta', 'Gomez', '25678901', '27-25678901-0', 'Supervisor de Almacén'],
            ['Jorge', 'Rios', '30234567', '20-30234567-1', 'Operador de Autoelevador'],
            ['Silvia', 'Luna', '35345678', '27-35345678-2', 'Operario'],
        ];
        
        $employees = [];
        foreach($data as $d) {
            $employees[] = \App\Models\Employee::create([
                'empresa_id' => $this->sede->empresa_id, // Requerido por Employee
                'sede_id' => $this->sede->id,
                'name' => $d[0],      // first_name -> name
                'last_name' => $d[1],
                'dni' => $d[2],
                'cuil' => $d[3],
                'puesto' => $d[4],    // position -> puesto
                'is_active' => true
            ]);
        }
        return $employees;
    }
}
