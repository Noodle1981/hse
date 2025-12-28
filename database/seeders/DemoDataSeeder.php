<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\DynamicChecklist;
use App\Models\ChecklistSection;
use App\Models\DynamicQuestion;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Admin
        $admin = User::create([
            'nombres' => 'Carlos',
            'apellidos' => 'Administrador',
            'email' => 'admin@ep.com',
            'password' => Hash::make('password'),
            'rol' => 'Administrador',
            'estado' => 'activo',
        ]);

        // 2. Crear Técnicos
        $tecnicos = [];
        $tecnicos[] = User::create([
            'nombres' => 'Juan',
            'apellidos' => 'Pérez',
            'email' => 'jperez@ep.com',
            'password' => Hash::make('password'),
            'rol' => 'Técnico',
            'estado' => 'activo',
            'matricula' => 'MAT-001',
        ]);

        $tecnicos[] = User::create([
            'nombres' => 'María',
            'apellidos' => 'González',
            'email' => 'mgonzalez@ep.com',
            'password' => Hash::make('password'),
            'rol' => 'Técnico',
            'estado' => 'activo',
            'matricula' => 'MAT-002',
        ]);

        $tecnicos[] = User::create([
            'nombres' => 'Pedro',
            'apellidos' => 'Rodríguez',
            'email' => 'prodriguez@ep.com',
            'password' => Hash::make('password'),
            'rol' => 'Técnico',
            'estado' => 'activo',
            'matricula' => 'MAT-003',
        ]);

        $tecnicos[] = User::create([
            'nombres' => 'Ana',
            'apellidos' => 'Martínez',
            'email' => 'amartinez@ep.com',
            'password' => Hash::make('password'),
            'rol' => 'Técnico',
            'estado' => 'activo',
            'matricula' => 'MAT-004',
        ]);

        // 3. Crear Empresas
        $empresa1 = Empresa::create([
            'nombre' => 'Industrias Metalúrgicas SA',
            'razon_social' => 'Industrias Metalúrgicas Sociedad Anónima',
            'cuit' => '30-12345678-9',
        ]);

        $empresa2 = Empresa::create([
            'nombre' => 'Construcciones del Sur',
            'razon_social' => 'Construcciones del Sur SRL',
            'cuit' => '30-98765432-1',
        ]);

        $empresa3 = Empresa::create([
            'nombre' => 'Alimentos Argentinos',
            'razon_social' => 'Alimentos Argentinos SA',
            'cuit' => '30-55555555-5',
        ]);

        // 4. Crear Sedes para Empresa 1
        $sede1_1 = Sede::create([
            'empresa_id' => $empresa1->id,
            'nombre_sede' => 'Planta Industrial - Avellaneda',
            'tipo_sede' => 'Planta Industrial',
            'direccion' => 'Av. Mitre 1234',
            'localidad' => 'Avellaneda, Buenos Aires',
            'telefono' => '011-4201-5678',
            'correo' => 'planta.avellaneda@metalurgicas.com',
        ]);

        $sede1_2 = Sede::create([
            'empresa_id' => $empresa1->id,
            'nombre_sede' => 'Oficinas Administrativas - CABA',
            'tipo_sede' => 'Oficina',
            'direccion' => 'Av. Corrientes 5678',
            'localidad' => 'CABA',
            'telefono' => '011-4555-1234',
            'correo' => 'admin@metalurgicas.com',
        ]);

        // 5. Crear Sedes para Empresa 2
        $sede2_1 = Sede::create([
            'empresa_id' => $empresa2->id,
            'nombre_sede' => 'Depósito Central',
            'tipo_sede' => 'Depósito',
            'direccion' => 'Ruta 3 Km 45',
            'localidad' => 'La Plata, Buenos Aires',
            'telefono' => '0221-456-7890',
            'correo' => 'deposito@construcciones.com',
        ]);

        $sede2_2 = Sede::create([
            'empresa_id' => $empresa2->id,
            'nombre_sede' => 'Obra en Construcción - Nordelta',
            'tipo_sede' => 'Obra',
            'direccion' => 'Av. de los Lagos 123',
            'localidad' => 'Tigre, Buenos Aires',
            'telefono' => '011-4700-9999',
            'correo' => 'obra.nordelta@construcciones.com',
        ]);

        // 6. Crear Sedes para Empresa 3
        $sede3_1 = Sede::create([
            'empresa_id' => $empresa3->id,
            'nombre_sede' => 'Planta de Producción',
            'tipo_sede' => 'Planta Industrial',
            'direccion' => 'Parque Industrial Lote 15',
            'localidad' => 'Rosario, Santa Fe',
            'telefono' => '0341-123-4567',
            'correo' => 'produccion@alimentos.com',
        ]);

        // 7. Asignar Técnicos a Sedes
        $sede1_1->users()->attach([$tecnicos[0]->id, $tecnicos[1]->id]); // Juan y María
        $sede1_2->users()->attach([$tecnicos[1]->id]); // María
        $sede2_1->users()->attach([$tecnicos[2]->id]); // Pedro
        $sede2_2->users()->attach([$tecnicos[2]->id, $tecnicos[3]->id]); // Pedro y Ana
        $sede3_1->users()->attach([$tecnicos[3]->id]); // Ana

        // 8. Crear Checklists con Categorías y Secciones
        $hys = Category::where('code', 'hys')->first();
        $electricidad = Subcategory::where('code', 'electricidad')->first();

        // Checklist 1: Control de EPP
        $checklist1 = DynamicChecklist::create([
            'title' => 'Control de Elementos de Protección Personal (EPP)',
            'description' => 'Verificación del uso correcto de EPP en planta industrial',
            'category' => 'hys',
            'category_id' => $hys->id,
            'subcategory_id' => null,
            'registry_code' => 'EP-REG-005',
            'is_active' => true,
            'is_draft' => false,
        ]);

        // Secciones para Checklist 1
        $section1_1 = ChecklistSection::create([
            'dynamic_checklist_id' => $checklist1->id,
            'title' => 'Protección de Cabeza',
            'description' => 'Verificación de elementos de protección craneal',
            'order' => 0,
        ]);

        $section1_2 = ChecklistSection::create([
            'dynamic_checklist_id' => $checklist1->id,
            'title' => 'Protección de Manos',
            'description' => 'Verificación de guantes y protección de extremidades superiores',
            'order' => 1,
        ]);

        $section1_3 = ChecklistSection::create([
            'dynamic_checklist_id' => $checklist1->id,
            'title' => 'Protección de Pies',
            'description' => 'Verificación de calzado de seguridad',
            'order' => 2,
        ]);

        // Preguntas para Checklist 1
        DynamicQuestion::create([
            'dynamic_checklist_id' => $checklist1->id,
            'section_id' => $section1_1->id,
            'label' => '¿El trabajador utiliza casco de seguridad?',
            'type' => 'radio',
            'options' => ['Sí', 'No', 'N/A'],
            'is_required' => true,
            'order' => 0,
        ]);

        DynamicQuestion::create([
            'dynamic_checklist_id' => $checklist1->id,
            'section_id' => $section1_1->id,
            'label' => 'Estado del casco',
            'type' => 'select',
            'options' => ['Bueno', 'Regular', 'Malo'],
            'is_required' => true,
            'order' => 1,
        ]);

        DynamicQuestion::create([
            'dynamic_checklist_id' => $checklist1->id,
            'section_id' => $section1_2->id,
            'label' => '¿Utiliza guantes apropiados para la tarea?',
            'type' => 'radio',
            'options' => ['Sí', 'No', 'N/A'],
            'is_required' => true,
            'order' => 2,
        ]);

        DynamicQuestion::create([
            'dynamic_checklist_id' => $checklist1->id,
            'section_id' => $section1_2->id,
            'label' => 'Tipo de guantes',
            'type' => 'text',
            'is_required' => false,
            'order' => 3,
        ]);

        DynamicQuestion::create([
            'dynamic_checklist_id' => $checklist1->id,
            'section_id' => $section1_3->id,
            'label' => '¿Utiliza calzado de seguridad con puntera de acero?',
            'type' => 'radio',
            'options' => ['Sí', 'No'],
            'is_required' => true,
            'order' => 4,
        ]);

        // Checklist 2: Inspección Eléctrica
        $checklist2 = DynamicChecklist::create([
            'title' => 'Inspección de Instalaciones Eléctricas',
            'description' => 'Control de seguridad en instalaciones eléctricas',
            'category' => 'hys',
            'category_id' => $hys->id,
            'subcategory_id' => $electricidad->id,
            'registry_code' => 'REG-001-0T',
            'is_active' => true,
            'is_draft' => false,
        ]);

        // Secciones para Checklist 2
        $section2_1 = ChecklistSection::create([
            'dynamic_checklist_id' => $checklist2->id,
            'title' => 'Tableros Eléctricos',
            'order' => 0,
        ]);

        $section2_2 = ChecklistSection::create([
            'dynamic_checklist_id' => $checklist2->id,
            'title' => 'Cableado y Conexiones',
            'order' => 1,
        ]);

        // Preguntas para Checklist 2
        DynamicQuestion::create([
            'dynamic_checklist_id' => $checklist2->id,
            'section_id' => $section2_1->id,
            'label' => '¿El tablero eléctrico está correctamente señalizado?',
            'type' => 'radio',
            'options' => ['Sí', 'No'],
            'is_required' => true,
            'order' => 0,
        ]);

        DynamicQuestion::create([
            'dynamic_checklist_id' => $checklist2->id,
            'section_id' => $section2_1->id,
            'label' => '¿Cuenta con disyuntor diferencial?',
            'type' => 'radio',
            'options' => ['Sí', 'No'],
            'is_required' => true,
            'order' => 1,
        ]);

        DynamicQuestion::create([
            'dynamic_checklist_id' => $checklist2->id,
            'section_id' => $section2_2->id,
            'label' => 'Estado general del cableado',
            'type' => 'select',
            'options' => ['Excelente', 'Bueno', 'Regular', 'Malo'],
            'is_required' => true,
            'order' => 2,
        ]);

        DynamicQuestion::create([
            'dynamic_checklist_id' => $checklist2->id,
            'section_id' => $section2_2->id,
            'label' => 'Observaciones adicionales',
            'type' => 'textarea',
            'is_required' => false,
            'help_text' => 'Describa cualquier anomalía encontrada',
            'order' => 3,
        ]);

        // Checklist 3: Sin secciones (legacy style)
        $calidad = Category::where('code', 'calidad')->first();
        
        $checklist3 = DynamicChecklist::create([
            'title' => 'Control de Calidad - Producto Terminado',
            'description' => 'Verificación de estándares de calidad en producto final',
            'category' => 'calidad',
            'category_id' => $calidad->id,
            'subcategory_id' => null,
            'registry_code' => null,
            'is_active' => true,
            'is_draft' => false,
        ]);

        // Preguntas sin sección
        DynamicQuestion::create([
            'dynamic_checklist_id' => $checklist3->id,
            'section_id' => null,
            'label' => '¿El producto cumple con las especificaciones técnicas?',
            'type' => 'radio',
            'options' => ['Sí', 'No'],
            'is_required' => true,
            'order' => 0,
        ]);

        DynamicQuestion::create([
            'dynamic_checklist_id' => $checklist3->id,
            'section_id' => null,
            'label' => 'Número de lote',
            'type' => 'text',
            'is_required' => true,
            'order' => 1,
        ]);

        DynamicQuestion::create([
            'dynamic_checklist_id' => $checklist3->id,
            'section_id' => null,
            'label' => 'Fecha de producción',
            'type' => 'date',
            'is_required' => true,
            'order' => 2,
        ]);

        $this->command->info('✅ Demo data creado exitosamente!');
        $this->command->info('📊 Resumen:');
        $this->command->info('   - 1 Administrador (admin@ep.com / password)');
        $this->command->info('   - 4 Técnicos');
        $this->command->info('   - 3 Empresas');
        $this->command->info('   - 6 Sedes');
        $this->command->info('   - 3 Checklists (2 con secciones, 1 sin secciones)');
        $this->command->info('   - Técnicos asignados a sedes');
    }
}
