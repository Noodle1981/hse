<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Database\Seeders\UseCases\{ConstructionSeeder, WinerySeeder, MiningSeeder};

class MasterDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('🚀 ========================================');
        $this->command->info('🚀 Iniciando MasterDemoSeeder (Demo Trazabilidad)');
        $this->command->info('🚀 ========================================');
        $this->command->info('');

        // Crear admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@ep.com'],
            [
                'nombres' => 'Carlos',
                'apellidos' => 'Administrador',
                'password' => Hash::make('password'),
                'rol' => 'Administrador',
                'estado' => 'activo',
                'firma' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            ]
        );

        $this->command->info('✅ Admin: admin@ep.com / password');

        // Crear Técnico Único
        $tecnico = User::firstOrCreate(
            ['email' => 'operario@ep.com'],
            [
                'nombres' => 'Roberto',
                'apellidos' => 'Operario',
                'password' => Hash::make('password'),
                'rol' => 'Técnico',
                'estado' => 'activo',
                'matricula' => 'MAT-DEMO-001',
                'firma' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            ]
        );

        $this->command->info('✅ Técnico Demo: operario@ep.com / password');
        $this->command->info('');
        
        // Ejecutar Caso de Uso Único de Trazabilidad y Seguridad
        $safetySeeder = new \Database\Seeders\UseCases\SafetyTraceabilitySeeder();
        $safetySeeder->run();
        $this->command->info('');

        // Resumen final
        $this->showSummary();
    }

    private function showSummary(): void
    {
        $this->command->info('');
        $this->command->info('🎉 ========================================');
        $this->command->info('🎉 MasterDemoSeeder COMPLETADO');
        $this->command->info('🎉 ========================================');
        $this->command->info('');
        $this->command->info('📊 Resumen de datos creados:');
        $this->command->info('   - Caso de Uso Maestro: Trazabilidad y Seguridad Industrial');
        $this->command->info('   - Checklists vinculados a Matriz IPERC con Lógica Fallo');
        $this->command->info('   - Inspecciones con Evaluación Automática Pasa/No Pasa');
        $this->command->info('');
        $this->command->info('🔐 Credenciales de Acceso:');
        $this->command->info('   Admin: admin@ep.com / password');
        $this->command->info('   Técnico: operario@ep.com / password');
        $this->command->info('');
    }
}
