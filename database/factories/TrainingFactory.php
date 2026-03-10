<?php

namespace Database\Factories;

use App\Models\Training;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\User;
use App\Models\DynamicChecklist;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingFactory extends Factory
{
    protected $model = Training::class;

    public function definition(): array
    {
        return [
            'empresa_id' => Empresa::factory(),
            'sede_id' => Sede::factory(),
            'user_id' => User::factory(),
            'created_by_user_id' => User::factory(),
            'is_template' => true,
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'type' => 'Presencial',
            'date' => now()->addDays(7),
            'duration' => 60,
            'status' => 'scheduled',
            'dynamic_checklist_id' => DynamicChecklist::factory(),
        ];
    }
}
