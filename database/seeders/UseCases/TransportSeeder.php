<?php

namespace Database\Seeders\UseCases;

use App\Models\Category;
use App\Models\Empresa;
use App\Models\Sede;
use Carbon\Carbon;
use Database\Seeders\Traits\SeederHelpers;
use Illuminate\Database\Seeder;

class TransportSeeder extends Seeder
{
    use SeederHelpers;

    private $tecnicos = [];

    private $sede;

    public function run(): void
    {
        echo "🚚 Creando caso de uso: Transporte...\n";

        $this->createCompanyAndSede();
        $checklists = $this->createChecklists();
        $this->createInspections($checklists, $this->tecnicos, $this->sede, 5); // Solo 5 inspecciones
        $employees = $this->seedEmployees();
        $this->seedTrainings($employees, $checklists);
        $this->seedEpp($employees);
        $this->seedRiskMatrix();
        $this->createAnnualContract($this->sede, $checklists, 'Transporte');

        // Crear asignaciones para Roberto (Transporte)
        // Tareas con vencimiento 31 Enero 2026
        $deadline = Carbon::create(2026, 1, 31, 12, 0, 0);
        if (! empty($this->tecnicos)) {
            $this->createDeadlineTasks($this->tecnicos[0], $this->sede, $checklists, $deadline);
            $this->createScheduledTrainings($this->tecnicos[0], $this->sede, $deadline, $checklists);
            $this->createPendingEppRequests($this->tecnicos[0], $this->sede, $deadline);
        }

        echo "✅ Transporte completado\n";
    }

    private function createCompanyAndSede(): void
    {
        $empresa = Empresa::create([
            'nombre' => 'Transportes del Norte SA',
            'razon_social' => 'Transportes del Norte Sociedad Anónima',
            'cuit' => '30-91234567-0',
        ]);

        $this->sede = Sede::create([
            'empresa_id' => $empresa->id,
            'nombre_sede' => 'Centro de Distribución Rosario',
            'tipo_sede' => 'Centro Logístico',
            'direccion' => 'Av. Circunvalación 2500',
            'localidad' => 'Rosario, Santa Fe',
            'telefono' => '0341-456-7890',
            'correo' => 'logistica@transportesdelnorte.com',
        ]);

        $this->sede->users()->attach(array_map(fn ($t) => $t->id, $this->tecnicos));
    }

    private function createChecklists(): array
    {
        $hys = Category::where('code', 'hys')->first();
        $ambiente = Category::where('code', 'ambiente')->first();

        return [
            $this->createChecklistWithSections(
                'Inspección de Vehículos de Carga',
                'Control pre-viaje de camiones',
                $hys,
                [
                    'Sistema de Frenos' => [
                        '¿Frenos delanteros en buen estado?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        '¿Frenos traseros en buen estado?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        'Nivel de líquido de frenos' => ['type' => 'select', 'options' => ['Óptimo', 'Bajo', 'Crítico']],
                    ],
                    'Neumáticos' => [
                        'Presión neumáticos delanteros (PSI)' => ['type' => 'number'],
                        'Presión neumáticos traseros (PSI)' => ['type' => 'number'],
                        '¿Profundidad de dibujo adecuada?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                    ],
                    'Iluminación y Señalización' => [
                        '¿Luces delanteras funcionan?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        '¿Luces traseras funcionan?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        '¿Balizas funcionan?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                    ],
                ]
            ),

            $this->createChecklistWithSections(
                'Control de Depósito',
                'Inspección de almacenamiento y estibaje',
                $hys,
                [
                    'Orden y Limpieza' => [
                        '¿Pasillos despejados?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        '¿Señalización visible?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                    ],
                    'Estanterías' => [
                        '¿Estanterías estables?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        '¿Carga correctamente distribuida?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                    ],
                ]
            ),

            $this->createChecklistWithoutSections(
                'Verificación de Documentación de Carga',
                'Control de manifiestos y documentación',
                $ambiente,
                [
                    'Número de manifiesto' => ['type' => 'text'],
                    '¿Documentación completa?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                    '¿Carga coincide con manifiesto?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                    'Observaciones' => ['type' => 'textarea'],
                ]
            ),
        ];
    }

    private function seedTrainings(array $employees, array $checklists): void
    {
        $this->createTrainings($this->sede, $this->tecnicos, [
            ['Manejo Defensivo', 'Técnicas de conducción segura', 5],
            ['Carga y Descarga Segura', 'Procedimientos de manipulación', 10],
            ['Primeros Auxilios en Ruta', 'Atención de emergencias', 15],
            ['Mantenimiento Preventivo', 'Cuidado básico de vehículos', 20],
            ['Normativa de Transporte', 'Legislación vigente', 25],
            ['Gestión de Residuos', 'Manejo de desechos en ruta', 30],
        ], $employees, $checklists);
    }

    private function seedEmployees(): array
    {
        return $this->createEmployees($this->sede, [
            ['Carlos', 'Ruiz', '55123456', '20-55123456-1', 'Chofer de Larga Distancia'],
            ['Andrés', 'Moreno', '56234567', '20-56234567-2', 'Chofer Urbano'],
            ['Gabriel', 'Ortiz', '57345678', '20-57345678-3', 'Operario de Depósito'],
            ['Matías', 'Navarro', '58456789', '20-58456789-4', 'Supervisor de Flota'],
            ['Nicolás', 'Herrera', '59567890', '20-59567890-5', 'Mecánico'],
            ['Facundo', 'Giménez', '60678901', '20-60678901-6', 'Operario de Depósito'],
            ['Ezequiel', 'Medina', '61789012', '20-61789012-7', 'Chofer'],
            ['Maximiliano', 'Romero', '62890123', '20-62890123-8', 'Ayudante'],
            ['Sebastián', 'Álvarez', '63901234', '20-63901234-9', 'Operario'],
            ['Cristian', 'Benítez', '64012345', '20-64012345-0', 'Chofer de Reparto'],
        ]);
    }

    private function seedEpp(array $employees): void
    {
        $eppItems = $this->createEppItems([
            ['Chaleco Reflectivo Alta Visibilidad', 'Visibilidad', 'EN 471', 12],
            ['Guantes Anticorte', 'Manos', 'EN 388', 3],
            ['Calzado de Seguridad con Puntera', 'Calzado', 'ISO 20345', 12],
            ['Casco de Seguridad', 'Cabeza', 'IRAM 3610', 24],
            ['Protector Auditivo Tipo Tapón', 'Auditiva', 'ANSI S3.19', 6],
        ]);

        $this->createEppDeliveries($this->sede, $employees, $this->tecnicos, $eppItems, 10);
    }

    private function seedRiskMatrix(): void
    {
        $this->createRiskMatrix(
            $this->sede,
            $this->tecnicos[0],
            'Matriz de Riesgos - Transporte de Cargas',
            'Evaluación de riesgos en operaciones logísticas',
            'Logística',
            'Transporte y Distribución',
            [
                [
                    'hazard' => 'Accidente de tránsito',
                    'type' => 'Físico',
                    'consequence' => 'Lesiones graves, muerte',
                    'controls' => [
                        ['Capacitación en manejo defensivo', 'Administrativo'],
                        ['Mantenimiento preventivo de vehículos', 'Ingeniería'],
                        ['Descansos obligatorios cada 4 horas', 'Administrativo'],
                    ],
                    'probability' => 3,
                    'severity' => 5,
                ],
                [
                    'hazard' => 'Caída de carga durante manipulación',
                    'type' => 'Físico',
                    'consequence' => 'Golpes, fracturas',
                    'controls' => [
                        ['Uso de calzado de seguridad', 'EPP'],
                        ['Procedimiento de estibaje seguro', 'Administrativo'],
                    ],
                    'probability' => 4,
                    'severity' => 3,
                ],
                [
                    'hazard' => 'Sobreesfuerzo en carga/descarga',
                    'type' => 'Ergonómico',
                    'consequence' => 'Lesiones musculares',
                    'controls' => [
                        ['Capacitación en levantamiento manual', 'Administrativo'],
                        ['Uso de ayudas mecánicas', 'Ingeniería'],
                    ],
                    'probability' => 4,
                    'severity' => 2,
                ],
                [
                    'hazard' => 'Atropello en depósito',
                    'type' => 'Físico',
                    'consequence' => 'Lesiones graves',
                    'controls' => [
                        ['Chaleco reflectivo obligatorio', 'EPP'],
                        ['Señalización de áreas peatonales', 'Ingeniería'],
                        ['Velocidad máxima 10 km/h', 'Administrativo'],
                    ],
                    'probability' => 2,
                    'severity' => 4,
                ],
                [
                    'hazard' => 'Fatiga del conductor',
                    'type' => 'Psicosocial',
                    'consequence' => 'Accidentes, errores operativos',
                    'controls' => [
                        ['Límite de horas de conducción', 'Administrativo'],
                        ['Monitoreo de descansos', 'Administrativo'],
                    ],
                    'probability' => 3,
                    'severity' => 4,
                ],
            ]
        );
    }

    public function setTechnicians(array $tecnicos): void
    {
        $this->tecnicos = $tecnicos;
    }
}
