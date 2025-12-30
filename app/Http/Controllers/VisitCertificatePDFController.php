<?php

namespace App\Http\Controllers;

use App\Models\GeneradorConstancia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;

class VisitCertificatePDFController extends Controller
{
    public function download($id)
    {
        $constancia = GeneradorConstancia::with(['sede.empresa', 'user'])->findOrFail($id);
        
        // Verificar permisos
        Gate::authorize('view', $constancia);
        
        $data = $this->prepareData($constancia);
        
        $pdf = Pdf::loadView('pdf.visit-certificate-report', $data);
        
        $filename = 'constancia_visita_' . $constancia->id . '_' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    public function preview($id)
    {
        $constancia = GeneradorConstancia::with(['sede.empresa', 'user'])->findOrFail($id);
        
        // Verificar permisos
        Gate::authorize('view', $constancia);
        
        $data = $this->prepareData($constancia);
        
        return view('pdf.visit-certificate-report', $data);
    }

    private function prepareData(GeneradorConstancia $constancia)
    {
        // Cargar relaciones necesarias
        $constancia->load([
            'user',
            'sede.empresa',
            'inspections.checklist',
            'trainings',
            'riskEvaluations.risk',
            'eppDeliveries.employee.sede'
        ]);

        $user = $constancia->user;
        $sede = $constancia->sede;
        $empresa = $sede->empresa;

        return [
            'data' => [
                'id' => $constancia->id,
                'nombre_empresa' => $empresa->nombre . ' - ' . $sede->nombre_sede,
                'razon_social' => $empresa->razon_social,
                'cuit' => $empresa->cuit,
                'sede' => $constancia->sede->toArray(),
                'tipo_visita' => $constancia->tipo_visita,
                'fecha_visita' => $constancia->fecha_visita ? $constancia->fecha_visita->format('d/m/Y') : $constancia->created_at->format('d/m/Y'),
                'observacion' => $constancia->observacion,
                'firma_instructor' => $constancia->firma_instructor ?? $user->firma,
                'date_firma_instructor' => $constancia->date_firma_instructor ?? $constancia->created_at,
                'aclaracion_instructor' => $user->nombres . ' ' . $user->apellidos,
                'titulo_instructor' => $user->titulo,
                'matricula_instructor' => $user->matricula,
                'firma_responsable' => $constancia->firma_responsable,
                'date_firma_responsable' => $constancia->date_firma_responsable,
                'justificacion_firma' => $constancia->justificacion_firma,
                'aclaracion_responsable' => $constancia->aclaracion_responsable,
                'created_at' => $constancia->created_at,
                'updated_at' => $constancia->updated_at,
                // Actividades vinculadas
                'actividades' => [
                    'inspections' => $constancia->inspections,
                    'trainings' => $constancia->trainings,
                    'riskEvaluations' => $constancia->riskEvaluations,
                    'eppDeliveries' => $constancia->eppDeliveries,
                    'hallazgos' => $constancia->hallazgosActividades,
                ],
            ]
        ];
    }
}
