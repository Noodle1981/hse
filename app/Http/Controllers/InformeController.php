<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;
use App\Models\Informe;
use App\Models\Sede;
use App\Models\SedeUser;
use App\Models\User;

class InformeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->rol == "Administrador") {
            
            $informes = Informe::all()->orderBy('created_at', 'desc')->with('sede')->get();
            return response()->json([
                'data' => $informes,
                'message'=>'Ok'
            ], 200);
        } else {
            $sedeUser = SedeUser::select('sede_id')->where("user_id",$request->user()->id)->get();
            if($sedeUser->isNotEmpty()) {
                $informes = Informe::whereIn("sede_id",$sedeUser)->orderBy('created_at', 'desc')->with('sede')->get();
                return response()->json([
                    'data' => $informes,
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
    
    public function informes(Request $request, $id)
    {
        if ($request->user()->rol == "Administrador") {
            
            $informes = Informe::where("sede_id",$id)->orderBy('created_at', 'desc')->with('sede')->get();
            return response()->json([
                'data' => $informes,
                'message'=>'Ok'
            ], 200);
        } else {
            $sedeUser = SedeUser::where([
                    ["user_id",$request->user()->id],
                    ["sede_id",$id]
                ])->first();
            if($sedeUser) {
                $informes = Informe::where("sede_id",$id)->orderBy('created_at', 'desc')->with('sede')->get();
                return response()->json([
                    'data' => $informes,
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
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string|max:3000',
                'capacitacion_asistentes' => 'nullable|integer',
                'capacitacion_temas' => 'nullable|string',
                'fecha' => 'required|date',
            ]);
        if ($validated) {
            return response()->json([
                'data' => Informe::create($validated),
                'message'=>'Ok'
            ], 200);
        } else {
            return response()->json([
                'data' => null,
                'message'=>'Error'
            ], 200);
        }
    }

    public function show(Informe $informe)
    {
        return $informe->load('items');
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'sede_id' => 'required|exists:sedes,id',
                'user_id' => 'required|exists:users,id',
                'titulo' => 'nullable|string|max:255',
                'descripcion' => 'nullable|string|max:3000',
                'capacitacion_asistentes' => 'nullable|integer',
                'capacitacion_temas' => 'nullable|string',
                'fecha' => 'nullable|date',
            ]);
            if ($validated) {
                $informe = Informe::where("id",$id)->first();
                $informe->update($validated);
                return response()->json([
                    'data' => $informe,
                    'message'=>'Informe actualizado'
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
        $informe = Informe::where("id",$id)->first()->load('items');
        $user = User::where("id",$informe['user_id'])->first();
        $sede = Sede::where("id",$informe['sede_id'])->with('empresa')->first();
        $informe['nombre_empresa']=$sede['empresa']->nombre;
        $informe['razon_social']=$sede['empresa']->razon_social;
        $informe['cuit']=$sede['empresa']->cuit;
        $informe['firma_instructor']=$user->firma;
        $informe['date_firma_instructor']=$informe->created_at;
        $informe['aclaracion_instructor']=$user->nombres.' '.$user->apellidos;
        $informe['titulo_instructor']=$user->titulo;
        $informe['matricula_instructor']=$user->matricula;
        return response()->json([
                'data' => $informe,
                'message'=>'Ok'
        ], 200);
    }

    public function destroy($id)
    {
        
        $informe = Informe::where("id",$id)->first();
        $informe->delete();

        return response()->json([
                'data' => null,
                'message'=>'Informe eliminado'
        ], 200);;
    }
    
    
}
