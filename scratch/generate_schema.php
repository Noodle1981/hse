<?php
// Script to generate a complete markdown document of the SQLite database schema
$dbPath = __DIR__ . '/../database/database.sqlite';
if (!file_exists($dbPath)) {
    $dbPath = 'database/database.sqlite';
}
if (!file_exists($dbPath)) {
    die("Error: No se encontró el archivo de base de datos en: $dbPath\n");
}

try {
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Error de conexión: " . $e->getMessage() . "\n");
}

// Get all tables except system tables and legacy/migrations/sessions/jobs
$stmt = $pdo->query("
    SELECT name 
    FROM sqlite_master 
    WHERE type='table' 
      AND name NOT LIKE 'sqlite_%' 
      AND name NOT LIKE 'migrations' 
      AND name NOT LIKE 'sessions' 
      AND name NOT LIKE 'cache%' 
      AND name NOT LIKE 'jobs%' 
      AND name NOT LIKE 'failed_jobs' 
      AND name NOT LIKE 'password_reset_tokens'
    ORDER BY name
");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

$md = "# 🗃️ Estructura de la Base de Datos - XamaSafe (Contexto para NotebookLM)\n\n";
$md .= "Este documento describe la estructura actual de la base de datos de **XamaSafe** para que NotebookLM (u otras herramientas de IA) puedan comprender perfectamente la arquitectura del modelo de datos, campos, tipos, restricciones y relaciones.\n\n";
$md .= "## 📊 Resumen del Modelo de Datos\n\n";
$md .= "El sistema cuenta con las siguientes entidades principales:\n";
$md .= "- **Empresas y Sedes**: Estructura organizativa (`empresas`, `sedes`, `sede_users`).\n";
$md .= "- **Usuarios y Consultoras**: Gestión de cuentas (`users`, `consultoras`).\n";
$md .= "- **Checklists Dinámicos**: Plantillas, secciones, subcategorías y lógica (`dynamic_checklists`, `dynamic_questions`, `checklist_sections`, `categories`, `subcategories`).\n";
$md .= "- **Inspecciones y Respuestas**: Registros de checklist ejecutados (`dynamic_inspections`, `dynamic_answers`, `checklist_assignments`).\n";
$md .= "- **Capacitaciones y Empleados**: Control de firmas, asistencias y personal (`employees`, `trainings`, `training_attendances`).\n";
$md .= "- **Seguridad en Campo**: Incidentes, hallazgos, fotos y certificados de visitas (`incidentes`, `hallazgos`, `hallazgo_seguimientos`, `hallazgo_fotos`, `visit_certificates`).\n";
$md .= "- **Planificación**: Plan Anual de HyS (`annual_plans`).\n\n";
$md .= "---\n\n";

foreach ($tables as $table) {
    $md .= "## Tabla: `$table`\n\n";
    
    // Columns information
    $colsStmt = $pdo->query("PRAGMA table_info(`$table`)");
    $cols = $colsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    $md .= "| Campo | Tipo | Admite Nulo | Valor por Defecto | PK |\n";
    $md .= "|---|---|---|---|---|\n";
    foreach ($cols as $col) {
        $pk = $col['pk'] ? '🔑 PK' : '';
        $notnull = $col['notnull'] ? 'No ❌' : 'Sí ✅';
        $dflt = $col['dflt_value'] !== null ? "`{$col['dflt_value']}`" : '*ninguno*';
        $md .= "| `{$col['name']}` | `{$col['type']}` | $notnull | $dflt | $pk |\n";
    }
    $md .= "\n";
    
    // Foreign Keys information
    $fksStmt = $pdo->query("PRAGMA foreign_key_list(`$table`)");
    $fks = $fksStmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($fks)) {
        $md .= "### 🔗 Relaciones (Foreign Keys)\n";
        foreach ($fks as $fk) {
            $md .= "- `{$fk['from']}` → vincula con `{$fk['table']}`(`{$fk['to']}`)\n";
        }
        $md .= "\n";
    }
    
    // Indexes (indexes info is nice)
    $idxStmt = $pdo->query("PRAGMA index_list(`$table`)");
    $idxs = $idxStmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($idxs)) {
        $md .= "### 🗂️ Índices\n";
        foreach ($idxs as $idx) {
            $unique = $idx['unique'] ? ' (Único)' : '';
            $md .= "- `{$idx['name']}`$unique\n";
        }
        $md .= "\n";
    }
    
    $md .= "---\n\n";
}

$outputFile = __DIR__ . '/../base_de_datos.md';
if (file_put_contents($outputFile, $md)) {
    echo "¡Éxito! El archivo base_de_datos.md se ha generado correctamente en la raíz del proyecto.\n";
} else {
    echo "Error al guardar el archivo.\n";
}
