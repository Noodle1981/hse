<?php

namespace Database\Factories;

use App\Models\DynamicChecklist;
use App\Models\DynamicInspection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DynamicInspection>
 */
class DynamicInspectionFactory extends Factory
{
    protected $model = DynamicInspection::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['draft', 'completed']);

        return [
            'dynamic_checklist_id' => DynamicChecklist::factory(),
            'user_id' => User::factory(),
            'completed_at' => $status === 'completed' ? fake()->dateTimeBetween('-30 days', 'now') : null,
            'notes' => fake()->optional(0.5)->paragraph(),
            'status' => $status,
        ];
    }

    /**
     * Inspección completada
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'completed_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * Inspección borrador
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'completed_at' => null,
        ]);
    }
}
