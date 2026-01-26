<?php

namespace Database\Seeders\UseCases;

use Illuminate\Database\Seeder;
use Database\Seeders\Traits\SeederHelpers;
use App\Models\{User, Empresa, Sede, Category};

class ConstructionSeeder extends Seeder
{
    use SeederHelpers;

    private $tecnicos = [];
    private $sede;

    public function run(): void
    {
        echo "🏗️  Creando caso de uso: Construcción...\n";

        $this->createCompanyAndSede();
        $checklists = $this->createChecklists();
        $this->createInspections($checklists, $this->tecnicos, $this->sede, 10);
        $employees = $this->seedEmployees(); // Crear empleados PRIMERO
        $this->seedTrainings($employees, $checklists); // Pasar empleados y checklists a capacitaciones
        $this->seedEpp($employees); // Pasar empleados a EPP
        $this->seedRiskMatrix();
        $this->createAnnualContract($this->sede, $checklists, 'Construcción');

        // Tareas con vencimiento 31 Enero 2026
        $deadline = \Carbon\Carbon::create(2026, 1, 31, 12, 0, 0);
        if (!empty($this->tecnicos)) {
            $this->createDeadlineTasks($this->tecnicos[0], $this->sede, $checklists, $deadline);
            $this->createScheduledTrainings($this->tecnicos[0], $this->sede, $deadline, $checklists);
            $this->createPendingEppRequests($this->tecnicos[0], $this->sede, $deadline);
        }

        echo "✅ Construcción completado\n";
    }

    private function createCompanyAndSede(): void
    {
        $empresa = Empresa::create([
            'nombre' => 'Constructora del Sur SA',
            'razon_social' => 'Constructora del Sur Sociedad Anónima',
            'cuit' => '30-71234567-8',
        ]);

        $this->sede = Sede::create([
            'empresa_id' => $empresa->id,
            'nombre_sede' => 'Obra Torre Nordelta',
            'tipo_sede' => 'Obra en Construcción',
            'direccion' => 'Av. de los Lagos 7000',
            'localidad' => 'Tigre, Buenos Aires',
            'telefono' => '011-4871-2000',
            'correo' => 'obra.nordelta@constructora.com',
        ]);

        // Asignar técnicos
        $this->sede->users()->attach(array_map(fn($t) => $t->id, $this->tecnicos));
    }

    private function createChecklists(): array
    {
        $hys = Category::where('code', 'hys')->first();
        $calidad = Category::where('code', 'calidad')->first();

        return [
            // 1. Inspección de Andamios (CON secciones)
            $this->createChecklistWithSections(
                'Inspección de Andamios',
                'Verificación de seguridad en andamios tubulares',
                $hys,
                [
                    'Estructura Base' => [
                        '¿Las bases están niveladas y estables?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        'Estado de los tubos verticales' => ['type' => 'select', 'options' => ['Excelente', 'Bueno', 'Regular', 'Malo']],
                    ],
                    'Plataformas de Trabajo' => [
                        '¿Las plataformas están completas?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        'Ancho de plataforma (cm)' => ['type' => 'number'],
                    ],
                    'Protecciones Colectivas' => [
                        '¿Cuenta con barandas perimetrales?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        '¿Tiene rodapié instalado?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        'Observaciones' => ['type' => 'textarea'],
                    ],
                ]
            ),

            // 2. Control de Excavaciones (CON secciones)
            $this->createChecklistWithSections(
                'Control de Excavaciones',
                'Verificación de seguridad en excavaciones',
                $hys,
                [
                    'Apuntalamiento' => [
                        '¿Cuenta con apuntalamiento adecuado?' => ['type' => 'radio', 'options' => ['Sí', 'No', 'N/A']],
                        'Profundidad de excavación (m)' => ['type' => 'number'],
                    ],
                    'Accesos y Salidas' => [
                        '¿Tiene escalera de acceso?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        'Distancia a salida más cercana (m)' => ['type' => 'number'],
                    ],
                    'Señalización' => [
                        '¿Está señalizada el área?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                        '¿Cuenta con vallado perimetral?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                    ],
                ]
            ),

            // 3. Orden y Limpieza (SIN secciones)
            $this->createChecklistWithoutSections(
                'Orden y Limpieza en Obra',
                'Control de orden y limpieza general',
                $calidad,
                [
                    'Sector evaluado' => ['type' => 'text'],
                    '¿El área está libre de escombros?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                    '¿Herramientas ordenadas?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                    'Calificación general' => ['type' => 'select', 'options' => ['Excelente', 'Bueno', 'Regular', 'Deficiente']],
                    'Observaciones' => ['type' => 'textarea'],
                ]
            ),
        ];
    }

    private function seedTrainings(array $employees, array $checklists): void
    {
        $this->createTrainings($this->sede, $this->tecnicos, [
            ['Trabajo en Altura', 'Capacitación sobre prevención de caídas', -15],
            ['Uso de Arnés de Seguridad', 'Correcto uso y mantenimiento de arneses', -30],
            ['Señalización de Obra', 'Normativa de señalización', -45],
            ['Primeros Auxilios en Obra', 'Atención de emergencias', 15],
            ['Manejo de Herramientas Eléctricas', 'Seguridad en uso de herramientas', 30],
            ['Prevención de Riesgos', 'Identificación de peligros', 45],
        ], $employees, $checklists);
    }

    private function seedEmployees(): array
    {
        return $this->createEmployees($this->sede, [
            ['Roberto', 'Gómez', '25123456', '20-25123456-7', 'Oficial Albañil'],
            ['Diego', 'Fernández', '26234567', '20-26234567-8', 'Ayudante'],
            ['Martín', 'Silva', '27345678', '20-27345678-9', 'Oficial Electricista'],
            ['Lucas', 'Torres', '28456789', '20-28456789-0', 'Oficial Plomero'],
            ['Javier', 'Ramírez', '29567890', '20-29567890-1', 'Operario'],
            ['Fernando', 'Castro', '30678901', '20-30678901-2', 'Capataz'],
            ['Sergio', 'Morales', '31789012', '20-31789012-3', 'Ayudante'],
            ['Pablo', 'Ruiz', '32890123', '20-32890123-4', 'Oficial Carpintero'],
            ['Gustavo', 'Méndez', '33901234', '20-33901234-5', 'Operario'],
            ['Adrián', 'Vega', '34012345', '20-34012345-6', 'Ayudante'],
        ]);
    }

    private function seedEpp(array $employees): void
    {
        $eppItems = $this->createEppItems([
            ['Casco MSA V-Gard Blanco', 'Cabeza', 'IRAM 3610', 24],
            ['Arnés de Seguridad Parquer', 'Anticaídas', 'IRAM 3622', 60],
            ['Botas de Seguridad Pampero', 'Calzado', 'ISO 20345', 12],
            ['Guantes de Cuero Reforzado', 'Manos', 'EN 388', 3],
            ['Chaleco Reflectivo Naranja', 'Visibilidad', 'EN 471', 12],
        ]);

        $this->createEppDeliveries($this->sede, $employees, $this->tecnicos, $eppItems, 10);
    }

    private function seedRiskMatrix(): void
    {
        $this->createRiskMatrix(
            $this->sede,
            $this->tecnicos[0],
            'Matriz de Riesgos - Obra Torre Nordelta',
            'Evaluación de riesgos en construcción de edificio',
            'Construcción',
            'Obra Civil',
            [
                [
                    'hazard' => 'Caída de altura desde andamios',
                    'type' => 'Físico',
                    'consequence' => 'Fracturas múltiples, muerte',
                    'controls' => [
                        ['Uso obligatorio de arnés de seguridad', 'EPP'],
                        ['Barandas perimetrales en andamios', 'Ingeniería'],
                        ['Inspección diaria de andamios', 'Administrativo'],
                    ],
                    'probability' => 3,
                    'severity' => 5,
                ],
                [
                    'hazard' => 'Golpe por caída de objetos',
                    'type' => 'Mecánico',
                    'consequence' => 'Traumatismo craneal, fracturas',
                    'controls' => [
                        ['Uso de casco de seguridad', 'EPP'],
                        ['Señalización de áreas de riesgo', 'Administrativo'],
                        ['Redes de contención', 'Ingeniería'],
                    ],
                    'probability' => 4,
                    'severity' => 4,
                ],
                [
                    'hazard' => 'Electrocución por contacto eléctrico',
                    'type' => 'Eléctrico',
                    'consequence' => 'Paro cardíaco, quemaduras graves',
                    'controls' => [
                        ['Guantes dieléctricos', 'EPP'],
                        ['Disyuntor diferencial', 'Ingeniería'],
                        ['Bloqueo y etiquetado', 'Administrativo'],
                    ],
                    'probability' => 2,
                    'severity' => 5,
                ],
                [
                    'hazard' => 'Atrapamiento en excavaciones',
                    'type' => 'Mecánico',
                    'consequence' => 'Asfixia, muerte',
                    'controls' => [
                        ['Apuntalamiento de paredes', 'Ingeniería'],
                        ['Inspección de excavaciones', 'Administrativo'],
                    ],
                    'probability' => 2,
                    'severity' => 5,
                ],
                [
                    'hazard' => 'Sobreesfuerzo por manipulación manual',
                    'type' => 'Ergonómico',
                    'consequence' => 'Lesiones lumbares, hernias',
                    'controls' => [
                        ['Capacitación en levantamiento seguro', 'Administrativo'],
                        ['Uso de equipos de izaje', 'Ingeniería'],
                    ],
                    'probability' => 4,
                    'severity' => 3,
                ],
            ]
        );
    }

    public function setTechnicians(array $tecnicos): void
    {
        $this->tecnicos = $tecnicos;
    }
}
