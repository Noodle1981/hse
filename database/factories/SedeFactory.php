<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sede>
 */
class SedeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'empresa_id' => \App\Models\Empresa::factory(),
            'nombre_sede' => $this->faker->city(),
            'direccion' => $this->faker->address(),
            'correo' => $this->faker->unique()->safeEmail(),
            'telefono' => $this->faker->phoneNumber(),
            'tipo_sede' => 'Principal',
            'localidad' => $this->faker->city(),
        ];
    }
}
