<?php

namespace Database\Seeders\UseCases;

use Illuminate\Database\Seeder;
use Database\Seeders\Traits\SeederHelpers;
use App\Models\{User, Empresa, Sede, Category, Risk};

class SafetyTraceabilitySeeder extends Seeder
{
    use SeederHelpers;

    private $tecnicos = [];
    private $sede;

    public function run(): void
    {
        echo "🛡️  Creando Caso de Uso Maestro: Trazabilidad Total 2025...\n";

        $this->createCompanyAndSede();
        
        // 1. Crear Matriz IPERC
        $this->seedRiskMatrix();
        
        // 2. Crear Checklists Trazables
        $checklists = $this->createTraceableChecklists();
        
        // 3. Empleados y Capacitaciones
        $employees = $this->seedEmployees();
        $this->seedTrainings($employees, $checklists);

        // 4. Generar 10 Visitas a lo largo de 2025 (Cronología)
        $this->seedTimeline2025($checklists, $employees);

        // 5. Plan Anual 2025
        $this->createAnnualContract($this->sede, $checklists, 'Seguridad Proactiva');

        echo "✅ Caso Maestro 2025 completado. Sistema de trazabilidad alimentado.\n";
    }

    private function seedTimeline2025(array $checklists, array $employees): void
    {
        $tecnico = is_array($this->tecnicos) ? $this->tecnicos[0] : $this->tecnicos->first();
        $riesgos = Risk::all();
        $riskAltura = $riesgos->where('hazard', 'like', '%Altura%')->first();
        $riskAuto = $riesgos->where('hazard', 'like', '%Atropello%')->first();

        // 10 Visitas Programadas (Incluimos hoy para disparar alertas en tiempo real)
        $dates = [
            '2025-01-15', '2025-02-10', '2025-03-05', '2025-04-20',
            '2025-05-12', '2025-06-18', '2025-10-05',
            now()->subDays(20)->format('Y-m-d'),
            now()->subDays(10)->format('Y-m-d'),
            now()->format('Y-m-d') 
        ];

        foreach ($dates as $index => $dateStr) {
            $date = \Carbon\Carbon::parse($dateStr);
            
            // Simular un fallo crítico en la visita 3 y en la ÚLTIMA (Hoy)
            $targetFail = null;
            if ($index == 2) $targetFail = $riskAltura; // Histórico
            if ($index == 9) $targetFail = $riskAuto;   // ALERTA ACTUAL: Hoy falló el autoelevador

            $visit = $this->createCompleteVisit($this->sede, $tecnico, $date, $checklists, $employees, $targetFail);

            // Si es la visita de hoy, forzamos un hallazgo
            if ($index == 9) {
                echo "   🚨 DISPARADOR ACTIVO: Fallo de control detectado HOY (" . $date->format('d/m/Y') . ")\n";
            }

            // Si hubo un hallazgo crítico en la visita 3, generar un incidente 2 días después
            if ($index == 2) {
                $hallazgo = $visit->hallazgos()->first();
                if ($hallazgo) {
                    $this->createIncidente($hallazgo, $date->copy()->addDays(2));
                    echo "   ⚠️ Incidente generado derivado de fallo en control (Marzo 2025)\n";
                }
            }
        }
    }

    private function createCompanyAndSede(): void
    {
        $this->tecnicos = User::where('rol', 'Técnico')->get();
        if ($this->tecnicos->isEmpty()) {
            $this->tecnicos = [User::first()]; // Fallback
        }

        $empresa = Empresa::create([
            'nombre' => 'TechIndustry Global',
            'razon_social' => 'TechIndustry Global S.R.L.',
            'cuit' => '30-77889900-1',
        ]);

        $this->sede = Sede::create([
            'empresa_id' => $empresa->id,
            'nombre_sede' => 'Planta Logística Central',
            'tipo_sede' => 'Centro de Distribución',
            'direccion' => 'Av. Industrial 500',
            'localidad' => 'Pilar, Buenos Aires',
            'telefono' => '0230-4455667',
            'correo' => 'seguridad@techindustry.com',
        ]);

        $this->sede->users()->attach($this->tecnicos->pluck('id'));
    }

    private function seedRiskMatrix(): int
    {
        $this->createRiskMatrix(
            $this->sede,
            $this->tecnicos[0],
            'Matriz IPERC - Operaciones Logísticas',
            'Control de riesgos en altura y movimiento de cargas',
            'Logística',
            'Almacenamiento y Despacho',
            [
                [
                    'hazard' => 'Caída de personas a distinto nivel',
                    'type' => 'Físico',
                    'consequence' => 'Fracturas, traumatismos craneales, muerte',
                    'controls' => [
                        ['Arnés de seguridad y línea de vida', 'EPP'],
                        ['Barandas y plataformas de trabajo', 'Ingeniería'],
                        ['Permiso de trabajo en altura', 'Administrativo'],
                    ],
                    'probability' => 3,
                    'severity' => 5,
                ],
                [
                    'hazard' => 'Atropello por autoelevadores',
                    'type' => 'Mecánico',
                    'consequence' => 'Contusiones, aplastamiento, muerte',
                    'controls' => [
                        ['Segregación de peatones y máquinas', 'Ingeniería'],
                        ['Alarma sonora y lumínica de reversa', 'Ingeniería'],
                        ['Capacitación en manejo defensivo', 'Administrativo'],
                    ],
                    'probability' => 4,
                    'severity' => 5,
                ],
                [
                    'hazard' => 'Caída de objetos desde altura',
                    'type' => 'Mecánico',
                    'consequence' => 'Golpes, lesiones físicas',
                    'controls' => [
                        ['Mallas de seguridad en racks', 'Ingeniería'],
                        ['Uso obligatorio de casco', 'EPP'],
                    ],
                    'probability' => 3,
                    'severity' => 4,
                ]
            ]
        );

        return \App\Models\RiskMatrix::latest()->first()->id;
    }

    private function createTraceableChecklists(): array
    {
        $hys = Category::where('code', 'hys')->first();
        
        // Obtener riesgos para vincular
        $riskAltura = Risk::where('hazard', 'like', '%Altura%')->first();
        $riskAuto = Risk::where('hazard', 'like', '%Atropello%')->first();

        return [
            $this->createChecklistWithSections(
                'Pre-Operacional de Autoelevadores',
                'Control diario de seguridad para montacargas',
                $hys,
                [
                    'Estado Mecánico' => [
                        '¿Frenos de servicio operativos?' => [
                            'type' => 'radio', 
                            'options' => ['Sí', 'No'],
                            'failure_values' => ['No'],
                            'risk_id' => $riskAuto?->id
                        ],
                        '¿Alarma de retroceso funciona?' => [
                            'type' => 'radio', 
                            'options' => ['Sí', 'No'],
                            'failure_values' => ['No'],
                            'risk_id' => $riskAuto?->id
                        ],
                        'Presión de neumáticos (PSI)' => [
                            'type' => 'number',
                            'min_value' => 80,
                            'max_value' => 110,
                            'risk_id' => $riskAuto?->id
                        ],
                    ],
                    'Cabina y Visibilidad' => [
                        '¿Espejos retrovisores limpios?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        '¿Luces de giro y stop?' => [
                            'type' => 'radio', 
                            'options' => ['Sí', 'No'],
                            'failure_values' => ['No'],
                            'risk_id' => $riskAuto?->id
                        ]
                    ]
                ]
            ),

            $this->createChecklistWithSections(
                'Inspección de Equipos de Altura',
                'Verificación de arneses y líneas de vida',
                $hys,
                [
                    'Estado del Arnés' => [
                        '¿Costuras en buen estado?' => [
                            'type' => 'radio', 
                            'options' => ['Sí', 'No'],
                            'failure_values' => ['No'],
                            'risk_id' => $riskAltura?->id
                        ],
                        '¿Presencia de quemaduras o químicos?' => [
                            'type' => 'radio', 
                            'options' => ['No', 'Sí'],
                            'failure_values' => ['Sí'],
                            'risk_id' => $riskAltura?->id
                        ],
                    ],
                    'Puntos de Anclaje' => [
                        '¿Punto de anclaje certificado?' => [
                            'type' => 'radio', 
                            'options' => ['Sí', 'No'],
                            'failure_values' => ['No'],
                            'risk_id' => $riskAltura?->id
                        ]
                    ]
                ]
            )
        ];
    }

    private function seedEmployees(): array
    {
        return $this->createEmployees($this->sede, [
            ['Alberto', 'Fernandez', '20123456', '20-20123456-9', 'Operador Logístico'],
            ['Marta', 'Gomez', '25678901', '27-25678901-0', 'Supervisor de Almacén'],
            ['Jorge', 'Rios', '30234567', '20-30234567-1', 'Operador de Autoelevador'],
            ['Silvia', 'Luna', '35345678', '27-35345678-2', 'Operario'],
        ]);
    }

    private function seedTrainings(array $employees, array $checklists): void
    {
        $this->createTrainings($this->sede, $this->tecnicos->toArray(), [
            ['Cero Accidentes: Trabajo en Altura', 'Normativa y uso correcto de EPP de altura', -10],
            ['Manejo Seguro de Autoelevadores', 'Seguridad en el movimiento de cargas', -5],
            ['Primeros Auxilios Avanzados', 'RCP y atención de traumas', 15],
        ], $employees, $checklists);
    }
}
