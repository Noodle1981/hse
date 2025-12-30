<?php

// Script de verificación de modelos - Fase 2 Hallazgos

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Hallazgo;
use App\Models\HallazgoSeguimiento;
use App\Models\HallazgoFoto;
use App\Models\Incidente;

echo "=== VERIFICACIÓN DE MODELOS - FASE 2 HALLAZGOS ===\n\n";

// 1. Verificar que los modelos existen
echo "✓ Hallazgo: " . (class_exists(Hallazgo::class) ? 'OK' : 'FAIL') . "\n";
echo "✓ HallazgoSeguimiento: " . (class_exists(HallazgoSeguimiento::class) ? 'OK' : 'FAIL') . "\n";
echo "✓ HallazgoFoto: " . (class_exists(HallazgoFoto::class) ? 'OK' : 'FAIL') . "\n";
echo "✓ Incidente: " . (class_exists(Incidente::class) ? 'OK' : 'FAIL') . "\n\n";

// 2. Verificar constantes
echo "=== CONSTANTES ===\n";
echo "Estados: " . Hallazgo::ESTADO_PENDIENTE . ", " . Hallazgo::ESTADO_EN_PROCESO . ", " . Hallazgo::ESTADO_RESUELTO . "\n";
echo "Criticidades: " . Hallazgo::CRITICIDAD_BAJO . ", " . Hallazgo::CRITICIDAD_ALTO . ", " . Hallazgo::CRITICIDAD_CRITICO . "\n";
echo "Tipos Incidente: " . Incidente::TIPO_ACCIDENTE . ", " . Incidente::TIPO_INCIDENTE . "\n";
echo "Tipos Foto: " . HallazgoFoto::TIPO_ANTES . ", " . HallazgoFoto::TIPO_DURANTE . ", " . HallazgoFoto::TIPO_DESPUES . "\n\n";

// 3. Verificar fillables
echo "=== FILLABLES ===\n";
$hallazgo = new Hallazgo();
echo "Hallazgo fillables: " . count($hallazgo->getFillable()) . " campos\n";

$seguimiento = new HallazgoSeguimiento();
echo "HallazgoSeguimiento fillables: " . count($seguimiento->getFillable()) . " campos\n";

$foto = new HallazgoFoto();
echo "HallazgoFoto fillables: " . count($foto->getFillable()) . " campos\n";

$incidente = new Incidente();
echo "Incidente fillables: " . count($incidente->getFillable()) . " campos\n\n";

// 4. Verificar que las tablas existen
echo "=== TABLAS ===\n";
try {
    $count = Hallazgo::count();
    echo "✓ Tabla hallazgos existe (registros: $count)\n";
} catch (\Exception $e) {
    echo "✗ Error en tabla hallazgos: " . $e->getMessage() . "\n";
}

try {
    $count = HallazgoSeguimiento::count();
    echo "✓ Tabla hallazgo_seguimientos existe (registros: $count)\n";
} catch (\Exception $e) {
    echo "✗ Error en tabla hallazgo_seguimientos: " . $e->getMessage() . "\n";
}

try {
    $count = HallazgoFoto::count();
    echo "✓ Tabla hallazgo_fotos existe (registros: $count)\n";
} catch (\Exception $e) {
    echo "✗ Error en tabla hallazgo_fotos: " . $e->getMessage() . "\n";
}

try {
    $count = Incidente::count();
    echo "✓ Tabla incidentes existe (registros: $count)\n";
} catch (\Exception $e) {
    echo "✗ Error en tabla incidentes: " . $e->getMessage() . "\n";
}

echo "\n=== VERIFICACIÓN COMPLETADA ===\n";
