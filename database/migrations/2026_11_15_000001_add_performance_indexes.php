<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $targets = [
            'users' => ['consultora_id', 'rol', 'estado'],
            'empresas' => ['consultora_id', 'activo'],
            'sedes' => ['empresa_id', 'consultora_id'],
            'assets' => ['sede_id', 'asset_type_id', 'status'],
            'visit_certificates' => ['consultora_id', 'sede_id', 'user_id', 'status'],
            'dynamic_inspections' => ['asset_id', 'sede_id', 'user_id', 'status'],
            'checklist_assignments' => ['consultora_id', 'asset_id', 'user_id', 'status', 'due_date'],
            'annual_plans' => ['consultora_id', 'empresa_id', 'sede_id', 'status'],
            'hallazgos' => ['consultora_id', 'sede_id', 'asset_id', 'estado'],
            'incidentes' => ['consultora_id', 'sede_id', 'asset_id', 'estado'],
            'master_reports' => ['consultora_id', 'empresa_id', 'sede_id', 'estado'],
        ];

        foreach ($targets as $tableName => $columns) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            foreach ($columns as $column) {
                if (Schema::hasColumn($tableName, $column) && ! Schema::hasIndex($tableName, [$column])) {
                    Schema::table($tableName, function (Blueprint $table) use ($column) {
                        $table->index($column);
                    });
                }
            }
        }
    }

    public function down(): void
    {
        // Performance indexes are non-destructive; down intentionally left safe.
    }
};
