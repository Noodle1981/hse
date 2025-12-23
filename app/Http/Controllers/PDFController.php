<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeneradorConstancia;
use App\Models\Sede;
use App\Models\User;
use PDF;

class PDFController extends Controller
{
    public function testPDF()
    {
        $pdf = PDF::loadView('informePDF');
        return $pdf->stream('pdfTest.pdf');
    }
    public function generatePDF (Request $request, $id)
    {
        $constancia = GeneradorConstancia::where("id",$id)->with('sede')->first();
        $sede = Sede::where("id",$constancia['sede_id'])->with('empresa')->first();
        $user = User::where("id",$constancia['user_id'])->first();
        $constancia['nombre_empresa']=$sede['empresa']->nombre.' - '.$sede['nombre_sede'];
        $constancia['razon_social']=$sede['empresa']->razon_social;
        $constancia['cuit']=$sede['empresa']->cuit;
        $constancia['firma_instructor']=$user->firma;
        $constancia['date_firma_instructor']=$constancia->created_at->subHours(3);
        $constancia['aclaracion_instructor']=$user->nombres.' '.$user->apellidos;
        $constancia['titulo_instructor']=$user->titulo;
        $constancia['matricula_instructor']=$user->matricula;
        $constancia['created_at']=$constancia->created_at->subHours(3);
        $constancia['updated_at']=$constancia->updated_at->subHours(3);
        $pdf = PDF::loadView('constanciaPDF', ['data' => $constancia]);
        return $pdf->stream('Constancia-de-Visita-'.$constancia['nombre_empresa'].'-'.$constancia['created_at']->format("m-d-Y-H-i").'.pdf');
    }
}
