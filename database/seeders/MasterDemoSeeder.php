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
        $this->command->info('🚀 Iniciando MasterDemoSeeder v3.0 (Manual Testing Mode)');
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

        // 3. Crear Gestor Minera del Sol
        $gestorMinera = User::firstOrCreate(
            ['email' => 'gestor_minera@xamasafe.com'],
            [
                'nombres' => 'Juan',
                'apellidos' => 'Gestor Minas',
                'password' => Hash::make('password'),
                'rol' => 'Administrador',
                'estado' => 'activo',
                'consultora_id' => $consultora->id,
            ]
        );
        
        // Crear Empresa Inicial (Vacía) para que el gestor tenga contexto
        $empresa = \App\Models\Empresa::firstOrCreate(
            ['cuit' => '30-55555555-5'],
            [
                'nombre' => 'Minera del Sol S.A.',
                'razon_social' => 'Minera del Sol Sociedad Anónima',
                'primary_color' => '#8B4513'
            ]
        );

        $sede = \App\Models\Sede::firstOrCreate(
             ['empresa_id' => $empresa->id, 'nombre_sede' => 'Mina San José'],
             [
                 'tipo_sede' => 'Yacimiento',
                 'direccion' => 'Ruta 40 km 3000',
                 'localidad' => 'San Juan', 
                 'correo' => 'seguridad@minera.com'
             ]
        );
        
        // Vincular gestor a la sede
        $sede->users()->syncWithoutDetaching([$gestorMinera->id]);

        $this->command->info('✅ Usuarios base creados: Admin y Gestor Minera');
        
        // ***************************************************************
        // NOTA: Se ha deshabilitado la generación automática de casos de uso
        // para permitir la prueba manual de CRUDs según caso_de_uso.md
        // ***************************************************************

        /*
        // 5. Ejecutar Caso de Uso 1 (Seguridad Industrial 2026)
        $this->command->info('🕒 Generando Caso de Uso 1: Seguridad Industrial 2026...');
        $safetySeeder = new \Database\Seeders\UseCases\SafetyTraceabilitySeeder();
        $safetySeeder->run($gestor1, $tecnico1); // Pasar usuarios para vinculación
        
        // 6. Ejecutar Caso de Uso 2 (Nuevo Cliente)
        $this->command->info('🏗️ Generando Caso de Uso 2: Nuevo Cliente...');
        // Aquí podríamos llamar a otro seeder o crear lógica ad-hoc
        $this->createNewUseCase($gestor2, $tecnico2);
        */

        $this->showSummary();
    }

    private function showSummary(): void
    {
        $this->command->info('');
        $this->command->info('🎉 ========================================');
        $this->command->info('🎉 MasterDemoSeeder COMPLETADO (Modo Manual)');
        $this->command->info('🎉 Ahora puedes ingresar con gestor_minera@xamasafe.com');
        $this->command->info('🎉 y seguir la guía en caso_de_uso.md');
        $this->command->info('🎉 ========================================');
    }
}
