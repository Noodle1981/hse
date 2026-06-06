<?php

namespace Database\Factories;

use App\Models\DynamicChecklist;
use App\Models\DynamicQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DynamicQuestion>
 */
class DynamicQuestionFactory extends Factory
{
    protected $model = DynamicQuestion::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $type = fake()->randomElement(DynamicQuestion::TYPES);

        return [
            'dynamic_checklist_id' => DynamicChecklist::factory(),
            'label' => $this->getQuestionLabel($type),
            'type' => $type,
            'options' => $this->getOptionsForType($type),
            'order' => fake()->numberBetween(0, 10),
            'is_required' => fake()->boolean(60),
            'help_text' => fake()->optional(0.3)->sentence(),
        ];
    }

    /**
     * Genera una pregunta según el tipo
     */
    private function getQuestionLabel(string $type): string
    {
        $labels = [
            'text' => [
                '¿Nombre del responsable?',
                '¿Número de serie del equipo?',
                '¿Ubicación del hallazgo?',
            ],
            'textarea' => [
                'Describa las observaciones encontradas',
                'Detalle las acciones correctivas',
                'Notas adicionales',
            ],
            'select' => [
                'Seleccione el área de trabajo',
                'Tipo de incidente',
                'Prioridad de la acción',
            ],
            'radio' => [
                '¿El equipo está en buen estado?',
                '¿Se cumple con la normativa?',
                '¿Requiere mantenimiento?',
            ],
            'checkbox' => [
                'Seleccione los EPP utilizados',
                'Riesgos identificados',
                'Documentación revisada',
            ],
            'number' => [
                '¿Cantidad de extintores revisados?',
                '¿Temperatura registrada?',
                '¿Número de trabajadores presentes?',
            ],
            'date' => [
                'Fecha de vencimiento',
                'Última revisión',
                'Próxima inspección',
            ],
            'signature' => [
                'Firma del inspector',
                'Firma del responsable',
                'Firma del supervisor',
            ],
        ];

        return fake()->randomElement($labels[$type] ?? ['Pregunta de prueba']);
    }

    /**
     * Genera opciones para tipos que las requieren
     */
    private function getOptionsForType(string $type): ?array
    {
        if (! in_array($type, ['select', 'radio', 'checkbox'])) {
            return null;
        }

        $optionSets = [
            'select' => [
                ['Área A', 'Área B', 'Área C', 'Área D'],
                ['Baja', 'Media', 'Alta', 'Crítica'],
                ['Turno Mañana', 'Turno Tarde', 'Turno Noche'],
            ],
            'radio' => [
                ['Sí', 'No'],
                ['Sí', 'No', 'N/A'],
                ['Cumple', 'No Cumple', 'Parcial'],
            ],
            'checkbox' => [
                ['Casco', 'Guantes', 'Lentes', 'Zapatos de seguridad'],
                ['Riesgo eléctrico', 'Riesgo químico', 'Riesgo mecánico'],
                ['Manual', 'Procedimiento', 'Registro', 'Certificado'],
            ],
        ];

        return fake()->randomElement($optionSets[$type]);
    }

    /**
     * Tipo específico de pregunta
     */
    public function type(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
            'label' => $this->getQuestionLabel($type),
            'options' => $this->getOptionsForType($type),
        ]);
    }

    /**
     * Pregunta requerida
     */
    public function required(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_required' => true,
        ]);
    }
}
