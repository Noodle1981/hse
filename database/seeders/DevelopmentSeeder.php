<?php

namespace Database\Seeders;

use App\Models\Consultora;
use App\Models\Empresa;
use App\Models\Provincia;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// use App\Models\Employee;

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

        // Vendedor
        User::updateOrCreate(
            ['email' => 'vendedor@app.com'],
            [
                'nombres' => 'Vendedor',
                'apellidos' => 'XamaSafe',
                'rol' => 'vendedor',
                'password' => Hash::make('password'),
                'estado' => 'activo',
                'consultora_id' => null, // Vendedor es global
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

        // 5. Empleados de prueba (Comentado en version-minina ya que el modelo fue eliminado)
        /*
        $empleados = [
            ['name' => 'Juan', 'last_name' => 'Perez', 'dni' => '20111222', 'puesto' => 'Oficial Albañil', 'sede_id' => $sede1->id],
            ['name' => 'Luis', 'last_name' => 'Gomez', 'dni' => '253103444', 'puesto' => 'Ayudante', 'sede_id' => $sede1->id],
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
        */

    }
}
