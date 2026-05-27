<?php

/**
 * Script para reiniciar los planes y datos operativos de visitas, checklists,
 * capacitaciones y reportes en XamaSafe, sin borrar las tablas maestras (usuarios,
 * empresas, sedes ni definiciones de checklists).
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Tablas operativas a limpiar
$tables = [
    'annual_plans',                     // Planes anuales de HyS
    'checklist_assignments',            // Asignaciones de checklists a técnicos/sedes
    'dynamic_inspections',              // Inspecciones/checklist completados o en borrador
    'dynamic_answers',                  // Respuestas de los checklists
    'visit_certificates',               // Constancias de visitas (Proof of Presence)
    'visit_checklist_completions',      // Completados de checklists en visitas
    'constancia_actividades',           // Actividades asociadas a constancias
    'trainings',                        // Capacitaciones registradas
    'training_attendances',             // Asistencias y firmas de capacitaciones
    'visit_trainings',                  // Capacitaciones realizadas dentro de visitas
    'visit_epp_deliveries',             // Entregas de EPP en visitas
    'capacitacion_presencials',         // Registros de capacitaciones presenciales
    'capacitacion_virtuals',            // Registros de capacitaciones virtuales
    'capacitacion_virual_asistentes',   // Asistentes a capacitaciones virtuales
    'hallazgos',                        // Hallazgos y desvíos reportados en campo
    'hallazgo_fotos',                   // Evidencias fotográficas de hallazgos
    'hallazgo_seguimientos',            // Historial de cambios de estado en hallazgos
    'incidentes',                       // Incidentes reportados en campo
    'incidente_fotos',                  // Fotos asociadas a incidentes
    'master_reports',                   // Reportes Maestros consolidados de fin de mes
];

echo "Iniciando limpieza de tablas operativas...\n";

// Desactivar restricciones de clave foránea según el tipo de base de datos
if (DB::connection() instanceof \Illuminate\Database\SQLiteConnection) {
    DB::statement('PRAGMA foreign_keys = OFF;');
} else {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
}

$clearedCount = 0;
foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        DB::table($table)->truncate();
        echo " - Tabla '$table' vaciada exitosamente.\n";
        $clearedCount++;
    } else {
        echo " - Tabla '$table' no existe en esta base de datos (omitida).\n";
    }
}

// Reactivar restricciones de clave foránea
if (DB::connection() instanceof \Illuminate\Database\SQLiteConnection) {
    DB::statement('PRAGMA foreign_keys = ON;');
} else {
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
}

echo "\n¡Éxito! Se han vaciado {$clearedCount} tablas operativas de visitas y planes.\n";
echo "Los datos maestros (usuarios, empresas, sedes, plantillas y preguntas de checklists) permanecen intactos.\n";
