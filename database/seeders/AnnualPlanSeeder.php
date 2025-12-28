<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sede;
use App\Models\AnnualPlan;
use App\Models\DynamicChecklist;

class AnnualPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sede = Sede::first();
        if (!$sede) return;

        $checklistExtintores = DynamicChecklist::where('title', 'like', '%Extintores%')->first();

        // 1. CAPACITACIONES
        AnnualPlan::create([
            'sede_id' => $sede->id,
            'category' => 'Capacitación',
            'activity_name' => 'Introducción en Higiene y Seguridad',
            'activity_code' => 'EP-CAP-001',
            'year' => 2025,
            'january' => true,
            'july' => true,
        ]);

        AnnualPlan::create([
            'sede_id' => $sede->id,
            'category' => 'Capacitación',
            'activity_name' => 'Riesgo de Corte - Elementos Cortopunzante',
            'activity_code' => 'EP-CAP-002',
            'year' => 2025,
            'march' => true,
            'september' => true,
        ]);

        // 2. INSPECCIONES
        AnnualPlan::create([
            'sede_id' => $sede->id,
            'category' => 'Inspección',
            'activity_name' => 'Extintores y Medios de Salida',
            'activity_code' => 'EP-REG-008',
            'checklist_id' => $checklistExtintores?->id,
            'year' => 2025,
            'january' => true, 'february' => true, 'march' => true, 'april' => true, 'may' => true, 'june' => true,
            'july' => true, 'august' => true, 'september' => true, 'october' => true, 'november' => true, 'december' => true,
        ]);

        // 3. REGISTROS
        AnnualPlan::create([
            'sede_id' => $sede->id,
            'category' => 'Registro',
            'activity_name' => 'Plan Anual de Capacitación',
            'activity_code' => 'EP-REG-010',
            'year' => 2025,
            'january' => true,
        ]);

        AnnualPlan::create([
            'sede_id' => $sede->id,
            'category' => 'Registro',
            'activity_name' => 'Relevamiento General de Riesgo Laboral',
            'activity_code' => 'Res.463/09-ANEXO 01',
            'year' => 2025,
            'january' => true,
        ]);
    }
}
