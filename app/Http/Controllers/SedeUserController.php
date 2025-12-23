<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Models\Sede;
use App\Models\SedeUser;
use App\Models\User;

class SedeUserController extends Controller
{
    public function index()
    {
        $response = collect([]);
        foreach (SedeUser::all() as $sedeUser) {
            $sede=Sede::where('id', $sedeUser->sede_id)->with('empresa')->first();
            //dd($sede);
            $user=User::where('id', $sedeUser->user_id)->first();
            $response->push((object)[
                'sede_user_id' => $sedeUser->id,
                'sede' => $sede->nombre_sede,
                'empresa' => $sede->empresa->nombre,
                'user' => $user->apellidos." ".$user->nombres,
                'user_id'=> $user->id,
                'updated_at' => $sedeUser->updated_at,
                'created_at' => $sedeUser->created_at,
        ]);
        }
        return $response;
    }
    
    public function listar_tecnicos_sede($id)
    {
        $response = collect([]);
        foreach (SedeUser::where('sede_id',$id)->get() as $sedeUser) {
            $sede=Sede::where('id', $sedeUser->sede_id)->with('empresa')->first();
            //dd($sede);
            $user=User::where('id', $sedeUser->user_id)->first();
            $response->push((object)[
                'sede_user_id' => $sedeUser->id,
                'sede' => $sede->nombre_sede,
                'empresa' => $sede->empresa->nombre,
                'nombres' => $user->nombres,
                'apellidos' => $user->apellidos,
                'user_id'=> $user->id,
                'updated_at' => $sedeUser->updated_at,
                'created_at' => $sedeUser->created_at,
        ]);
        }
        return $response;
    }
    
    public function listar_tecnicos_no_sede($id)
    {
        $response = collect([]);
        
        $sede=Sede::where('id', $id)->with('empresa')->first();
        $sedesUser =SedeUser::select('user_id')->where('sede_id',$id)->get();
        $users=User::whereNotIn('id', $sedesUser)->where('rol','Técnico')->get();
        foreach ($users as $user) {
            //dd($sede);
            $response->push((object)[
                'sede' => $sede->nombre_sede,
                'empresa' => $sede->empresa->nombre,
                'nombres' => $user->nombres,
                'apellidos' => $user->apellidos,
                'user_id'=> $user->id,
        ]);
        }
        return $response;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'sede_id' => 'required|exists:sedes,id'
        ]);
        if ($validated) {
            $sedeUser = SedeUser::where([
                    ["user_id",$request->user_id],
                    ["sede_id",$request->sede_id]
                ])->first();
            if ($sedeUser) {
                return response()->json([
                    'data' => $sedeUser,
                    'message'=>'Esta asignación ya está creada'
                ], 200);
            } else {
                return response()->json([
                    'data' => SedeUser::create($validated),
                    'message'=>'Asignación creada'
                ], 200);
            }
        } else {
            return response()->json([
                'data' => null,
                'message'=>'Error'
            ], 200);
        }
    }

    public function show(SedeUser $sedeUser)
    {
        return $sedeUser;
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'sede_id' => 'required|exists:sedes,id',
            'prev_sede_id' => 'required|exists:sedes,id'
        ]);
        if ($validated) {
            $sedeUser = SedeUser::where([
                    ["user_id",$request->user_id],
                    ["prev_sede_id",$request->prev_sede_id]
                ])->first();
            $sedeUser['sede_id'] = $request->sede_id;
            $sedeUser->save();
            return response()->json([
                'data' => $sedeUser,
                'message'=>'Asignación modificada'
            ], 200);
        } else {
            return response()->json([
                'data' => null,
                'message'=>'Error'
            ], 200);
        }

        
    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'sede_id' => 'required|exists:sedes,id'
        ]);
        $sedeUser = SedeUser::where([
                ["user_id",$request->user_id],
                ["sede_id",$request->sede_id]
            ])->first();
        if ($sedeUser) {
            $sedeUser->delete();
            return response()->json([
                'data' => null,
                'message'=>'Asignación eliminada'
            ], 200);
        } else {
            return response()->json([
                    'data' => null,
                    'message'=>'Error'
                ], 200);  
        }

        
    }
}
