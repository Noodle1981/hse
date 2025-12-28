<?php

namespace Database\Seeders;

use App\Models\EppItem;
use App\Models\Employee;
use App\Models\Empresa;
use App\Models\Sede;
use Illuminate\Database\Seeder;

class EppDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Items de EPP
        $items = [
            ['nombre' => 'Casco MSA V-Gard Blanco', 'tipo' => 'Cabeza', 'codigo_norma' => 'IRAM 3610', 'vida_util_meses' => 24],
            ['nombre' => 'Guantes de Nitrilo High-Flex', 'tipo' => 'Manos', 'codigo_norma' => 'EN 388', 'vida_util_meses' => 3],
            ['nombre' => 'Botines de Seguridad Franca S3', 'tipo' => 'Calzado', 'codigo_norma' => 'ISO 20345', 'vida_util_meses' => 12],
            ['nombre' => 'Anteojos de Seguridad 3M Virtua', 'tipo' => 'Ocular', 'codigo_norma' => 'ANSI Z87.1', 'vida_util_meses' => 6],
            ['nombre' => 'Protector Auditivo de Copa Libus', 'tipo' => 'Auditiva', 'codigo_norma' => 'ANSI S3.19', 'vida_util_meses' => 36],
        ];

        foreach ($items as $item) {
            EppItem::firstOrCreate(['nombre' => $item['nombre']], $item);
        }

        // 2. Crear algunos empleados demo si no existen
        $empresa = Empresa::first();
        $sede = Sede::where('empresa_id', $empresa->id)->first();

        if ($empresa && $sede) {
            $employees = [
                ['name' => 'Juan', 'last_name' => 'Pérez', 'dni' => '12345678', 'cuil' => '20-12345678-9', 'puesto' => 'Operario'],
                ['name' => 'María', 'last_name' => 'Gonzalez', 'dni' => '87654321', 'cuil' => '27-87654321-0', 'puesto' => 'Supervisor'],
                ['name' => 'Carlos', 'last_name' => 'Rodriguez', 'dni' => '11223344', 'cuil' => '20-11223344-5', 'puesto' => 'Oficial Montador'],
                ['name' => 'Ana', 'last_name' => 'Martinez', 'dni' => '55667788', 'cuil' => '27-55667788-2', 'puesto' => 'Ayudante'],
                ['name' => 'Roberto', 'last_name' => 'Sanchez', 'dni' => '99887766', 'cuil' => '20-99887766-1', 'puesto' => 'Chofer'],
            ];

            foreach ($employees as $emp) {
                $employee = Employee::updateOrCreate(['dni' => $emp['dni']], array_merge($emp, [
                    'empresa_id' => $empresa->id,
                    'sede_id' => $sede->id,
                ]));

                // 3. Crear una entrega de ejemplo solo para el primer empleado para no saturar
                if ($emp['dni'] === '12345678') {
                    $tecnico = \App\Models\User::where('email', 'jperez@ep.com')->first();
                    
                    \App\Models\EppDelivery::updateOrCreate(
                        ['employee_id' => $employee->id, 'observaciones' => 'Entrega de equipo nuevo para ingreso.'],
                        [
                            'user_id' => $tecnico ? $tecnico->id : 1,
                            'delivery_date' => now(),
                            'signature' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAAAyCAYAAACqNX66AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH5AwCERYkYkS7AAAAV0lEQVR42u3BAQ0AAADCoPdPbQ43oAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAfgxvEwAB6m8yVAAAAABJRU5ErkJggg==',
                        ]
                    );
                }
            }
        }
    }
}
