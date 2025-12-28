<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sede;
use App\Models\User;

class AssignTechniciansToSedesSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener todos los técnicos
        $tecnicos = User::where('rol', 'Técnico')->get();
        
        if ($tecnicos->isEmpty()) {
            $this->command->error('No hay técnicos en la base de datos. Ejecuta DemoDataSeeder primero.');
            return;
        }

        // Obtener todas las sedes
        $sedes = Sede::all();
        
        if ($sedes->isEmpty()) {
            $this->command->error('No hay sedes en la base de datos. Ejecuta DemoDataSeeder primero.');
            return;
        }

        // Limpiar asignaciones existentes
        foreach ($sedes as $sede) {
            $sede->users()->detach();
        }

        $this->command->info('Asignaciones anteriores eliminadas.');

        // Asignar técnicos a sedes
        $asignaciones = 0;
        
        foreach ($sedes as $index => $sede) {
            // Asignar 1-2 técnicos por sede de forma rotativa
            $tecnicosParaSede = $tecnicos->slice($index % $tecnicos->count(), 2);
            
            foreach ($tecnicosParaSede as $tecnico) {
                $sede->users()->attach($tecnico->id);
                $asignaciones++;
                $this->command->info("✓ {$tecnico->apellidos} asignado a {$sede->nombre_sede}");
            }
        }

        $this->command->info("✅ {$asignaciones} asignaciones creadas exitosamente!");
    }
}
