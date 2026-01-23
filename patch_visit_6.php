<?php

$visit = App\Models\VisitCertificate::find(6);

if ($visit) {
    try {
        $visit->update([
            'general_observations' => 'Observación de prueba parcheada manualmente para validar PDF.',
            'responsible_name' => 'Juan Responsable (Parche)',
            'signature_skip_reason' => 'Prueba de visualización en PDF',
            'email_responsable' => 'test@demo.com'
        ]);
        echo "Visit 6 patched successfully.\n";
    } catch (\Exception $e) {
        echo "Error updating Visit 6: " . $e->getMessage() . "\n";
    }
} else {
    echo "Visit 6 not found.\n";
}
