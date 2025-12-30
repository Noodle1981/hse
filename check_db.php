<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== REVISIÓN DE BASE DE DATOS ===\n\n";

// Empresas
echo "📊 EMPRESAS (" . App\Models\Empresa::count() . "):\n";
foreach (App\Models\Empresa::all(['id', 'nombre']) as $empresa) {
    echo "  [{$empresa->id}] {$empresa->nombre}\n";
}

echo "\n";

// Sedes
echo "🏢 SEDES (" . App\Models\Sede::count() . "):\n";
foreach (App\Models\Sede::with('empresa')->get(['id', 'nombre_sede', 'empresa_id']) as $sede) {
    echo "  [{$sede->id}] {$sede->nombre_sede} (Empresa: {$sede->empresa->nombre})\n";
}

echo "\n";

// Matrices de Riesgo
echo "⚠️ MATRICES DE RIESGO (" . App\Models\RiskMatrix::count() . "):\n";
foreach (App\Models\RiskMatrix::with(['empresa', 'sede'])->get() as $matriz) {
    echo "  [{$matriz->id}] {$matriz->title}\n";
    echo "      Empresa: {$matriz->empresa->nombre}\n";
    echo "      Sede: {$matriz->sede->nombre_sede}\n";
    echo "      Estado: {$matriz->status}\n";
    echo "      Riesgos: " . $matriz->risks()->count() . "\n";
    echo "\n";
}

// Usuarios Admin
echo "👤 USUARIOS ADMIN:\n";
foreach (App\Models\User::where('rol', 'Administrador')->get(['id', 'name', 'email']) as $user) {
    echo "  [{$user->id}] {$user->name} ({$user->email})\n";
}

echo "\n=== FIN DE REVISIÓN ===\n";
