<?php

namespace Database\Seeders\UseCases;

use App\Models\Category;
use App\Models\Empresa;
use App\Models\Sede;
use Database\Seeders\Traits\SeederHelpers;
use Illuminate\Database\Seeder;

class MiningSeeder extends Seeder
{
    use SeederHelpers;

    private $tecnicos = [];

    private $sede;

    public function run(): void
    {
        echo "⛏️  Creando caso de uso: Minería...\n";

        $this->createCompanyAndSede();
        $checklists = $this->createChecklists();
        $this->createInspections($checklists, $this->tecnicos, $this->sede, 10);
        $employees = $this->seedEmployees();
        $this->seedTrainings($employees, $checklists);
        $this->seedEpp($employees);
        $this->seedRiskMatrix();
        $this->createAnnualContract($this->sede, $checklists, 'Minería');

        // Tareas con vencimiento 31 Enero 2026
        $deadline = \Carbon\Carbon::create(2026, 1, 31, 12, 0, 0);
        if (! empty($this->tecnicos)) {
            $this->createDeadlineTasks($this->tecnicos[0], $this->sede, $checklists, $deadline);
            $this->createScheduledTrainings($this->tecnicos[0], $this->sede, $deadline, $checklists);
            $this->createPendingEppRequests($this->tecnicos[0], $this->sede, $deadline);
        }

        echo "✅ Minería completado\n";
    }

    private function createCompanyAndSede(): void
    {
        $empresa = Empresa::create([
            'nombre' => 'Minera Andina SA',
            'razon_social' => 'Minera Andina Sociedad Anónima',
            'cuit' => '30-91234567-0',
        ]);

        $this->sede = Sede::create([
            'empresa_id' => $empresa->id,
            'nombre_sede' => 'Mina San Juan',
            'tipo_sede' => 'Explotación Minera',
            'direccion' => 'Ruta 149 Km 250',
            'localidad' => 'Calingasta, San Juan',
            'telefono' => '0264-442-5000',
            'correo' => 'operaciones@mineraandina.com',
        ]);

        $this->sede->users()->attach(array_map(fn ($t) => $t->id, $this->tecnicos));
    }

    private function createChecklists(): array
    {
        $hys = Category::where('code', 'hys')->first();
        $ambiente = Category::where('code', 'ambiente')->first();

        return [
            $this->createChecklistWithSections(
                'Inspección de Maquinaria Pesada',
                'Control pre-operacional de equipos mineros',
                $hys,
                [
                    'Sistema Hidráulico' => [
                        '¿Nivel de aceite hidráulico correcto?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        '¿Hay fugas en mangueras?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                    ],
                    'Sistema Eléctrico' => [
                        '¿Batería en buen estado?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        '¿Luces funcionando?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                    ],
                    'Cabina y Controles' => [
                        '¿Cinturón de seguridad funcional?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        '¿Bocina operativa?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        'Horómetro (horas)' => ['type' => 'number'],
                    ],
                ]
            ),

            $this->createChecklistWithSections(
                'Control de Voladuras',
                'Verificación de seguridad en voladuras',
                $hys,
                [
                    'Almacenamiento de Explosivos' => [
                        '¿Polvorín cerrado con llave?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        'Temperatura del polvorín (°C)' => ['type' => 'number'],
                    ],
                    'Perforación' => [
                        'Profundidad de barrenos (m)' => ['type' => 'number'],
                        '¿Diámetro correcto?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                    ],
                    'Carga y Detonación' => [
                        '¿Área evacuada?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        '¿Sirena de aviso funcionó?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        'Observaciones' => ['type' => 'textarea'],
                    ],
                ]
            ),

            $this->createChecklistWithoutSections(
                'Ventilación de Túneles',
                'Control de calidad de aire en galerías',
                $ambiente,
                [
                    'Sector evaluado' => ['type' => 'text'],
                    'Nivel de oxígeno (%)' => ['type' => 'number'],
                    'Nivel de CO (ppm)' => ['type' => 'number'],
                    '¿Ventiladores operativos?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                    'Temperatura ambiente (°C)' => ['type' => 'number'],
                ]
            ),
        ];
    }

    private function seedTrainings(array $employees, array $checklists): void
    {
        $this->createTrainings($this->sede, $this->tecnicos, [
            ['Operación de Maquinaria Pesada', 'Manejo seguro de palas y camiones', -25],
            ['Uso de Explosivos', 'Manejo seguro de voladuras', -50],
            ['Rescate en Minas', 'Procedimientos de emergencia', -75],
            ['Primeros Auxilios Mineros', 'Atención de emergencias', 5],
            ['Ventilación en Minería', 'Control de atmósferas peligrosas', 20],
            ['Prevención de Derrumbes', 'Fortificación y sostenimiento', 35],
        ], $employees, $checklists);
    }

    private function seedEmployees(): array
    {
        return $this->createEmployees($this->sede, [
            ['Ramón', 'Quiroga', '45123456', '20-45123456-9', 'Operador de Pala'],
            ['Esteban', 'Ibarra', '46234567', '20-46234567-0', 'Perforista'],
            ['Néstor', 'Cabrera', '47345678', '20-47345678-1', 'Volador'],
            ['Hugo', 'Navarro', '48456789', '20-48456789-2', 'Operador de Camión'],
            ['Rubén', 'Ortiz', '49567890', '20-49567890-3', 'Mecánico'],
            ['Mario', 'Ponce', '50678901', '20-50678901-4', 'Supervisor de Turno'],
            ['Luis', 'Vargas', '51789012', '20-51789012-5', 'Operario'],
            ['Tomás', 'Benítez', '52890123', '20-52890123-6', 'Topógrafo'],
            ['Ernesto', 'Cáceres', '53901234', '20-53901234-7', 'Operario'],
            ['Víctor', 'Domínguez', '54012345', '20-54012345-8', 'Ayudante'],
        ]);
    }

    private function seedEpp(array $employees): void
    {
        $eppItems = $this->createEppItems([
            ['Casco Minero con Lámpara LED', 'Cabeza', 'IRAM 3610', 24],
            ['Respirador Media Cara MSA', 'Respiratoria', 'NIOSH P100', 6],
            ['Botas Mineras Reforzadas', 'Calzado', 'ISO 20345', 12],
            ['Guantes de Cuero Manga Larga', 'Manos', 'EN 388', 3],
            ['Protector Auditivo Tipo Copa', 'Auditiva', 'ANSI S3.19', 36],
        ]);

        $this->createEppDeliveries($this->sede, $employees, $this->tecnicos, $eppItems, 10);
    }

    private function seedRiskMatrix(): void
    {
        $this->createRiskMatrix(
            $this->sede,
            $this->tecnicos[0],
            'Matriz de Riesgos - Mina San Juan',
            'Evaluación de riesgos en explotación minera',
            'Minería',
            'Extracción Subterránea',
            [
                [
                    'hazard' => 'Derrumbe de galerías',
                    'type' => 'Geológico',
                    'consequence' => 'Sepultamiento, muerte',
                    'controls' => [
                        ['Fortificación con pernos y mallas', 'Ingeniería'],
                        ['Inspección geomecánica', 'Administrativo'],
                        ['Capacitación en reconocimiento de terreno', 'Administrativo'],
                    ],
                    'probability' => 3,
                    'severity' => 5,
                ],
                [
                    'hazard' => 'Explosión por gases',
                    'type' => 'Químico',
                    'consequence' => 'Quemaduras, muerte',
                    'controls' => [
                        ['Detector de gases continuo', 'Ingeniería'],
                        ['Ventilación forzada', 'Ingeniería'],
                        ['Prohibición de fumar', 'Administrativo'],
                    ],
                    'probability' => 2,
                    'severity' => 5,
                ],
                [
                    'hazard' => 'Atropello por maquinaria',
                    'type' => 'Mecánico',
                    'consequence' => 'Politraumatismo, muerte',
                    'controls' => [
                        ['Chaleco reflectivo', 'EPP'],
                        ['Alarma de retroceso en equipos', 'Ingeniería'],
                        ['Segregación de tránsito', 'Administrativo'],
                    ],
                    'probability' => 4,
                    'severity' => 5,
                ],
                [
                    'hazard' => 'Intoxicación por polvo de sílice',
                    'type' => 'Químico',
                    'consequence' => 'Silicosis, enfermedades respiratorias',
                    'controls' => [
                        ['Respirador P100', 'EPP'],
                        ['Riego de frentes', 'Ingeniería'],
                        ['Monitoreo de polvo', 'Administrativo'],
                    ],
                    'probability' => 4,
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
