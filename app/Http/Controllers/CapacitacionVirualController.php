<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\CapacitacionVirual;
use App\Models\Sede;
use App\Models\SedeUser;
use App\Models\User;

class CapacitacionVirualController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->rol == "Administrador") {

            $capacitaciones = CapacitacionVirual::all()->orderBy('created_at', 'desc')->with('sede')->get();
            return response()->json([
                'data' => $capacitaciones,
                'message'=>'Ok'
            ], 200);
        } else {
            $sedeUser = SedeUser::select('sede_id')->where("user_id",$request->user()->id)->get();
            if($sedeUser->isNotEmpty()) {
                $capacitaciones = CapacitacionVirual::whereIn("sede_id",$sedeUser)->orderBy('created_at', 'desc')->with('sede')->get();
                return response()->json([
                    'data' => $capacitaciones,
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
    
    public function capacitaciones(Request $request, $id)
    {
        if ($request->user()->rol == "Administrador") {

            $capacitaciones = CapacitacionVirual::where("sede_id",$id)->orderBy('created_at', 'desc')->with('sede')->get();
            foreach($capacitaciones as $capacitacion) {
                $capacitacion['tipo_capacitacion']='Virtual';
            }
            return response()->json([
                'data' => $capacitaciones,
                'message'=>'Ok'
            ], 200);
        } else {
            $sedeUser = SedeUser::where([
                    ["user_id",$request->user()->id],
                    ["sede_id",$id]
                ])->first();
            if($sedeUser) {
                $capacitaciones = CapacitacionVirual::where("sede_id",$id)->orderBy('created_at', 'desc')->with('sede')->get();
                foreach($capacitaciones as $capacitacion) {
                    $capacitacion['tipo_capacitacion']='Virtual';
                }
                return response()->json([
                    'data' => $capacitaciones,
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
            'tema' => 'required|string|max:255',
            //'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'foto' => 'nullable|string',
            'estado' => 'nullable|boolean',
            'email_responsable' => 'nullable|string|max:50',
        ]);
        if ($validated) {
            if($validated['foto']) {
                //$validated['foto'] = $request->file('foto')->store('capacitacionesVirtuales', 'private');
                $base64File = $request->input('file_data');
                // Extract and decode the base64 string
                $data = substr($base64File, strpos($base64File, ',') + 1);
                $decodedFile = base64_decode($data);
                // Determine file extension (assuming image for this example)
                $extension = explode('/', explode(':', substr($base64File, 0, strpos($base64File, ';')))[1])[1];
                $fileName = 'capacitacionesVirtuales/'.'capacitacion-'.Carbon::now()->format('Y-m-d-H-i-s'). '.' . $extension;
                // Save to the 'local' disk
                Storage::disk('local')->put($fileName, $decodedFile);
                $validated['foto'] = $fileName;
                $validated['estado']=true;
            }
            return response()->json([
                'data' => CapacitacionVirual::create($validated),
                'message'=>'Ok'
            ], 200);
        } else {
            return response()->json([
                'data' => null,
                'message'=>'Error'
            ], 200);
        }
    }

    public function show(CapacitacionVirual $capacitacion)
    {
        return $capacitacion->load('sede');
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            ]);
            if ($validated) {
                //$validated['foto'] = $request->file('foto')->store('capacitacionesPresenciales', 'private');
                //Storage::put('capacitacionesPresenciales/'., $request->file('foto'));
                $base64File = $request->input('file_data');
                // Extract and decode the base64 string
                $data = substr($base64File, strpos($base64File, ',') + 1);
                $decodedFile = base64_decode($data);
                // Determine file extension (assuming image for this example)
                $extension = explode('/', explode(':', substr($base64File, 0, strpos($base64File, ';')))[1])[1];
                $fileName = Str::random(40) . '.' . $extension;
                // Save to the 'local' disk
                Storage::disk('local')->put($fileName, $decodedFile);
                $validated['estado']=true;
                $capacitacion = CapacitacionVirual::where("id",$id)->first();
                $capacitacion->save();
                return response()->json([
                    'data' => $capacitacion,
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
        $capacitacion = CapacitacionVirual::where("id",$id)->with('sede')->first();
        $sede = Sede::where("id",$capacitacion['sede_id'])->with('empresa')->first();
        $capacitacion['nombre_empresa']=$sede['empresa']->nombre.' - '.$sede['nombre_sede'];
        return response()->json([
                'data' => $capacitacion,
                'message'=>'Ok'
        ], 200);
    }

    public function destroy($id)
    {
        $capacitacion = CapacitacionVirual::where("id",$id)->first();
        $capacitacion->delete();

        return response()->json([
                'data' => null,
                'message'=>'Capacitación eliminada'
        ], 200);
    }

}
