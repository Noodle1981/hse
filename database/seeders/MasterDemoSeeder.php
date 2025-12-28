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
        $this->command->info('🚀 Iniciando MasterDemoSeeder');
        $this->command->info('🚀 ========================================');
        $this->command->info('');

        // Crear admin
        $admin = User::create([
            'nombres' => 'Carlos',
            'apellidos' => 'Administrador',
            'email' => 'admin@ep.com',
            'password' => Hash::make('password'),
            'rol' => 'Administrador',
            'estado' => 'activo',
            'firma' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        $this->command->info('✅ Admin creado: admin@ep.com / password');
        $this->command->info('');

        // Crear técnicos globales (2 por caso de uso + 1 para transporte)
        $tecnicos = [];
        $tecnicosData = [
            ['Juan', 'Pérez', 'jperez@ep.com', 'MAT-001'],
            ['María', 'González', 'mgonzalez@ep.com', 'MAT-002'],
            ['Carlos', 'Rodríguez', 'crodriguez@ep.com', 'MAT-003'],
            ['Ana', 'Martínez', 'amartinez@ep.com', 'MAT-004'],
            ['Pedro', 'López', 'plopez@ep.com', 'MAT-005'],
            ['Laura', 'Fernández', 'lfernandez@ep.com', 'MAT-006'],
            ['Roberto', 'Sánchez', 'rsanchez@ep.com', 'MAT-007'],
        ];

        foreach ($tecnicosData as $data) {
            $tecnicos[] = User::create([
                'nombres' => $data[0],
                'apellidos' => $data[1],
                'email' => $data[2],
                'password' => Hash::make('password'),
                'rol' => 'Técnico',
                'estado' => 'activo',
                'matricula' => $data[3],
                'firma' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            ]);
        }

        $this->command->info('✅ 7 Técnicos creados');
        $this->command->info('');

        // Caso 1: Construcción
        $constructionSeeder = new ConstructionSeeder();
        $constructionSeeder->setTechnicians([$tecnicos[0], $tecnicos[1]]);
        $constructionSeeder->run();
        $this->command->info('');

        // Caso 2: Bodega
        $winerySeeder = new WinerySeeder();
        $winerySeeder->setTechnicians([$tecnicos[2], $tecnicos[3]]);
        $winerySeeder->run();
        $this->command->info('');

        // Caso 3: Minería
        $miningSeeder = new MiningSeeder();
        $miningSeeder->setTechnicians([$tecnicos[4], $tecnicos[5]]);
        $miningSeeder->run();
        $this->command->info('');

        // Caso 4: Transporte (con asignaciones)
        $transportSeeder = new \Database\Seeders\UseCases\TransportSeeder();
        $transportSeeder->setTechnicians([$tecnicos[6]]);
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
        $this->command->info('');
        $this->command->info('👥 Usuarios:');
        $this->command->info('   - 1 Administrador');
        $this->command->info('   - 7 Técnicos (4 casos de uso)');
        $this->command->info('');
        $this->command->info('🏢 Empresas y Sedes:');
        $this->command->info('   - 4 Empresas');
        $this->command->info('   - 4 Sedes (1 por empresa)');
        $this->command->info('');
        $this->command->info('📋 Checklists:');
        $this->command->info('   - 12 Checklists (8 con secciones, 4 sin secciones)');
        $this->command->info('   - ~95 Inspecciones completadas');
        $this->command->info('   - 7 Asignaciones (2 vencidas, 5 pendientes)');
        $this->command->info('');
        $this->command->info('🎓 Capacitaciones:');
        $this->command->info('   - 24 Capacitaciones (12 pasadas, 12 futuras)');
        $this->command->info('   - ~150 Asistencias registradas');
        $this->command->info('');
        $this->command->info('🛡️  EPP:');
        $this->command->info('   - 40 Empleados');
        $this->command->info('   - 20 Items de EPP');
        $this->command->info('   - 40 Entregas de EPP');
        $this->command->info('');
        $this->command->info('⚠️  Riesgos:');
        $this->command->info('   - 4 Matrices de Riesgo');
        $this->command->info('   - ~19 Riesgos evaluados');
        $this->command->info('');
        $this->command->info('📅 Contratos Anuales:');
        $this->command->info('   - 4 Contratos (48 actividades)');
        $this->command->info('');
        $this->command->info('🔐 Credenciales:');
        $this->command->info('   Admin: admin@ep.com / password');
        $this->command->info('   Técnico Construcción: jperez@ep.com / password');
        $this->command->info('   Técnico Bodega: crodriguez@ep.com / password');
        $this->command->info('   Técnico Minería: plopez@ep.com / password');
        $this->command->info('   Técnico Transporte: rsanchez@ep.com / password');
        $this->command->info('');
        $this->command->info('✨ ¡Listo para probar los dashboards!');
        $this->command->info('');
    }
}
