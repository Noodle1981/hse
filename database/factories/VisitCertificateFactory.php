<?php

namespace Database\Factories;

use App\Models\Empresa;
use App\Models\Sede;
use App\Models\User;
use App\Models\VisitCertificate;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitCertificateFactory extends Factory
{
    protected $model = VisitCertificate::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'sede_id' => Sede::factory(),
            'empresa_id' => Empresa::factory(),
            'visit_date' => now()->format('Y-m-d'),
            'start_time' => now(),
            'status' => 'draft',
            'general_observations' => $this->faker->paragraph,
        ];
    }

    public function inProgress()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
        ]);
    }

    public function completed()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'end_time' => now(),
            'responsible_name' => $this->faker->name,
            'signature' => 'data:image/png;base64,sample',
            'date_firma_responsable' => now(),
        ]);
    }
}
