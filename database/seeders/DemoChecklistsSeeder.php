<?php

namespace Database\Seeders;

use App\Models\DynamicChecklist;
use App\Models\DynamicQuestion;
use App\Models\DynamicInspection;
use App\Models\DynamicAnswer;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoChecklistsSeeder extends Seeder
{
    /**
     * Seeder para crear datos de demostración del sistema de checklists
     */
    public function run(): void
    {
        // Crear usuarios de prueba
        $admin = User::firstOrCreate(['email' => 'admin@ep.com'], [
            'password' => bcrypt('password'),
            'nombres' => 'Admin',
            'apellidos' => 'EP',
            'genero' => 'M',
            'rol' => 'Administrador',
            'matricula' => 'ADM-001',
            'firma' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        // Crear 5 técnicos
        $tecnicos = [];
        $nombres = ['Juan', 'María', 'Carlos', 'Ana', 'Pedro'];
        $apellidos = ['Pérez', 'González', 'Rodríguez', 'Martínez', 'López'];
        
        foreach (range(1, 5) as $i) {
            $tecnicos[] = User::firstOrCreate(
                ['email' => "tecnico{$i}@ep.com"],
                [
                    'password' => bcrypt('password'),
                    'nombres' => $nombres[$i-1],
                    'apellidos' => $apellidos[$i-1],
                    'genero' => $i % 2 == 0 ? 'F' : 'M',
                    'rol' => 'Técnico',
                    'matricula' => "TEC-00{$i}",
                    'firma' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
                ]
            );
        }

        // Array de checklists con configuración
        $checklistsConfig = [
            [
                'title' => 'Inspección de Equipo de Protección Personal (EPP)',
                'description' => 'Verificación del estado y correcto uso de los EPP',
                'category' => 'hys',
                'questions' => [
                    ['label' => '¿El trabajador cuenta con casco reglamentario?', 'type' => 'radio', 'options' => ['Sí', 'No', 'N/A']],
                    ['label' => '¿Los lentes de seguridad están en buen estado?', 'type' => 'radio', 'options' => ['Sí', 'No', 'N/A']],
                    ['label' => '¿El calzado de seguridad cuenta con puntera de acero?', 'type' => 'radio', 'options' => ['Sí', 'No', 'N/A']],
                    ['label' => 'Observaciones adicionales', 'type' => 'textarea', 'options' => null],
                    ['label' => 'Firma del responsable', 'type' => 'signature', 'options' => null],
                ],
                'inspecciones' => 15
            ],
            [
                'title' => 'Inspección de Vehículos',
                'description' => 'Control de seguridad de vehículos antes de su uso',
                'category' => 'seguimiento',
                'questions' => [
                    ['label' => 'Patente del vehículo', 'type' => 'text'],
                    ['label' => 'Fecha de inspección', 'type' => 'date'],
                    ['label' => 'Estado de neumáticos', 'type' => 'select', 'options' => ['Excelente', 'Bueno', 'Regular', 'Malo']],
                    ['label' => 'Kilómetros actuales', 'type' => 'number'],
                ],
                'inspecciones' => 12
            ],
            [
                'title' => 'Control de Orden y Limpieza',
                'description' => 'Evaluación de orden y limpieza en áreas de trabajo',
                'category' => 'calidad',
                'questions' => [
                    ['label' => 'Área evaluada', 'type' => 'text'],
                    ['label' => '¿El área está libre de obstáculos?', 'type' => 'radio', 'options' => ['Sí', 'No']],
                    ['label' => 'Calificación general', 'type' => 'select', 'options' => ['Excelente', 'Bueno', 'Regular', 'Deficiente']],
                ],
                'inspecciones' => 20
            ],
            [
                'title' => 'Inspección de Extintores',
                'description' => 'Verificación del estado de los extintores',
                'category' => 'hys',
                'questions' => [
                    ['label' => 'N° de extintor', 'type' => 'text'],
                    ['label' => 'Ubicación', 'type' => 'text'],
                    ['label' => 'Tipo', 'type' => 'select', 'options' => ['ABC', 'CO2', 'H2O', 'Químico']],
                    ['label' => 'Presión correcta', 'type' => 'radio', 'options' => ['Sí', 'No']],
                    ['label' => 'Fecha de vencimiento', 'type' => 'date'],
                ],
                'inspecciones' => 10
            ],
            [
                'title' => 'Inspección Eléctrica',
                'description' => 'Control de instalaciones eléctricas',
                'category' => 'seguimiento',
                'questions' => [
                    ['label' => 'Sector inspeccionado', 'type' => 'text'],
                    ['label' => 'Tableros en buen estado', 'type' => 'radio', 'options' => ['Sí', 'No']],
                    ['label' => 'Cables sin daños', 'type' => 'radio', 'options' => ['Sí', 'No']],
                    ['label' => 'Observaciones', 'type' => 'textarea'],
                ],
                'inspecciones' => 8
            ],
            [
                'title' => 'Inspección de Herramientas',
                'description' => 'Estado de herramientas manuales',
                'category' => 'calidad',
                'questions' => [
                    ['label' => 'Tipo de herramienta', 'type' => 'text'],
                    ['label' => 'Estado general', 'type' => 'select', 'options' => ['Excelente', 'Bueno', 'Regular', 'Malo']],
                    ['label' => 'Requiere mantenimiento', 'type' => 'radio', 'options' => ['Sí', 'No']],
                ],
                'inspecciones' => 12
            ],
            [
                'title' => 'Control Ambiental',
                'description' => 'Verificación de condiciones ambientales',
                'category' => 'ambiente',
                'questions' => [
                    ['label' => 'Área evaluada', 'type' => 'text'],
                    ['label' => 'Residuos clasificados correctamente', 'type' => 'radio', 'options' => ['Sí', 'No']],
                    ['label' => 'Nivel de ruido aceptable', 'type' => 'radio', 'options' => ['Sí', 'No']],
                    ['label' => 'Iluminación adecuada', 'type' => 'radio', 'options' => ['Sí', 'No']],
                ],
                'inspecciones' => 15
            ],
            [
                'title' => 'Inspección de Maquinaria',
                'description' => 'Control de estado de maquinaria',
                'category' => 'seguimiento',
                'questions' => [
                    ['label' => 'Identificación de máquina', 'type' => 'text'],
                    ['label' => 'Protecciones instaladas', 'type' => 'radio', 'options' => ['Sí', 'No']],
                    ['label' => 'Señalización visible', 'type' => 'radio', 'options' => ['Sí', 'No']],
                    ['label' => 'Última mantención', 'type' => 'date'],
                ],
                'inspecciones' => 10
            ],
        ];

        // Crear checklists y sus inspecciones
        foreach ($checklistsConfig as $config) {
            $checklist = DynamicChecklist::firstOrCreate(
                ['title' => $config['title']],
                [
                    'description' => $config['description'],
                    'category' => $config['category'],
                    'is_active' => true,
                ]
            );

            // Crear preguntas si no existen
            if ($checklist->questions()->count() === 0) {
                foreach ($config['questions'] as $index => $q) {
                    DynamicQuestion::create([
                        'dynamic_checklist_id' => $checklist->id,
                        'label' => $q['label'],
                        'type' => $q['type'],
                        'options' => $q['options'] ?? null,
                        'order' => $index,
                        'is_required' => in_array($q['type'], ['radio', 'signature', 'text']),
                    ]);
                }
            }

            // Crear inspecciones
            for ($i = 1; $i <= $config['inspecciones']; $i++) {
                $tecnico = $tecnicos[array_rand($tecnicos)];
                
                $inspection = DynamicInspection::create([
                    'dynamic_checklist_id' => $checklist->id,
                    'user_id' => $tecnico->id,
                    'completed_at' => now()->subDays(rand(1, 90)),
                    'status' => 'completed',
                    'notes' => $i % 3 == 0 ? 'Todo conforme' : null,
                ]);

                // Generar respuestas
                foreach ($checklist->questions as $question) {
                    $value = match($question->type) {
                        'text' => $this->generateTextValue($question->label),
                        'textarea' => rand(0, 1) ? 'Sin observaciones' : 'Requiere seguimiento',
                        'date' => now()->subDays(rand(1, 30))->format('Y-m-d'),
                        'number' => rand(1000, 99999),
                        'radio' => $question->options[array_rand($question->options)],
                        'select' => $question->options[array_rand($question->options)],
                        'checkbox' => json_encode(array_slice($question->options, 0, rand(1, count($question->options)))),
                        'signature' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
                        default => null,
                    };

                    DynamicAnswer::create([
                        'dynamic_inspection_id' => $inspection->id,
                        'dynamic_question_id' => $question->id,
                        'value' => $value,
                    ]);
                }
            }
        }

        $this->command->info('✅ Datos de demostración creados:');
        $this->command->info('   - 8 checklists');
        $this->command->info('   - 102 inspecciones completadas');
        $this->command->info('   - 1 administrador + 5 técnicos');
    }

    private function generateTextValue(string $label): string
    {
        if (str_contains(strtolower($label), 'patente')) {
            return 'ABC' . rand(100, 999);
        }
        if (str_contains(strtolower($label), 'área') || str_contains(strtolower($label), 'sector')) {
            return ['Almacén', 'Oficina', 'Taller', 'Depósito', 'Producción'][rand(0, 4)];
        }
        if (str_contains(strtolower($label), 'extintor')) {
            return 'EXT-' . rand(100, 999);
        }
        return 'Valor-' . rand(1, 100);
    }
}
