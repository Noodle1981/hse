<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;
use App\Models\GeneradorConstancia;
use App\Models\Registro;
use App\Models\Sede;
use App\Models\SedeUser;
use App\Models\User;

class GeneradorConstanciaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->rol == "Administrador") {
            
            $constancias = GeneradorConstancia::all()->orderBy('created_at', 'desc')->with('sede')->get();
            return response()->json([
                'data' => $constancias,
                'message'=>'Ok'
            ], 200);
        } else {
            $sedeUser = SedeUser::select('sede_id')->where("user_id",$request->user()->id)->get();
            if($sedeUser->isNotEmpty()) {
                $constancias = GeneradorConstancia::whereIn("sede_id",$sedeUser)->orderBy('created_at', 'desc')->with('sede')->get();
                return response()->json([
                    'data' => $constancias,
                    'message'=>'Ok'
                ], 200);
            } else {
                return response()->json([
                    'data' => null,
                    'message'=>'El usuario no se encuentra asignado a ninguna sede'
                ], 200);
            }
        
        }
    }
    
    public function constancias(Request $request, $id, $cant=null)
    {
        if ($request->user()->rol == "Administrador") {
            if (! $cant) {
                $constancias = GeneradorConstancia::where("sede_id",$id)->orderBy('created_at', 'desc')->with('sede')->get();
            } else {
                $constancias = GeneradorConstancia::where("sede_id",$id)->orderBy('created_at', 'desc')->with('sede')->take($cant)->get();
            }
            return response()->json([
                'data' => $constancias,
                'message'=>'Ok'
            ], 200);
        } else {
            $sedeUser = SedeUser::where([
                    ["user_id",$request->user()->id],
                    ["sede_id",$id]
                ])->first();
            if($sedeUser) {
                if (! $cant) {
                    $constancias = GeneradorConstancia::where("sede_id",$id)->orderBy('created_at', 'desc')->with('sede')->get();
                } else {
                    $constancias = GeneradorConstancia::where("sede_id",$id)->orderBy('created_at', 'desc')->with('sede')->take($cant)->get();
                }
                return response()->json([
                    'data' => $constancias,
                    'message'=>'Ok'
                ], 200);
            } else {
                return response()->json([
                    'data' => null,
                    'message'=>'El usuario no se encuentra asignado a la sede'
                ], 200);
            }
        
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sede_id' => 'required|exists:sedes,id',
            'user_id' => 'required|exists:users,id',
            'tipo_visita' => 'required|string|max:255',
            'observacion' => 'required|string|max:3000',
            'firma_responsable' => 'nullable',
            'aclaracion_responsable' => 'nullable|string|max:255',
        ]);
        if ($validated) {
            if ($validated['firma_responsable']) {
                $validated['date_firma_responsable']=Carbon::now('America/Argentina/San_Juan');
            }
            $constancia = GeneradorConstancia::create($validated);
            $sede = Sede::where("id",$validated['sede_id'])->with('empresa')->first();
            $registro = Registro::create([
                'user_id' => $validated['user_id'],
                'tipo_registro' => 'Constancia de visita - '.$validated['tipo_visita'],
                'descripcion' => 'Creación de constancia para la sede: '.$sede['empresa']->nombre.' - '.$sede['nombre_sede'],
                'nombre_usuario' => $request->user()->nombres.' '.$request->user()->apellidos,
                'link' => '/constancias/'.$validated['sede_id'],
            ]);
            return response()->json([
                'data' => $constancia,
                'message'=>'Ok'
            ], 200);
        } else {
            return response()->json([
                'data' => null,
                'message'=>'Error'
            ], 200);
        }
    }

    public function show(GeneradorConstancia $generadorConstancia)
    {
        return $generadorConstancia->load('sede');
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'firma_responsable' => 'required',
                'aclaracion_responsable' => 'nullable|string|max:255',
            ]);
            if ($validated) {
                if ($validated['firma_responsable']) {
                    $validated['date_firma_responsable']=Carbon::now('America/Argentina/San_Juan');
                }
                $generadorConstancia = GeneradorConstancia::where("id",$id)->first();
                $generadorConstancia['firma_responsable'] = $validated['firma_responsable'];
                $generadorConstancia['date_firma_responsable'] = $validated['date_firma_responsable'];
                $generadorConstancia->save();
                return response()->json([
                    'data' => $generadorConstancia,
                    'message'=>'Ok'
                ], 200);
            } else {
                return response()->json([
                    'data' => null,
                    'message'=>'Error'
                ], 200);
            }    
        } catch (ValidationException $e) {
            //dd($e);
            return response()->json([
                'data' => null,
                'message'=>'Valores incorrectos',
                'detail'=> $e,
            ], 200);
        }
        
    }
    
    
    
    public function datos(Request $request, $id)
    {
        $constancia = GeneradorConstancia::where("id",$id)->with('sede')->first();
        $user = User::where("id",$constancia['user_id'])->first();
        $sede = Sede::where("id",$constancia['sede_id'])->with('empresa')->first();
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
        //$empresa = Empresa::where("id",$constancia['sede'][])
        //$constancia['password']=$request->n_doc;
        return response()->json([
                'data' => $constancia,
                'message'=>'Ok'
        ], 200);
    }

    public function destroy($id)
    {
        
        $generadorConstancia = GeneradorConstancia::where("id",$id)->first();
        $generadorConstancia->delete();

        return response()->json([
                'data' => null,
                'message'=>'Constancia eliminada'
        ], 200);
    }
    
    
}
