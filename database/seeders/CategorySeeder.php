<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Categoría: Higiene y Seguridad Laboral
        $hys = Category::create([
            'code' => 'hys',
            'name' => 'Higiene y Seguridad Laboral',
            'description' => 'Checklists relacionados con seguridad e higiene en el trabajo',
            'color' => '#EF4444',
            'icon' => 'shield-check',
            'is_active' => true,
            'order' => 1
        ]);

        // Subcategoría: Electricidad
        Subcategory::create([
            'category_id' => $hys->id,
            'code' => 'electricidad',
            'name' => 'Electricidad',
            'description' => 'Inspecciones de instalaciones eléctricas',
            'is_active' => true,
            'order' => 1
        ]);

        // Otras categorías
        $calidad = Category::create([
            'code' => 'calidad',
            'name' => 'Calidad',
            'description' => 'Checklists de control de calidad',
            'color' => '#3B82F6',
            'icon' => 'award',
            'is_active' => true,
            'order' => 2
        ]);

        $seguimiento = Category::create([
            'code' => 'seguimiento',
            'name' => 'Seguimiento',
            'description' => 'Checklists de seguimiento operativo',
            'color' => '#10B981',
            'icon' => 'clipboard-list',
            'is_active' => true,
            'order' => 3
        ]);

        $ambiente = Category::create([
            'code' => 'ambiente',
            'name' => 'Medio Ambiente',
            'description' => 'Checklists ambientales',
            'color' => '#059669',
            'icon' => 'leaf',
            'is_active' => true,
            'order' => 4
        ]);
    }
}
