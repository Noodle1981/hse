<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Empresa>
 */
class EmpresaFactory extends Factory
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
            'razon_social' => $this->faker->company() . ' S.A.',
            'cuit' => $this->faker->unique()->numerify('###########'),
            'consultora_id' => \App\Models\Consultora::factory(),
        ];
    }
}
