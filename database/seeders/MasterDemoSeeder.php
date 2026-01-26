<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Consultora;
use App\Models\Empresa;
use App\Models\Sede;

class MasterDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('🚀 ========================================');
        $this->command->info('🚀 Iniciando MasterDemoSeeder - Super Admins Only');
        $this->command->info('🚀 ========================================');

        // 1. Limpieza de Usuarios
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
        Schema::enableForeignKeyConstraints();

        // 2. Crear Super Admins
        $superadmins = [
            ['nombres' => 'Omar', 'apellidos' => 'Olivera', 'email' => 'oolivera@xamasafe.com.ar'],
            ['nombres' => 'Carlos', 'apellidos' => 'Taglioreti', 'email' => 'ctaglioreti@xamasafe.com.ar'],
            ['nombres' => 'Walter', 'apellidos' => 'Jalaf', 'email' => 'wjalaf@xamasafe.com.ar'],
            // User requested specific email
            ['nombres' => 'Super', 'apellidos' => 'Admin', 'email' => 'xamamen@xamasafe.com.ar'], 
        ];

        foreach ($superadmins as $admin) {
            User::create([
                'nombres' => $admin['nombres'],
                'apellidos' => $admin['apellidos'],
                'email' => $admin['email'],
                'password' => Hash::make('password'),
                'rol' => 'super_admin', // Correct format for isSuperAdmin check
                'estado' => true, // Fixed: boolean instead of string 'activo'
                'consultora_id' => null,
            ]);
            $this->command->info("✅ Superusuario creado: {$admin['email']} / password");
        }

        $this->command->info('');
        $this->command->info('🎉 ========================================');
        $this->command->info('🎉 Seeding Completado (Solo Super Admins).');
        $this->command->info('🎉 ========================================');
    }

    private function truncateTables(array $tables)
    {
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }
    }
}
