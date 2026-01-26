<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Traits\SeederHelpers;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\Category;
use App\Models\Consultora;

class FastDemoSeeder extends Seeder
{
    use SeederHelpers;

    private $tecnicos = [];
    private $sede;

    public function run(): void
    {
        $this->command->info('🚀 Iniciando FastDemoSeeder (Demo Express)...');

        // 1. Obtener Tenancy (Consultora) y Usuario Técnico
        $admin = User::where('rol', 'Administrador')->first(); 
        if (!$admin) {
            $this->command->error('No se encontró usuario Administrador. Asegúrate de ejecutar MasterDemoSeeder o crear uno manualmente.');
            return;
        }

        $technician = User::where('rol', 'Técnico')->first();
        if (!$technician) {
            $this->command->warn('No se encontró usuario Técnico. Creando uno temporal...');
            $technician = User::create([
                'nombres' => 'Juan',
                'apellidos' => 'Técnico',
                'email' => 'tecnico_demo@ep.com',
                'password' => bcrypt('password'),
                'rol' => 'Técnico',
                'estado' => 'activo',
                'consultora_id' => $admin->consultora_id
            ]);
        }
        $this->tecnicos = [$technician];

        // 2. Crear Empresa y Sede
        $this->command->info('🏢 Creando Empresa: Constructora ABC...');
        $empresa = Empresa::create([
            'nombre' => 'Constructora ABC',
            'cuit' => '30-12345678-9',
            'razon_social' => 'Constructora ABC S.A.',
            'consultora_id' => $admin->consultora_id
        ]);

        $this->sede = Sede::create([
            'empresa_id' => $empresa->id,
            'nombre_sede' => 'Obra Central - Torre Norte',
            'direccion' => 'Av. Libertador 1234',
            'localidad' => 'Buenos Aires',
            'telefono' => '11-5555-6666',
            'correo' => 'contacto@obra.com',
            'tipo_sede' => 'Obra'
        ]);

        // Asignar técnico a la sede
        $technician->sedes()->syncWithoutDetaching([$this->sede->id]);

        // 3. Crear Checklists (3 Templates)
        $this->command->info('📋 Creando Checklists Templates...');
        $checklists = $this->createCustomChecklists();

        // 4. Crear Asignaciones (Assignments) para el técnico
        $this->command->info('📅 Asignando tareas al técnico...');
        $this->createDemoAssignments($checklists, $technician, $this->sede);

        // 5. Crear Matriz de Riesgo con Disparadores
        $this->command->info('⚠️ Generando Matriz de Riesgo crítica...');
        // $this->seedCriticalRiskMatrix($technician);

        // 6. Crear Plan Anual
        $this->command->info('📅 Generando Contrato Anual...');
        $this->createAnnualContract($this->sede, $checklists, 'Construcción');

        // 7. Generar algunas inspecciones pasadas para estadísticas (opcional)
        // $this->createInspections($checklists, $this->tecnicos, $this->sede, 5);

        $this->command->info('✅ ¡Demo Express Lista! Accede como técnico para ver las tareas.');
    }

    private function createCustomChecklists()
    {
        $templates = [];

        // 1. Inspección de EPP (Con secciones)
        $categoryHys = Category::where('code', 'hys')->first();

        // SeederHelpers expects array keys as section titles
        $sectionsEpp = [
            'Casco y Cabeza' => [
                '¿Uso correcto de casco?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                '¿Barbijo ajustado?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
            ],
            'Protección Altura' => [
                '¿Arnés en buen estado?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
                '¿Línea de vida conectada?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
            ]
        ];

        $templates[] = $this->createChecklistWithSections(
            'Inspección de EPP - Control Diario',
            'Verificación diaria de equipos de protección personal',
            $categoryHys,
            $sectionsEpp
        );

        // 2. Verificación de Extintores (Sin secciones)
        $questionsExt = [
            '¿Extintor en ubicación correcta?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
            '¿Carga vigente?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
            '¿Presión en manómetro OK?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
            'Foto de etiqueta' => ['type' => 'photo'],
        ];
        $templates[] = $this->createChecklistWithoutSections(
            'Control de Extintores',
            'Revisión mensual de matafuegos',
            $categoryHys,
            $questionsExt
        );

        // 3. Control de Derrames (Ambiental)
        $categoryAmb = Category::where('code', 'ambiente')->first();
        if (!$categoryAmb) $categoryAmb = $categoryHys; // Fallback

        $questionsEnv = [
            '¿Kit de derrames disponible?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
            '¿Banejas de contención limpias?' => ['type' => 'radio', 'options' => ['Sí', 'No']],
            'Volumen estimado de residuos (L)' => ['type' => 'number', 'min_value' => 0, 'max_value' => 1000],
        ];
        $templates[] = $this->createChecklistWithoutSections(
            'Control de Derrames',
            'Verificación de kit y contenciones',
            $categoryAmb,
            $questionsEnv
        );

        return $templates;
    }

    private function seedCriticalRiskMatrix($technician)
    {
        $risksData = [
            [
                'hazard' => 'Caída de personas a distinto nivel', // SeederHelpers uses 'hazard' not 'description'
                'type' => 'Seguridad',
                'consequence' => 'Muerte, fracturas múltiples.',
                'probability' => 5, // Muy Alta
                'severity' => 5, // Catastrófico (SeederHelpers uses 'severity' not 'impact')
                'controls' => [
                    ['Uso obligatorio de arnés', 'Admin'],
                    ['Líneas de vida', 'Ingenieria'], 
                    ['Barandas perimetrales', 'Ingenieria']
                ]
            ],
            [
                'hazard' => 'Atrapamiento por partes móviles',
                'type' => 'Seguridad',
                'consequence' => 'Amputaciones, lesiones graves.',
                'probability' => 4,
                'severity' => 4, 
                'controls' => [
                    ['Resguardos en máquinas', 'Ingenieria'], 
                    ['Paradas de emergencia', 'Ingenieria']
                ]
            ],
            [
                'hazard' => 'Exposición a ruido',
                'type' => 'Higiene',
                'consequence' => 'Hipoacusia a largo plazo.',
                'probability' => 3,
                'severity' => 3,
                'controls' => [
                    ['Protectores auditivos de copa', 'EPP']
                ]
            ]
        ];

        /* Signature of createRiskMatrix: 
           $sede, $tecnico, $title, $description, $area, $process, $risksData 
        */

        $this->createRiskMatrix(
            $this->sede,
            $technician,
            'Matriz de Riesgo - Obra Torre Norte',
            'Identificación de peligros críticos en fase de estructura',
            'Estructura',
            'Hormigonado',
            $risksData
        );
    }
}
