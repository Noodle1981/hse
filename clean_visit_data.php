<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "--- STARTING VISIT DATA CLEANUP ---\n";

try {
    // Disable foreign key checks
    Schema::disableForeignKeyConstraints();

    // Tables to clean
    $tables = [
        'visit_epp_deliveries',
        'visit_trainings',
        'visit_checklist_completions',
        'hallazgos', // Need care if mixed content
        'incidentes', // Need care
        'visit_certificates',
        // 'checklist_assignments' // Maybe keep assignments? User said "clean records", assignments are operational. Let's keep assignments for now or only pending ones. 
        // Better to clean everything related to "transactions" but keep "master data" or "setup".
        // Assignments are transactions ideally.
    ];
    
    // Only delete hallazgos/incidentes linked to visits to be safe
    DB::table('hallazgos')->whereNotNull('visit_certificate_id')->delete();
    echo "Cleaned `hallazgos` linked to visits.\n";

    DB::table('incidentes')->whereNotNull('visit_certificate_id')->delete();
    echo "Cleaned `incidentes` linked to visits.\n";

    // Truncate strictly visit tables
    DB::table('visit_epp_deliveries')->truncate();
    echo "Truncated `visit_epp_deliveries`.\n";

    DB::table('visit_trainings')->truncate();
    echo "Truncated `visit_trainings`.\n";

    DB::table('visit_checklist_completions')->truncate();
    echo "Truncated `visit_checklist_completions`.\n";

    DB::table('visit_certificates')->truncate();
    echo "Truncated `visit_certificates`.\n";

    // Re-enable FK
    Schema::enableForeignKeyConstraints();

    echo "--- CLEANUP COMPLETE ---\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
