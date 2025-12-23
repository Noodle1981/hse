<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\Pruebas;
use Illuminate\Support\Facades\Mail;
use App\Models\GeneradorConstancia;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

use PDF;

class CorreoController extends Controller
{
    public function test ()
    {
        Mail::to("carlostaglio34@gmail.com")->send(new Pruebas());
        return "Ok";
    }
    
    public function attach (Request $request)
    {
        //return $_ENV['MAIL_FROM_ADDRESS'];
        $id=3;
        // Mail::to("carlostaglio34@gmail.com")->send(new Pruebas());
        $constancia = GeneradorConstancia::where("id",$id)->with('sede')->first();
        $sede = Sede::where("id",$constancia['sede_id'])->with('empresa')->first();
        $user = User::where("id",$constancia['user_id'])->first();
        $constancia['nombre_empresa']=$sede['empresa']->nombre.' - '.$sede['nombre_sede'];
        $constancia['razon_social']=$sede['empresa']->razon_social;
        $constancia['cuit']=$sede['empresa']->cuit;
        $constancia['firma_instructor']=$user->firma;
        $constancia['date_firma_instructor']=$constancia->created_at;
        $constancia['aclaracion_instructor']=$user->nombres.' '.$user->apellidos;
        $constancia['titulo_instructor']=$user->titulo;
        $constancia['matricula_instructor']=$user->matricula;
        
        Mail::send('mailConstancia', ['texto'=>'prueba'], function ($message) use ($constancia) {
            
            $pdf = PDF::loadView('constanciaPDF', ['data' => $constancia]);
            $message->to('carlostaglio34@gmail.com', 'Carlos Taglio')->subject('Constancia de visita');
            $message->attachData($pdf->output(), 'Constancia-de-Visita-'.$constancia['nombre_empresa'].'-'.$constancia['created_at']->format("m-d-Y-H-i").'.pdf', [
                'mime' => 'application/pdf',
            ]);
        });
        Mail::send('mailConstancia', ['texto'=> 'prueba'], function ($message) use ($constancia) {
            
            $pdf = PDF::loadView('constanciaPDF', ['data' => $constancia]);
            $message->to(App::environment)->subject('Constancia de visita');
            $message->attachData($pdf->output(), 'Constancia-de-Visita-'.$constancia['nombre_empresa'].'-'.$constancia['created_at']->format("m-d-Y-H-i").'.pdf', [
                'mime' => 'application/pdf',
            ]);
        });
        return "Ok Attachment";
    }
    
    public function sendToken(Request $request)
    {
        try {
            $validated = $request->validate([
                'constancia_id' => 'required|exists:generador_constancias,id',
                'asunto' => 'required|string|max:255',
                'email_responsable' => 'required|email',
                'texto' => 'required|string|max:255',
            ]);
            if($validated) {
                $generadorConstancia = GeneradorConstancia::where("id",$validated['constancia_id'])->first();
                if($generadorConstancia) {
                    $generadorConstancia['email_responsable'] = $validated['email_responsable'];
                    $generadorConstancia['token_responsable'] = Str::random(60);
                    $generadorConstancia['code'] = Str::random(4);
                    $generadorConstancia->save();
                    
                    Mail::send('mailConstancia', [
                            'texto' => $request->texto,
                            'link' => $_ENV['APP_URL'].'/verification/'.$generadorConstancia->token_responsable,
                            'code' => $generadorConstancia->code,
                        ], function ($message) use ($request) {
                            $message->to($request->email_responsable)->subject($request->asunto);
                    });
                    $texto = 'Recordatorio de firma de la constancia Constancia-de-Visita-'.$generadorConstancia['nombre_empresa'].'-'.$generadorConstancia['created_at']->format("m-d-Y-H-i").' enviada a la dirección '.$request->email_responsable.' con el texto: '.$request->texto;
                    Mail::send('mailConstancia', ['texto' => $texto], function ($message) use ($request) {
                            $message->to($_ENV['MAIL_FROM_ADDRESS'])->subject($request->asunto);
                        });
                    
                    return response()->json([
                        'data' => null,
                        'message'=>'Token enviado'
                    ], 200);
                } else {
                    return response()->json([
                        'data' => null,
                        'message'=>'Constancia inexistente'
                    ], 200);
                }
            } else {
                return response()->json([
                    'data' => null,
                    'message'=>'Error'
                ], 200);
            }
            

            return response()->noContent();
        } catch (ValidationException $e) {
            return response()->json([
                'data' => null,
                'message'=>'Valores incorrectos',
                'detail'=> $e,
            ], 200);
        }
    }
    
    public function constancia (Request $request)
    {
        $validated = $request->validate([
                'constancia_id' => 'required|exists:generador_constancias,id',
                'asunto' => 'required|string|max:255',
                'email_responsable' => 'required|email',
                'texto' => 'required|string|max:255',
            ]);
        if ($validated) {
            $constancia = GeneradorConstancia::where("id",$request->constancia_id)->with('sede')->first();
            $sede = Sede::where("id",$constancia['sede_id'])->with('empresa')->first();
            $user = User::where("id",$constancia['user_id'])->first();
            $constancia['nombre_empresa']=$sede['empresa']->nombre;
            $constancia['razon_social']=$sede['empresa']->razon_social;
            $constancia['cuit']=$sede['empresa']->cuit;
            $constancia['firma_instructor']=$user->firma;
            $constancia['date_firma_instructor']=$constancia->created_at;
            $constancia['aclaracion_instructor']=$user->nombres.' '.$user->apellidos;
            $constancia['titulo_instructor']=$user->titulo;
            $constancia['matricula_instructor']=$user->matricula;
        
            Mail::send('mailConstancia', ['texto' => $request->texto], function ($message) use ($constancia, $request) {
            
                $pdf = PDF::loadView('constanciaPDF', ['data' => $constancia]);
                $message->to($request->email_responsable)->subject($request->asunto);
                $message->attachData($pdf->output(), 'Constancia-de-Visita-'.$constancia['nombre_empresa'].'-'.$constancia['created_at']->format("m-d-Y-H-i").'.pdf', [
                    'mime' => 'application/pdf',
                ]);
            });
            $texto = 'Constancia Constancia-de-Visita-'.$constancia['nombre_empresa'].'-'.$constancia['created_at']->format("m-d-Y-H-i").'.pdf enviada a la dirección '.$request->email_responsable.' con el texto: '.$request->texto;
            Mail::send('mailConstancia', ['texto' => $texto], function ($message) use ($constancia, $request) {
                $message->to($_ENV['MAIL_FROM_ADDRESS'])->subject($request->asunto);
            });
            return response()->json([
                'data' => null,
                'message'=>'Correo enviado'
            ], 200);
        } else {
            return response()->json([
                'data' => null,
                'message'=>'Error'
            ], 200);
        }
        
    }
    
    public function constanciaResponsable (Request $request)
    {
        $validated = $request->validate([
                'token_responsable' => 'required|string|max:60',
                'code' => 'required|string|max:4',
                'asunto' => 'required|string|max:255',
                'email_responsable' => 'required|email',
                'texto' => 'required|string|max:255',
            ]);
        if ($validated) {
            $constancia = GeneradorConstancia::where([
                    ["code",$request->code],
                    ["token_responsable",$request->token_responsable]
                ])->first();
            if ($constancia) {
                $sede = Sede::where("id",$constancia['sede_id'])->with('empresa')->first();
                $user = User::where("id",$constancia['user_id'])->first();
                $constancia['nombre_empresa']=$sede['empresa']->nombre;
                $constancia['razon_social']=$sede['empresa']->razon_social;
                $constancia['cuit']=$sede['empresa']->cuit;
                $constancia['firma_instructor']=$user->firma;
                $constancia['date_firma_instructor']=$constancia->created_at;
                $constancia['aclaracion_instructor']=$user->nombres.' '.$user->apellidos;
                $constancia['titulo_instructor']=$user->titulo;
                $constancia['matricula_instructor']=$user->matricula;
        
                Mail::send('mailConstancia', ['texto' => $request->texto], function ($message) use ($constancia, $request) {
            
                    $pdf = PDF::loadView('constanciaPDF', ['data' => $constancia]);
                    $message->to($request->email_responsable)->subject($request->asunto);
                    $message->attachData($pdf->output(), 'Constancia-de-Visita-'.$constancia['nombre_empresa'].'-'.$constancia['created_at']->format("m-d-Y-H-i").'.pdf', [
                            'mime' => 'application/pdf',
                        ]);
                    });
                $texto = 'Constancia Constancia-de-Visita-'.$constancia['nombre_empresa'].'-'.$constancia['created_at']->format("m-d-Y-H-i").'.pdf enviada a la dirección '.$request->email_responsable.' con el texto: '.$request->texto;
                Mail::send('mailConstancia', ['texto' => $texto], function ($message) use ($constancia, $request) {
                    $message->to($_ENV['MAIL_FROM_ADDRESS'])->subject($request->asunto);
                });
                return response()->json([
                    'data' => null,
                    'message'=>'Correo enviado'
                ], 200);
            } else {
                throw ValidationException::withMessages([
                    'code' => __('Código no válido'),
                ]);
            }
        } else {
            return response()->json([
                'data' => null,
                'message'=>'Error'
            ], 200);
        }
        
    }
}
