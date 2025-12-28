<?php

namespace App\Http\Controllers;

use App\Models\EppDelivery;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class EppPDFController extends Controller
{
    public function download($id)
    {
        $delivery = EppDelivery::with(['employee.empresa', 'employee.sede', 'items', 'user'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.epp-delivery', [
            'delivery' => $delivery,
        ]);
        
        return $pdf->download("constancia_epp_{$delivery->employee->dni}.pdf");
    }

    public function preview($id)
    {
        $delivery = EppDelivery::with(['employee.empresa', 'employee.sede', 'items', 'user'])->findOrFail($id);
        
        $pdf = Pdf::loadView('pdf.epp-delivery', [
            'delivery' => $delivery,
        ]);
        
        return $pdf->stream();
    }
}
