<?php

namespace Database\Factories;

use App\Models\Consultora;
use App\Models\Empresa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Empresa>
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
            'razon_social' => $this->faker->company().' S.A.',
            'cuit' => $this->faker->unique()->numerify('###########'),
            'consultora_id' => Consultora::factory(),
        ];
    }
}
