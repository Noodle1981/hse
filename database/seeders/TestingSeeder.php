<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class TestingSeeder extends Seeder
{
    /**
     * Seeder mínimo para testing de estabilización
     * Solo crea usuarios esenciales sin datos de prueba
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('🧪 ========================================');
        $this->command->info('🧪 Iniciando TestingSeeder');
        $this->command->info('🧪 ========================================');
        $this->command->info('');

        // ADMIN (Gestor de Empresa)
        // Este es el rol "Administrador" actual del sistema
        // En el futuro, cuando se implemente multi-tenant, habrá un SuperAdmin por encima
        User::create([
            'nombres' => 'Carlos',
            'apellidos' => 'Gestor',
            'email' => 'admin@ep.com',
            'password' => Hash::make('password'),
            'rol' => 'Administrador',
            'estado' => 'activo',
            'firma' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        $this->command->info('✅ Admin/Gestor creado: admin@ep.com / password');

        // OPERARIO (Técnico de Campo)
        // Este es el rol "Técnico" actual del sistema
        // Completa checklists, registra inspecciones, etc.
        User::create([
            'nombres' => 'Juan',
            'apellidos' => 'Operario',
            'email' => 'operario@ep.com',
            'password' => Hash::make('password'),
            'rol' => 'Técnico',
            'estado' => 'activo',
            'matricula' => 'OPE-001',
            'firma' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        $this->command->info('✅ Operario/Técnico creado: operario@ep.com / password');

        $this->command->info('');
        $this->command->info('🎉 ========================================');
        $this->command->info('🎉 TestingSeeder COMPLETADO');
        $this->command->info('🎉 ========================================');
        $this->command->info('');
        $this->command->info('📊 Usuarios creados para testing:');
        $this->command->info('');
        $this->command->info('👤 ADMIN / GESTOR (Rol: Administrador):');
        $this->command->info('   Email: admin@ep.com');
        $this->command->info('   Password: password');
        $this->command->info('   Permisos: Gestión completa del sistema');
        $this->command->info('');
        $this->command->info('👤 OPERARIO (Rol: Técnico):');
        $this->command->info('   Email: operario@ep.com');
        $this->command->info('   Password: password');
        $this->command->info('   Permisos: Completar checklists, registrar inspecciones');
        $this->command->info('');
        $this->command->info('📝 NOTA: El sistema actual tiene 2 roles:');
        $this->command->info('   - Administrador = Gestor de empresa (admin@ep.com)');
        $this->command->info('   - Técnico = Operario de campo (operario@ep.com)');
        $this->command->info('');
        $this->command->info('🔮 FUTURO: Con multi-tenant habrá SuperAdmin por encima de Administrador');
        $this->command->info('');
        $this->command->info('✨ Base de datos lista para testing manual');
        $this->command->info('✨ Comienza probando el CRUD de cada módulo');
        $this->command->info('');
    }
}
