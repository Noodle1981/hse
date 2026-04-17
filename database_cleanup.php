<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = [
    'checklist_assignments',
    'visit_checklist_completions',
    'visit_trainings',
    'dynamic_inspections',
    'dynamic_answers',
    'trainings',
    'training_attendances',
    'master_reports',
    'hallazgos',
    'hallazgo_fotos',
    'hallazgo_seguimientos',
    'incidentes',
    'incidente_fotos',
    'annual_plans',
    'visit_certificates',
    'constancia_actividades',
    'capacitacion_presencials',
    'capacitacion_viruals',
    'capacitacion_virual_asistentes'
];

if (DB::connection() instanceof \Illuminate\Database\SQLiteConnection) {
    DB::statement('PRAGMA foreign_keys = OFF;');
} else {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
}

foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        DB::table($table)->truncate();
        echo "Table $table cleared (truncated).\n";
    }
}

if (DB::connection() instanceof \Illuminate\Database\SQLiteConnection) {
    DB::statement('PRAGMA foreign_keys = ON;');
} else {
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
}

echo "Cleanup completed successfully!\n";
