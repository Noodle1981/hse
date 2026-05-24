<?php
$dbPath = __DIR__ . '/../database/database.sqlite';
if (!file_exists($dbPath)) {
    $dbPath = 'database/database.sqlite';
}
if (!file_exists($dbPath)) {
    die("Database file not found at: $dbPath\n");
}

$pdo = new PDO("sqlite:$dbPath");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get tables
$stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' AND name NOT LIKE 'migrations' AND name NOT LIKE 'sessions' AND name NOT LIKE 'cache%' AND name NOT LIKE 'jobs%' AND name NOT LIKE 'failed_jobs' ORDER BY name");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "# 🗃️ Estructura de la Base de Datos - XamaSafe\n\n";
echo "Este documento contiene la estructura completa de la base de datos SQLite para XamaSafe (tablas, campos, tipos, restricciones de nulidad, valores por defecto y relaciones de clave foránea).\n\n";

foreach ($tables as $table) {
    echo "## Tabla: `$table`\n\n";
    
    // Columns
    $colsStmt = $pdo->query("PRAGMA table_info(`$table`)");
    $cols = $colsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "| Campo | Tipo | Nulo | Por Defecto | PK |\n";
    echo "|---|---|---|---|---|\n";
    foreach ($cols as $col) {
        $pk = $col['pk'] ? '🔑 PK' : '';
        $notnull = $col['notnull'] ? 'No' : 'Sí';
        $dflt = $col['dflt_value'] !== null ? "`{$col['dflt_value']}`" : '*ninguno*';
        echo "| `{$col['name']}` | `{$col['type']}` | $notnull | $dflt | $pk |\n";
    }
    echo "\n";
    
    // Foreign Keys
    $fksStmt = $pdo->query("PRAGMA foreign_key_list(`$table`)");
    $fks = $fksStmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($fks)) {
        echo "### 🔗 Relaciones (Foreign Keys)\n";
        foreach ($fks as $fk) {
            echo "- `{$fk['from']}` → `{$fk['table']}`(`{$fk['to']}`)\n";
        }
        echo "\n";
    }
    echo "---\n\n";
}
