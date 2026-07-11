<?php

namespace Database\Factories;

use App\Models\Consultora;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Consultora>
 */
class ConsultoraFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->company(),
            'cuit' => $this->faker->unique()->numerify('###########'),
            'max_tecnicos' => 10,
            'description' => $this->faker->paragraph(),
            'email' => $this->faker->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
        ];
    }
}
