<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Consultora;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\Provincia;
use App\Models\Employee;
use App\Models\EppItem;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Provincias
        $this->call(ProvinciaSeeder::class);
        $sanJuan = Provincia::where('nombre', 'San Juan')->first();

        // 2. Consultora Principal (EP - Equipo Preventivo)
        $consultora = Consultora::updateOrCreate(
            ['cuit' => '1321321321321'],
            [
                'nombre' => 'EP - Equipo Preventivo',
                'email' => 'consultas@ep.com.ar',
                'phone' => '02644533704',
                'fiscal_address' => 'calle tanto',
                'locality' => 'Santa Lucía',
                'province' => 'San Juan',
                'description' => 'Primer Cliente',
                'max_tecnicos' => 10,
            ]
        );

        // 3. Usuarios (según doc/credenciales.md)
        
        // Super Admin
        User::updateOrCreate(
            ['email' => 'xamanen@app.com'],
            [
                'nombres' => 'Admin',
                'apellidos' => 'XamaSafe',
                'rol' => 'super_admin',
                'password' => Hash::make('password'),
                'estado' => 'activo',
                'consultora_id' => null, // SuperAdmin es global
            ]
        );

        // Gestor (Administrador de Consultora)
        $gestor = User::updateOrCreate(
            ['email' => 'gestor@app.com'],
            [
                'nombres' => 'Gestor',
                'apellidos' => 'EP',
                'rol' => 'administrador',
                'password' => Hash::make('password'),
                'estado' => 'activo',
                'consultora_id' => $consultora->id,
            ]
        );

        // Operario (Técnico de Consultora)
        $tecnico = User::updateOrCreate(
            ['email' => 'tecnico@app.com'],
            [
                'nombres' => 'Técnico',
                'apellidos' => 'EP',
                'rol' => 'tecnico',
                'password' => Hash::make('password'),
                'estado' => 'activo',
                'consultora_id' => $consultora->id,
            ]
        );

        // 4. Empresa y Sedes
        $empresa = Empresa::updateOrCreate(
            ['cuit' => '30112233445'],
            [
                'consultora_id' => $consultora->id,
                'nombre' => 'Constructora Horizonte',
                'razon_social' => 'Constructora Horizonte S.A.',
                'direccion' => 'Av. Libertador 1234',
                'email' => 'obras@horizonte.com',
            ]
        );

        $sede1 = Sede::updateOrCreate(
            ['nombre_sede' => 'Obra Central', 'empresa_id' => $empresa->id],
            [
                'direccion' => 'Ruta 40 km 15',
                'localidad' => 'Pocito',
                'provincia_id' => $sanJuan->id,
                'tipo_sede' => 'Obra',
            ]
        );

        $sede2 = Sede::updateOrCreate(
            ['nombre_sede' => 'Planta Industrial', 'empresa_id' => $empresa->id],
            [
                'direccion' => 'Parque Industrial Albardón',
                'localidad' => 'Albardón',
                'provincia_id' => $sanJuan->id,
                'tipo_sede' => 'Planta',
            ]
        );

        // Vincular usuarios a sedes
        $gestor->sedes()->sync([$sede1->id, $sede2->id]);
        $tecnico->sedes()->sync([$sede1->id, $sede2->id]);

        // 5. Empleados de prueba
        $empleados = [
            ['name' => 'Juan', 'last_name' => 'Perez', 'dni' => '20111222', 'puesto' => 'Oficial Albañil', 'sede_id' => $sede1->id],
            ['name' => 'Luis', 'last_name' => 'Gomez', 'dni' => '25333444', 'puesto' => 'Ayudante', 'sede_id' => $sede1->id],
            ['name' => 'Ana', 'last_name' => 'Lopez', 'dni' => '30555666', 'puesto' => 'Soldadora', 'sede_id' => $sede2->id],
            ['name' => 'Marta', 'last_name' => 'Rodriguez', 'dni' => '35777888', 'puesto' => 'Operaria de Grúa', 'sede_id' => $sede2->id],
            ['name' => 'Ricardo', 'last_name' => 'Sanchez', 'dni' => '40999000', 'puesto' => 'Logística', 'sede_id' => $sede2->id],
        ];

        foreach ($empleados as $empData) {
            Employee::updateOrCreate(
                ['dni' => $empData['dni']],
                [
                    'empresa_id' => $empresa->id,
                    'sede_id' => $empData['sede_id'],
                    'name' => $empData['name'],
                    'last_name' => $empData['last_name'],
                    'puesto' => $empData['puesto'],
                    'is_active' => true,
                ]
            );
        }

        // 6. Catálogo EPP (EppItems)
        $eppItems = [
            [
                'nombre' => 'Casco de Seguridad',
                'tipo' => 'Cabeza',
                'vida_util_meses' => 24,
                'vida_util_fabricante_anios' => 5,
                'unidad_medida' => 'Unidad',
                'codigo_norma' => 'IRAM 3620',
                'stock_actual' => 50,
                'stock_seguridad' => 10,
                'requiere_control_critico' => true,
            ],
            [
                'nombre' => 'Guantes de Nitrilo',
                'tipo' => 'Ocular', // Corrección: Ocular? No, debería ser Manos. El PRD decía "Ocular" en el ejemplo pero es un error común. Lo pongo según lógica.
                'tipo' => 'Manos',
                'vida_util_meses' => 1,
                'vida_util_fabricante_anios' => 2,
                'unidad_medida' => 'Par',
                'codigo_norma' => 'IRAM 3610',
                'stock_actual' => 200,
                'stock_seguridad' => 50,
                'requiere_control_critico' => false,
            ],
            [
                'nombre' => 'Botas con puntera',
                'tipo' => 'Pies',
                'vida_util_meses' => 12,
                'vida_util_fabricante_anios' => 3,
                'unidad_medida' => 'Par',
                'codigo_norma' => 'IRAM 3610-1',
                'stock_actual' => 30,
                'stock_seguridad' => 5,
                'requiere_control_critico' => false,
            ],
            [
                'nombre' => 'Arnés de seguridad - 4 puntos',
                'tipo' => 'Cuerpo completo',
                'vida_util_meses' => 60,
                'vida_util_fabricante_anios' => 10,
                'unidad_medida' => 'Unidad',
                'codigo_norma' => 'IRAM 3622-1',
                'stock_actual' => 15,
                'stock_seguridad' => 3,
                'requiere_control_critico' => true,
            ],
        ];

        foreach ($eppItems as $itemData) {
            EppItem::updateOrCreate(
                ['nombre' => $itemData['nombre']],
                array_merge($itemData, ['is_active' => true])
            );
        }
    }
}
