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
        $this->command->info('🚀 Iniciando MasterDemoSeeder v2.0 (Multi-Tenancy)');
        $this->command->info('🚀 ========================================');
        $this->command->info('');

        // 0. Sembrar Categorías
        $this->call(CategorySeeder::class);

        // 1. Crear Consultora Maestra
        $consultora = \App\Models\Consultora::firstOrCreate(
            ['cuit' => '30-11111111-9'],
            [
                'nombre' => 'XamaSafe Consulting Group',
                'max_tecnicos' => 20
            ]
        );

        // 2. Crear Super Admin
        User::firstOrCreate(
            ['email' => 'admin@xamasafe.com'],
            [
                'nombres' => 'Carlos',
                'apellidos' => 'Administrator',
                'password' => Hash::make('password'),
                'rol' => 'super_admin',
                'estado' => 'activo',
                'consultora_id' => null,
            ]
        );

        // 3. Crear Usuarios Empresa 1
        $gestor1 = User::firstOrCreate(
            ['email' => 'gestor1@empresa1.com'],
            [
                'nombres' => 'Juan',
                'apellidos' => 'Gestor Uno',
                'password' => Hash::make('password'),
                'rol' => 'Administrador',
                'estado' => 'activo',
                'consultora_id' => $consultora->id,
            ]
        );

        $tecnico1 = User::firstOrCreate(
            ['email' => 'tecnico1@empresa1.com'],
            [
                'nombres' => 'Roberto',
                'apellidos' => 'Tecnico Uno',
                'password' => Hash::make('password'),
                'rol' => 'Técnico',
                'estado' => 'activo',
                'consultora_id' => $consultora->id,
                'matricula' => 'MAT-E1-001'
            ]
        );

        // 4. Crear Usuarios Empresa 2
        $gestor2 = User::firstOrCreate(
            ['email' => 'gestor2@empresa2.com'],
            [
                'nombres' => 'Maria',
                'apellidos' => 'Gestor Dos',
                'password' => Hash::make('password'),
                'rol' => 'Administrador',
                'estado' => 'activo',
                'consultora_id' => $consultora->id,
            ]
        );

        $tecnico2 = User::firstOrCreate(
            ['email' => 'tecnico2@empresa2.com'],
            [
                'nombres' => 'Luis',
                'apellidos' => 'Tecnico Dos',
                'password' => Hash::make('password'),
                'rol' => 'Técnico',
                'estado' => 'activo',
                'consultora_id' => $consultora->id,
                'matricula' => 'MAT-E2-001'
            ]
        );

        $this->command->info('✅ Usuarios creados según tareas.md');
        
        // 5. Ejecutar Caso de Uso 1 (Seguridad Industrial 2026)
        $this->command->info('🕒 Generando Caso de Uso 1: Seguridad Industrial 2026...');
        $safetySeeder = new \Database\Seeders\UseCases\SafetyTraceabilitySeeder();
        $safetySeeder->run($gestor1, $tecnico1); // Pasar usuarios para vinculación
        
        // 6. Ejecutar Caso de Uso 2 (Nuevo Cliente)
        $this->command->info('🏗️ Generando Caso de Uso 2: Nuevo Cliente...');
        // Aquí podríamos llamar a otro seeder o crear lógica ad-hoc
        $this->createNewUseCase($gestor2, $tecnico2);

        $this->showSummary();
    }

    private function createNewUseCase($gestor, $tecnico)
    {
        // Lógica para Empresa 2
        $empresa = \App\Models\Empresa::create([
            'nombre' => 'Aceros del Sur S.A.',
            'razon_social' => 'Aceros del Sur Sociedad Anónima',
            'cuit' => '30-99999999-1',
            'primary_color' => '#1e40af', // Azul Aceros
        ]);

        $sede = $empresa->sedes()->create([
            'nombre_sede' => 'Planta Industrial Quilmes',
            'direccion' => 'Av. Mitre 1234',
            'localidad' => 'Quilmes',
            'tipo_sede' => 'Industrial'
        ]);

        $sede->users()->attach([$gestor->id, $tecnico->id]);
        
        $this->command->info('✅ Empresa 2 y Sede creadas');
    }

    private function showSummary(): void
    {
        $this->command->info('');
        $this->command->info('🎉 ========================================');
        $this->command->info('🎉 MasterDemoSeeder COMPLETADO');
        $this->command->info('🎉 ========================================');
    }
}
