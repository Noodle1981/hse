<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\GeneradorConstancia;
use App\Models\Sede;
use App\Models\SedeUser;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResponsableConstanciaController extends Controller
{
    public function hashing(Request $request)
    {
        $hash = Str::random(60);
        return response()->json([
                'data' => $hash,
                'message'=>'Ok'
        ], 200);
    }
    
    public function verifyToken(Request $request)
    {
        try {
            $validated = $request->validate([
                'token_responsable' => 'required|string|max:60',
            ]);
            $constancia = GeneradorConstancia::where("token_responsable",$request->token_responsable)->first();
            if ($constancia) {
                return response()->json([
                    'data' => null,
                    'message'=>'Ok'
                ], 200);
            } else {
                throw ValidationException::withMessages([
                    'url' => __('Token no válido'),
                ]);
            }
        } catch (ValidationException $e){
            return response()->json([
                'data' => null,
                'message'=>'Valores incorrectos',
                'detail'=> $e,
            ], 200);
        }
    }
    
    public function verifyCode(Request $request)
    {
        try {
            $validated = $request->validate([
                'token_responsable' => 'required|string|max:60',
                'code' => 'required|string|max:4',
            ]);
            $constancia = GeneradorConstancia::where([
                    ["code",$request->code],
                    ["token_responsable",$request->token_responsable]
                ])->with('sede')->first();
            if ($constancia) {
                
                $user = User::where("id",$constancia['user_id'])->first();
                $sede = Sede::where("id",$constancia['sede_id'])->with('empresa')->first();
                $constancia['nombre_empresa']=$sede['empresa']->nombre.' - '.$sede['nombre_sede'];
                $constancia['razon_social']=$sede['empresa']->razon_social;
                $constancia['cuit']=$sede['empresa']->cuit;
                $constancia['firma_instructor']=$user->firma;
                $constancia['date_firma_instructor']=$constancia->created_at;
                $constancia['aclaracion_instructor']=$user->nombres.' '.$user->apellidos;
                $constancia['titulo_instructor']=$user->titulo;
                $constancia['matricula_instructor']=$user->matricula;
                return response()->json([
                    'data' => $constancia,
                    'message'=>'Ok'
                ], 200);
            } else {
                throw ValidationException::withMessages([
                    'code' => __('Código no válido'),
                ]);
            }
        } catch (ValidationException $e){
            return response()->json([
                'data' => null,
                'message'=>'Valores incorrectos',
                'detail'=> $e,
            ], 200);
        }
    }
    
    public function firmaResponsable(Request $request)
    {
        try {
            $validated = $request->validate([
                //'constancia_id' => 'required|exists:generador_constancias,id',
                'token_responsable' => 'required|string|max:60',
                'code' => 'required|string|max:4',
                'firma_responsable' => 'required',
                'aclaracion_responsable' => 'nullable|string|max:255',
            ]);
            $constancia = GeneradorConstancia::where([
                    ["code",$request->code],
                    ["token_responsable",$request->token_responsable]
                ])->first();
            if ($constancia) {
                if ($validated['firma_responsable']) {
                    $constancia['firma_responsable'] = $request->firma_responsable;
                    $constancia['date_firma_responsable']=Carbon::now('America/Argentina/San_Juan');
                    $constancia->save();
                    return response()->json([
                        'data' => $constancia,
                        'message'=>'Constancia firmada'
                    ], 200);
                }
            } else {
                throw ValidationException::withMessages([
                    'code' => __('Código no válido'),
                ]);
            }
            
        } catch (ValidationException $e){
            return response()->json([
                'data' => null,
                'message'=>'Valores incorrectos',
                'detail'=> $e,
            ], 200);
        }
    }

}
