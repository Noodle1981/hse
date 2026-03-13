<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Models\Sede;
use App\Models\Empresa;
use App\Models\SedeUser;
use App\Models\User;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->isGestor()) { 
            return response()->json([
                'data' => Empresa::with('sedes')->get(),
                'message'=>'Ok'
            ], 200);
        } else {
            $response = collect([]);
            foreach (SedeUser::where('user_id', $request->user()->id)->get() as $sedeUser) {
                $sede=Sede::where('id', $sedeUser->sede_id)->first();
                $empresa=Empresa::where('id', $sede->empresa_id)->first();
                $empresa['sedes']=[$sede];
                
                $response->push($empresa);
            }
            if($response->isEmpty()) {
                return response()->json([
                    'data' => $response,
                    'message'=>'No tiene sedes asignadas',
                ], 200);;
            } else {
                return response()->json([
                    'data' => $response,
                    'message'=>'Ok',
                ], 200);;
            }
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'razon_social' => 'string|max:255',
                'cuit' => 'string|max:255',
                'sedes' => 'nullable',
            ]);
        } catch (ValidationException $e) {
            //dd($e);
            return response()->json([
                'data' => null,
                'message'=>'Valores incorrectos',
                'detail'=> $e,
            ], 200);
        }
        if($validated) {
            //dd($validated['nombre']);
            $empresa = Empresa::create([
                'nombre' => $validated['nombre'],
                'razon_social' => $validated['razon_social'],
                'cuit' => $validated['cuit']
                
            ]);
            $sede = Sede::create([
                'empresa_id'=> $empresa->id,
                'nombre_sede'=> $validated['sedes']['nombre_sede'],
                'tipo_sede'=> $validated['sedes']['tipo_sede'],
                'correo'=> $validated['sedes']['correo'],
                'telefono'=> $validated['sedes']['telefono'],
                'direccion'=> $validated['sedes']['direccion'],
                'localidad'=> $validated['sedes']['localidad'] ?? null,
                'responsable_1_nombre'=> $validated['sedes']['responsable_1_nombre'] ?? null,
                'responsable_1_cargo'=> $validated['sedes']['responsable_1_cargo'] ?? null,
                'responsable_1_email'=> $validated['sedes']['responsable_1_email'] ?? null,
                'responsable_2_nombre'=> $validated['sedes']['responsable_2_nombre'] ?? null,
                'responsable_2_cargo'=> $validated['sedes']['responsable_2_cargo'] ?? null,
                'responsable_2_email'=> $validated['sedes']['responsable_2_email'] ?? null,
                'responsable_3_nombre'=> $validated['sedes']['responsable_3_nombre'] ?? null,
                'responsable_3_cargo'=> $validated['sedes']['responsable_3_cargo'] ?? null,
                'responsable_3_email'=> $validated['sedes']['responsable_3_email'] ?? null,
            ]);
            return response()->json([
                    'data' => $empresa->load('sedes'),
                    'message'=>'Empresa creada'
                ], 200);
        } else {
            return response()->json([
                'data' => null,
                'message'=>'Error en la creación de empresa'
            ], 200);
        }
    }

    public function show(Empresa $empresa) 
    { 
        return $empresa->load('sedes'); 
    } 
    
    public function datos($id)
    {
        $sede=Sede::where('id', $id)->first();
        $empresa = Empresa::where("id",$sede->empresa_id)->first();
        return response()->json([
                'data' => $empresa->load('sedes'),
                'message'=>'Ok'
            ], 200);
    }

    public function update(Request $request, $id) 
    { 
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'razon_social' => 'string|max:255',
                'cuit' => 'string|max:255',
                'sedes' => 'nullable',
            ]);
        } catch (ValidationException $e) {
            //dd($e);
            return response()->json([
                'data' => null,
                'message'=>'Valores incorrectos',
                'detail'=> $e,
            ], 200);
        }
        if($validated) {
            $sede = Sede::where("id",$id)->first();
            $empresa = Empresa::where("id",$sede["empresa_id"])->first();
            
            $empresa['nombre'] = $validated['nombre'];
            $empresa['razon_social'] = $validated['razon_social'];
            $empresa['cuit'] = $validated['cuit'];
            $empresa->save();
            
            $sede['nombre_sede'] = $validated['sedes'][0]['nombre_sede'];
            $sede['tipo_sede'] = $validated['sedes'][0]['tipo_sede'];
            $sede['correo'] = $validated['sedes'][0]['correo'];
            $sede['telefono'] = $validated['sedes'][0]['telefono'];
            $sede['direccion'] = $validated['sedes'][0]['direccion'];
            $sede['localidad'] = $validated['sedes'][0]['localidad'] ?? null;
            $sede['responsable_1_nombre'] = $validated['sedes'][0]['responsable_1_nombre']?? null;
            $sede['responsable_1_cargo'] = $validated['sedes'][0]['responsable_1_cargo'] ?? null;
            $sede['responsable_1_email'] = $validated['sedes'][0]['responsable_1_email'] ?? null;
            $sede['responsable_2_nombre'] = $validated['sedes'][0]['responsable_2_nombre'] ?? null;
            $sede['responsable_2_cargo'] = $validated['sedes'][0]['responsable_2_cargo'] ?? null;
            $sede['responsable_2_email'] = $validated['sedes'][0]['responsable_2_email'] ?? null;
            $sede['responsable_3_nombre'] = $validated['sedes'][0]['responsable_3_nombre'] ?? null;
            $sede['responsable_3_cargo'] = $validated['sedes'][0]['responsable_3_cargo'] ?? null;
            $sede['responsable_3_email'] = $validated['sedes'][0]['responsable_3_email'] ?? null;
            $sede->save();
            return response()->json([
                    'data' => $empresa,
                    'message'=>'Empresa modificada'
                ], 200);
        } else {
            return response()->json([
                'data' => null,
                'message'=>'Error en la modificación de empresa'
            ], 200);
        }
    } 

    public function destroy($id) 
    { 
        $empresa = Empresa::where("id",$id)->first();
        $empresa->delete(); 
        return response()->json([
                'data' => null,
                'message'=>'Empresa eliminada'
            ], 200); 
    }
}
