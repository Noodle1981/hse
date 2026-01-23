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
        $this->command->info('🚀 Iniciando MasterDemoSeeder (Modo Single-Tech)');
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
        $this->command->info('');

        // Crear Técnico Único
        $tecnico = User::firstOrCreate(
            ['email' => 'operario@ep.com'],
            [
                'nombres' => 'Roberto',
                'apellidos' => 'Operario',
                'password' => Hash::make('password'),
                'rol' => 'Técnico', // O 'Operario' según el sistema
                'estado' => 'activo',
                'matricula' => 'MAT-DEMO-001',
                'firma' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            ]
        );

        $this->command->info('✅ Técnico Demo: operario@ep.com / password');
        $this->command->info('');
        
        // Array de un solo técnico para pasar a los seeders
        $tecnicos = [$tecnico];

        // Caso 1: Construcción
        $constructionSeeder = new ConstructionSeeder();
        $constructionSeeder->setTechnicians($tecnicos);
        $constructionSeeder->run();
        $this->command->info('');

        // Caso 2: Bodega
        $winerySeeder = new WinerySeeder();
        $winerySeeder->setTechnicians($tecnicos);
        $winerySeeder->run();
        $this->command->info('');

        // Caso 3: Minería
        $miningSeeder = new MiningSeeder();
        $miningSeeder->setTechnicians($tecnicos);
        $miningSeeder->run();
        $this->command->info('');

        // Caso 4: Transporte
        $transportSeeder = new \Database\Seeders\UseCases\TransportSeeder();
        $transportSeeder->setTechnicians($tecnicos);
        $transportSeeder->run();
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
        $this->command->info('   - 1 Técnico manejando 4 Casos de Uso');
        $this->command->info('   - Tareas con vencimiento 31 de Enero generadas');
        $this->command->info('   - Matrices de Riesgo y Planes Anuales completos');
        $this->command->info('');
        $this->command->info('🔐 Credenciales:');
        $this->command->info('   Admin: admin@ep.com / password');
        $this->command->info('   Técnico: operario@ep.com / password');
        $this->command->info('');
    }
}
