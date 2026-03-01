<?php

namespace App\Http\Controllers;

use App\Models\EppDeliveryItem;
use Illuminate\Http\Request;

class EppQrController extends Controller
{
    /**
     * Muestra la vista pública de trazabilidad de un EPP escaneado.
     * No requiere autenticación.
     */
    public function show($token)
    {
        // Buscar el item por su UUID único de trazabilidad
        $item = EppDeliveryItem::with(['eppItem', 'eppDelivery.employee.empresa', 'eppDelivery.employee.sede'])
            ->where('codigo_qr_trazabilidad', $token)
            ->firstOrFail();

        return view('public.epp-qr-scan', compact('item'));
    }
}
