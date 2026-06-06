<?php

namespace Database\Factories;

use App\Models\Hallazgo;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class HallazgoFactory extends Factory
{
    protected $model = Hallazgo::class;

    public function definition(): array
    {
        return [
            'sede_id' => Sede::factory(),
            'user_id' => User::factory(),
            'titulo' => $this->faker->sentence,
            'descripcion' => $this->faker->paragraph,
            'ubicacion_especifica' => $this->faker->address,
            'criticidad' => $this->faker->randomElement(['bajo', 'medio', 'alto', 'critico']),
            'estado' => 'pendiente',
            'fecha_deteccion' => now(),
            'plazo_correccion' => now()->addDays(30),
        ];
    }
}
