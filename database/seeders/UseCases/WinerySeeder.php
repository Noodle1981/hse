<?php

namespace Database\Seeders\UseCases;

use Illuminate\Database\Seeder;
use Database\Seeders\Traits\SeederHelpers;
use App\Models\{User, Empresa, Sede, Category};

class WinerySeeder extends Seeder
{
    use SeederHelpers;

    private $tecnicos = [];
    private $sede;

    public function run(): void
    {
        echo "🍷 Creando caso de uso: Bodega...\n";

        $this->createCompanyAndSede();
        $checklists = $this->createChecklists();
        $this->createInspections($checklists, $this->tecnicos, $this->sede, 10);
        $employees = $this->seedEmployees();
        $this->seedTrainings($employees);
        $this->seedEpp($employees);
        $this->seedRiskMatrix();
        $this->createAnnualContract($this->sede, $checklists, 'Bodega');

        // Tareas con vencimiento 31 Enero 2026
        $deadline = \Carbon\Carbon::create(2026, 1, 31, 12, 0, 0);
        if (!empty($this->tecnicos)) {
            $this->createDeadlineTasks($this->tecnicos[0], $this->sede, $checklists, $deadline);
            $this->createScheduledTrainings($this->tecnicos[0], $this->sede, $deadline);
            $this->createPendingEppRequests($this->tecnicos[0], $this->sede, $deadline);
        }

        echo "✅ Bodega completado\n";
    }

    private function createCompanyAndSede(): void
    {
        $empresa = Empresa::create([
            'nombre' => 'Bodegas del Valle SRL',
            'razon_social' => 'Bodegas del Valle Sociedad de Responsabilidad Limitada',
            'cuit' => '30-81234567-9',
        ]);

        $this->sede = Sede::create([
            'empresa_id' => $empresa->id,
            'nombre_sede' => 'Bodega Mendoza',
            'tipo_sede' => 'Planta de Producción',
            'direccion' => 'Ruta 7 Km 1025',
            'localidad' => 'Luján de Cuyo, Mendoza',
            'telefono' => '0261-498-3000',
            'correo' => 'produccion@bodegasdelvalle.com',
        ]);

        $this->sede->users()->attach(array_map(fn($t) => $t->id, $this->tecnicos));
    }

    private function createChecklists(): array
    {
        $hys = Category::where('code', 'hys')->first();
        $calidad = Category::where('code', 'calidad')->first();

        return [
            $this->createChecklistWithSections(
                'Control de Tanques de Fermentación',
                'Inspección de tanques de acero inoxidable',
                $hys,
                [
                    'Estado Estructural' => [
                        '¿El tanque presenta corrosión?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        'Estado de soldaduras' => ['type' => 'select', 'options' => ['Excelente', 'Bueno', 'Regular', 'Malo']],
                    ],
                    'Válvulas y Conexiones' => [
                        '¿Las válvulas cierran correctamente?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        '¿Hay fugas visibles?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                    ],
                    'Instrumentación' => [
                        'Temperatura registrada (°C)' => ['type' => 'number'],
                        '¿El termómetro funciona?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        'Observaciones' => ['type' => 'textarea'],
                    ],
                ]
            ),

            $this->createChecklistWithSections(
                'Inspección de Barricas de Roble',
                'Control de estado de barricas',
                $calidad,
                [
                    'Estado de Madera' => [
                        '¿La madera presenta grietas?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        'Humedad de la madera' => ['type' => 'select', 'options' => ['Adecuada', 'Seca', 'Húmeda']],
                    ],
                    'Cercos y Tapones' => [
                        '¿Los cercos están ajustados?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        '¿El tapón sella correctamente?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                    ],
                    'Identificación' => [
                        'Número de barrica' => ['type' => 'text'],
                        'Año de fabricación' => ['type' => 'number'],
                    ],
                ]
            ),

            $this->createChecklistWithoutSections(
                'Higiene y Sanitización',
                'Control de limpieza en áreas de producción',
                $calidad,
                [
                    'Área evaluada' => ['type' => 'text'],
                    '¿Pisos limpios y secos?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                    '¿Equipos sanitizados?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                    'Producto sanitizante usado' => ['type' => 'text'],
                    'Calificación' => ['type' => 'select', 'options' => ['Excelente', 'Bueno', 'Regular', 'Deficiente']],
                ]
            ),
        ];
    }

    private function seedTrainings(array $employees): void
    {
        $this->createTrainings($this->sede, $this->tecnicos, [
            ['Manejo de Productos Químicos', 'Uso seguro de sanitizantes', -20],
            ['Espacios Confinados', 'Ingreso seguro a tanques', -40],
            ['Higiene en Bodega', 'Buenas prácticas de manufactura', -60],
            ['Cata y Calidad', 'Evaluación sensorial', 10],
            ['Mantenimiento de Barricas', 'Cuidado de barricas de roble', 25],
            ['Prevención de Incendios', 'Uso de extintores', 40],
        ], $employees);
    }

    private function seedEmployees(): array
    {
        return $this->createEmployees($this->sede, [
            ['Miguel', 'Sosa', '35123456', '20-35123456-8', 'Enólogo'],
            ['Raúl', 'Pereyra', '36234567', '20-36234567-9', 'Operario de Bodega'],
            ['Jorge', 'Acosta', '37345678', '20-37345678-0', 'Técnico en Fermentación'],
            ['Ricardo', 'Molina', '38456789', '20-38456789-1', 'Operario'],
            ['Alberto', 'Ríos', '39567890', '20-39567890-2', 'Supervisor de Calidad'],
            ['Héctor', 'Paz', '40678901', '20-40678901-3', 'Operario'],
            ['Oscar', 'Luna', '41789012', '20-41789012-4', 'Técnico de Laboratorio'],
            ['Daniel', 'Campos', '42890123', '20-42890123-5', 'Operario'],
            ['Marcelo', 'Rojas', '43901234', '20-43901234-6', 'Encargado de Barricas'],
            ['Claudio', 'Vera', '44012345', '20-44012345-7', 'Operario'],
        ]);
    }

    private function seedEpp(array $employees): void
    {
        $eppItems = $this->createEppItems([
            ['Guantes Nitrilo Resistente Químicos', 'Manos', 'EN 374', 3],
            ['Máscara Respiratoria 3M 6200', 'Respiratoria', 'NIOSH N95', 6],
            ['Delantal PVC Impermeable', 'Cuerpo', 'EN 343', 12],
            ['Botas de Goma Blancas', 'Calzado', 'ISO 20345', 12],
            ['Protector Facial Transparente', 'Facial', 'EN 166', 12],
        ]);

        $this->createEppDeliveries($this->sede, $employees, $this->tecnicos, $eppItems, 10);
    }

    private function seedRiskMatrix(): void
    {
        $this->createRiskMatrix(
            $this->sede,
            $this->tecnicos[0],
            'Matriz de Riesgos - Bodega Mendoza',
            'Evaluación de riesgos en producción vitivinícola',
            'Producción',
            'Elaboración de Vinos',
            [
                [
                    'hazard' => 'Intoxicación por gases (CO2, SO2)',
                    'type' => 'Químico',
                    'consequence' => 'Asfixia, muerte',
                    'controls' => [
                        ['Detector de gases portátil', 'Ingeniería'],
                        ['Ventilación forzada en tanques', 'Ingeniería'],
                        ['Procedimiento de ingreso a espacios confinados', 'Administrativo'],
                    ],
                    'probability' => 3,
                    'severity' => 5,
                ],
                [
                    'hazard' => 'Quemaduras por productos químicos',
                    'type' => 'Químico',
                    'consequence' => 'Quemaduras en piel y ojos',
                    'controls' => [
                        ['Guantes y protector facial', 'EPP'],
                        ['Ducha de emergencia', 'Ingeniería'],
                        ['Capacitación en manejo de químicos', 'Administrativo'],
                    ],
                    'probability' => 3,
                    'severity' => 4,
                ],
                [
                    'hazard' => 'Caída en escaleras de tanques',
                    'type' => 'Físico',
                    'consequence' => 'Fracturas, traumatismos',
                    'controls' => [
                        ['Barandas en escaleras', 'Ingeniería'],
                        ['Calzado antideslizante', 'EPP'],
                    ],
                    'probability' => 3,
                    'severity' => 4,
                ],
                [
                    'hazard' => 'Incendio por alcohol',
                    'type' => 'Físico',
                    'consequence' => 'Quemaduras, pérdida de instalaciones',
                    'controls' => [
                        ['Extintores CO2', 'Ingeniería'],
                        ['Prohibición de fumar', 'Administrativo'],
                        ['Detección automática de incendios', 'Ingeniería'],
                    ],
                    'probability' => 2,
                    'severity' => 5,
                ],
            ]
        );
    }

    public function setTechnicians(array $tecnicos): void
    {
        $this->tecnicos = $tecnicos;
    }
}
