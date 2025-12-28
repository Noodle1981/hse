<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RiskMatrix;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\User;
use App\Models\Risk;
use App\Models\RiskControl;

class RiskDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('rol', 'Administrador')->first() ?? User::first();
        $sede = Sede::first();
        
        if (!$sede) return;

        $matrix = RiskMatrix::create([
            'title' => 'Matriz de Riesgos Operativa - Planta Rosario',
            'description' => 'Evaluación de riesgos generales para el sector de producción y mantenimiento.',
            'area' => 'Producción',
            'process' => 'Carpintería Metalúrgica',
            'empresa_id' => $sede->empresa_id,
            'sede_id' => $sede->id,
            'user_id' => $admin->id,
            'status' => 'active',
        ]);

        // Riesgo 1
        $risk1 = $matrix->risks()->create([
            'hazard' => 'Corte con disco de amoladora',
            'type' => 'Mecánico',
            'consequence' => 'Amputaciones, laceraciones graves.',
            'order' => 1,
        ]);

        $risk1->controls()->createMany([
            ['measure' => 'Uso de guantes de malla de acero', 'type' => 'EPP'],
            ['measure' => 'Protección de disco instalada y fija', 'type' => 'Ingeniería'],
            ['measure' => 'Checklist pre-uso de herramienta', 'type' => 'Administrativo'],
        ]);

        // Riesgo 2
        $risk2 = $matrix->risks()->create([
            'hazard' => 'Caída de objetos en tránsito',
            'type' => 'Mecánico / Logístico',
            'consequence' => 'Contusiones, fracturas en miembros inferiores.',
            'order' => 2,
        ]);

        $risk2->controls()->createMany([
            ['measure' => 'Calzado de seguridad con puntera', 'type' => 'EPP'],
            ['measure' => 'Delimitación de pasillos peatonales', 'type' => 'Administrativo'],
        ]);
    }
}
