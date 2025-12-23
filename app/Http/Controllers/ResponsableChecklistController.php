<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\ChecklistAsign;
use App\Models\Sede;
use App\Models\SedeUser;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResponsableChecklistController extends Controller
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
            $asignacion = ChecklistAsign::where("token_responsable",$request->token_responsable)->first();
            if ($asignacion) {
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
            $asignacion = ChecklistAsign::where([
                    ["code",$request->code],
                    ["token_responsable",$request->token_responsable]
                ])->with('sede')->first();
            if ($asignacion) {
                
                $user = User::where("id",$asignacion['user_id'])->first();
                $sede = Sede::where("id",$asignacion['sede_id'])->with('empresa')->first();
                $asignacion['nombre_empresa']=$sede['empresa']->nombre;
                $asignacion['razon_social']=$sede['empresa']->razon_social;
                $asignacion['cuit']=$sede['empresa']->cuit;
                $asignacion['firma_instructor']=$user->firma;
                $asignacion['date_firma_instructor']=$asignacion->created_at;
                $asignacion['aclaracion_instructor']=$user->nombres.' '.$user->apellidos;
                $asignacion['titulo_instructor']=$user->titulo;
                $asignacion['matricula_instructor']=$user->matricula;
                return response()->json([
                    'data' => $asignacion,
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
            $asignacion = ChecklistAsign::where([
                    ["code",$request->code],
                    ["token_responsable",$request->token_responsable]
                ])->first();
            if ($asignacion) {
                if ($validated['firma_responsable']) {
                    $asignacion['firma_responsable'] = $request->firma_responsable;
                    $asignacion['date_firma_responsable']=Carbon::now('America/Argentina/San_Juan');
                    $asignacion->save();
                    return response()->json([
                        'data' => $asignacion,
                        'message'=>'Checklist firmado'
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
