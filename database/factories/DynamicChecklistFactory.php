<?php

namespace Database\Factories;

use App\Models\DynamicChecklist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DynamicChecklist>
 */
class DynamicChecklistFactory extends Factory
{
    protected $model = DynamicChecklist::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => fake()->randomElement([
                'Control de EPP',
                'Inspección de Extintores',
                'Checklist de Vehículos',
                'Control de Herramientas',
                'Inspección de Seguridad',
                'Control de Calidad',
                'Verificación de Equipos',
            ]),
            'description' => fake()->optional(0.7)->sentence(),
            'category' => fake()->randomElement(['hys', 'calidad', 'seguimiento', null]),
            'is_active' => fake()->boolean(90), // 90% activos
        ];
    }

    /**
     * Estado: activo
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Estado: inactivo
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Categoría específica
     */
    public function category(string $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => $category,
        ]);
    }
}
