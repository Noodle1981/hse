<?php

use App\Models\VisitCertificate;
use Illuminate\Support\Facades\View;

// Get the latest visit
$visit = VisitCertificate::latest()->first();

echo "--- VISIT ID: " . $visit->id . " ---\n";
echo "Status: " . $visit->status . "\n";
echo "Responsible Name (DB): " . ($visit->responsible_name ?? 'NULL') . "\n";
echo "Responsible Email (DB): " . ($visit->email_responsable ?? 'NULL') . "\n";
echo "Signature Skip Reason: " . ($visit->signature_skip_reason ?? 'NULL') . "\n";
echo "General Observations (DB): " . ($visit->general_observations ?? 'NULL') . "\n";
echo "Notes (Legacy Accessor): " . ($visit->notes ?? 'NULL') . "\n";

echo "\n--- CHECKLIST COMPLETIONS (" . $visit->checklistCompletions->count() . ") ---\n";
foreach ($visit->checklistCompletions as $completion) {
    echo "ID: " . $completion->id . "\n";
    echo "  - Completed At: " . $completion->created_at . "\n";
    echo "  - Assignment ID: " . ($completion->checklist_assignment_id ?? 'NULL') . "\n";
    echo "  - Checklist Title: " . ($completion->assignment?->checklist?->title ?? 'BROKEN RELATION') . "\n";
}

echo "\n--- PDF VIEW RENDER TEST ---\n";
try {
    $html = View::make('pdfs.visit-certificate', compact('visit'))->render();
    echo "Render Status: SUCCESS\n";
    
    // Check for critical strings in the output
    echo "Contains 'Firma Pendiente': " . (str_contains($html, 'Firma Pendiente') ? 'YES' : 'NO') . "\n";
    echo "Contains Observation Text: " . (str_contains($html, $visit->general_observations ?? 'XYZ_NOT_FOUND') ? 'YES' : 'NO') . "\n";
    
} catch (\Exception $e) {
    echo "Render Status: FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
}
