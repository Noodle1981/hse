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
    'dynamic_inspections',
    'trainings',
    'training_attendances',
    'master_reports',
    'hallazgos',
    'incidentes',
    'annual_plans',
    'visit_certificates',
    'visit_certificate_activity'
];

foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        DB::table($table)->delete();
        echo "Table $table cleared.\n";
    }
}

echo "Cleanup completed successfully!\n";
