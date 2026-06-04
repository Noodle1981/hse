<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$r = App\Models\MasterReport::with('sede')->latest()->first();
if ($r) {
    echo "=== MASTER REPORT ===\n";
    echo "ID: " . $r->id . "\n";
    echo "Estado: " . $r->estado . "\n";
    echo "Periodo: " . $r->periodo . "\n";
    echo "created_at: " . $r->created_at . "\n";
    echo "Titulo: " . $r->titulo . "\n";
    echo "Sede: " . ($r->sede?->nombre_sede ?? 'N/A') . "\n";
    echo "Sede ID: " . $r->sede_id . "\n";
    echo "User ID: " . $r->user_id . "\n";
    echo "observacion_revision: " . ($r->observacion_revision ?? 'NULL') . "\n";
} else {
    echo "No MasterReport found.\n";
}

echo "\n=== ANNUAL PLANS (Informe) ===\n";
$plans = App\Models\AnnualPlan::where('category', 'Informe')->get();
foreach ($plans as $p) {
    echo "ID: " . $p->id . " | activity_name: " . $p->activity_name . " | sede_id: " . $p->sede_id . " | scheduled_months: " . json_encode($p->scheduled_months) . "\n";
}

echo "\n=== USUARIOS TECNICOS ===\n";
$users = App\Models\User::where('rol', 'tecnico')->get();
foreach ($users as $u) {
    echo "ID: " . $u->id . " | Nombre: " . $u->nombres . " " . $u->apellidos . "\n";
}
