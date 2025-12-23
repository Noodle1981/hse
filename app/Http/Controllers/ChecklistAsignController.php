<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

use App\Models\ChecklistAsign;
use App\Models\ChecklistOt;
use App\Models\ChecklistCar;
use App\Models\ChecklistSga;
use App\Models\ChecklistHe;
use App\Models\ChecklistVeh;
use App\Models\ChecklistOl;
use App\Models\ChecklistEpp;
use App\Models\ChecklistExt;
use App\Models\ChecklistExtItem;
use App\Models\ChecklistHm;
use App\Models\ChecklistHmItem;
use App\Models\Sede;
use App\Models\SedeUser;
use App\Models\User;

class ChecklistAsignController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->rol == "Administrador") {
            
            $asignaciones = ChecklistAsign::all()->orderBy('created_at', 'desc')->with('sede')->get();
            return response()->json([
                'data' => $constancias,
                'message'=>'Ok'
            ], 200);
        } else {
            $sedeUser = SedeUser::select('sede_id')->where("user_id",$request->user()->id)->get();
            if($sedeUser->isNotEmpty()) {
                $asignaciones = ChecklistAsign::whereIn("sede_id",$sedeUser)->orderBy('created_at', 'desc')->with('sede')->get();
                return response()->json([
                    'data' => $asignaciones,
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
    
    public function asignaciones(Request $request, $id)
    {
        if ($request->user()->rol == "Administrador") {
            
            $asignaciones = ChecklistAsign::where("sede_id",$id)->orderBy('created_at', 'desc')->with('sede')->get();
            return response()->json([
                'data' => $asignaciones,
                'message'=>'Ok'
            ], 200);
        } else {
            $sedeUser = SedeUser::where([
                    ["user_id",$request->user()->id],
                    ["sede_id",$id]
                ])->first();
            if($sedeUser) {
                $asignaciones = ChecklistAsign::where("sede_id",$id)->orderBy('created_at', 'desc')->with('sede')->get();
                return response()->json([
                    'data' => $asignaciones,
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
        try {
            switch ($request['tipo_checklist']) {
                case 'REG-001':
                    $data_checklist = $request['data_checklist'];
                    $checklistAsign = ChecklistAsign::create($request->except('data_checklist'));
                        
                    $data_checklist['checklist_asign_id'] = $checklistAsign->id;
                    $checklist = ChecklistOt::create($data_checklist);
                    $checklistAsign['checklist_id']=$checklist->id;
                    $checklistAsign->save();
                        
                    return response()->json([
                        'data' => $checklistAsign,
                        'message'=>'Ok'
                    ], 200);
                    break;
                case 'REG-002':
                    $data_checklist = $request['data_checklist'];
                    $checklistAsign = ChecklistAsign::create($request->except('data_checklist'));
                        
                    $data_checklist['checklist_asign_id'] = $checklistAsign->id;
                    $checklist = ChecklistCar::create($data_checklist);
                    $checklistAsign['checklist_id']=$checklist->id;
                    $checklistAsign->save();
                        
                    return response()->json([
                        'data' => $checklistAsign,
                        'message'=>'Ok'
                    ], 200);
                    break;
                case 'REG-003':
                    $data_checklist = $request['data_checklist'];
                    $checklistAsign = ChecklistAsign::create($request->except('data_checklist'));
                        
                    $data_checklist['checklist_asign_id'] = $checklistAsign->id;
                    $checklist = ChecklistSga::create($data_checklist);
                    $checklistAsign['checklist_id']=$checklist->id;
                    $checklistAsign->save();
                        
                    return response()->json([
                        'data' => $checklistAsign,
                        'message'=>'Ok'
                    ], 200);
                    break;
                case 'REG-004':
                    $data_checklist = $request['data_checklist'];
                    $checklistAsign = ChecklistAsign::create($request->except('data_checklist'));
                        
                    $data_checklist['checklist_asign_id'] = $checklistAsign->id;
                    $checklist = ChecklistHe::create($data_checklist);
                    $checklistAsign['checklist_id']=$checklist->id;
                    $checklistAsign->save();
                        
                    return response()->json([
                        'data' => $checklistAsign,
                        'message'=>'Ok'
                    ], 200);
                    break;
                case 'REG-005':
                    $data_checklist = $request['data_checklist'];
                    $checklistAsign = ChecklistAsign::create($request->except('data_checklist'));
                        
                    $data_checklist['checklist_asign_id'] = $checklistAsign->id;
                    $checklist = ChecklistEpp::create($data_checklist);
                    $checklistAsign['checklist_id']=$checklist->id;
                    $checklistAsign->save();
                        
                    return response()->json([
                        'data' => $checklistAsign,
                        'message'=>'Ok'
                    ], 200);
                    break;
                case 'REG-006':
                    $data_checklist = $request['data_checklist'];
                    $checklistAsign = ChecklistAsign::create($request->except('data_checklist'));
                        
                    $data_checklist['checklist_asign_id'] = $checklistAsign->id;
                    $checklist = ChecklistVeh::create($data_checklist);
                    $checklistAsign['checklist_id']=$checklist->id;
                    $checklistAsign->save();
                        
                    return response()->json([
                        'data' => $checklistAsign,
                        'message'=>'Ok'
                    ], 200);
                    break;
                case 'REG-007':
                    $data_checklist = $request['data_checklist'];
                    $checklistAsign = ChecklistAsign::create($request->except('data_checklist'));
                        
                    $data_checklist['checklist_asign_id'] = $checklistAsign->id;
                    $checklist = ChecklistOl::create($data_checklist);
                    $checklistAsign['checklist_id']=$checklist->id;
                    $checklistAsign->save();
                        
                    return response()->json([
                        'data' => $checklistAsign,
                        'message'=>'Ok'
                    ], 200);
                    break;
                case 'REG-008':
                    $data_checklist = $request['data_checklist'];
                    $checklistAsign = ChecklistAsign::create($request->except('data_checklist'));
                        
                    $data_checklist['checklist_asign_id'] = $checklistAsign->id;
                    $items = $data_checklist['items'];
                    $data_checklist = $data_checklist->except('items');
                    $checklist = ChecklisExt::create($data_checklist);
                    $checklist->items()->saveMany($items);
                    $checklistAsign['checklist_id']=$checklist->id;
                    $checklistAsign->save();
                        
                    return response()->json([
                        'data' => $checklistAsign,
                        'message'=>'Ok'
                    ], 200);
                    break;
                case 'REG-009':
                    $data_checklist = $request['data_checklist'];
                    $checklistAsign = ChecklistAsign::create($request->except('data_checklist'));
                        
                    $data_checklist['checklist_asign_id'] = $checklistAsign->id;
                    $items = $data_checklist['items'];
                    $data_checklist = $data_checklist->except('items');
                    $checklist = ChecklistHm::create($data_checklist);
                    $checklist->items()->saveMany($items);
                    $checklistAsign['checklist_id']=$checklist->id;
                    $checklistAsign->save();
                        
                    return response()->json([
                        'data' => $checklistAsign,
                        'message'=>'Ok'
                    ], 200);
                    break;
                default:
                    return response()->json([
                        'data' => null,
                        'message'=>'Valores incorrectos'
                    ], 200);
            }
            
        } catch (ValidationException $e) {
            dd($e);
            return response()->json([
                'data' => null,
                'message'=>'Valores incorrectos',
                'reason'=> $e.message
            ], 200);
        }
        
    }
    
    public function datos($id)
    {
        try {
            $checklistAsign = ChecklistAsign::where("id",$id)->first();
            $sede = Sede::where("id",$checklistAsign['sede_id'])->first();
            $checklistAsign['nombre_sede']=$sede->nombre_sede;
            $user = User::where("id",$checklistAsign['user_id'])->first();
            $checklistAsign['firma_instructor']=(string)$user->firma;
            switch ($checklistAsign['tipo_checklist']) {
                case 'REG-001':
                    $checklist = ChecklistOt::where("id",$checklistAsign['checklist_id'])->first();
                    $checklistAsign['data_checklist']=$checklist;
                        
                    return response()->json([
                        'data' => $checklistAsign,
                        'message'=>'Ok'
                    ], 200);
                    break;
                case 'REG-002':
                    $checklist = ChecklistCar::where("id",$checklistAsign['checklist_id'])->first();
                    $checklistAsign['data_checklist']=$checklist;
                        
                    return response()->json([
                        'data' => $checklistAsign,
                        'message'=>'Ok'
                    ], 200);
                    break;
                case 'REG-003':
                    $checklist = ChecklistSga::where("id",$checklistAsign['checklist_id'])->first();
                    $checklistAsign['data_checklist']=$checklist;
                        
                    return response()->json([
                        'data' => $checklistAsign,
                        'message'=>'Ok'
                    ], 200);
                    break;
                case 'REG-004':
                    $checklist = ChecklistHe::where("id",$checklistAsign['checklist_id'])->first();
                    $checklistAsign['data_checklist']=$checklist;
                        
                    return response()->json([
                        'data' => $checklistAsign,
                        'message'=>'Ok'
                    ], 200);
                    break;
                case 'REG-005':
                    $checklist = ChecklistEpp::where("id",$checklistAsign['checklist_id'])->first();
                    $checklistAsign['data_checklist']=$checklist;
                        
                    return response()->json([
                        'data' => $checklistAsign,
                        'message'=>'Ok'
                    ], 200);
                    break;
                case 'REG-006':
                    $checklist = ChecklistVeh::where("id",$checklistAsign['checklist_id'])->first();
                    $checklistAsign['data_checklist']=$checklist;
                        
                    return response()->json([
                        'data' => $checklistAsign,
                        'message'=>'Ok'
                    ], 200);
                    break;
                case 'REG-007':
                    $checklist = ChecklistOl::where("id",$checklistAsign['checklist_id'])->first();
                    $checklistAsign['data_checklist']=$checklist;
                        
                    return response()->json([
                        'data' => $checklistAsign,
                        'message'=>'Ok'
                    ], 200);
                    break;
                case 'REG-008':
                    $checklist = ChecklistExt::with('items')->find($checklistAsign['checklist_id']);
                    $checklistAsign['data_checklist']=$checklist;
                        
                    return response()->json([
                        'data' => $checklistAsign,
                        'message'=>'Ok'
                    ], 200);
                    break;
                case 'REG-009':
                    $checklist = ChecklistHm::with('items')->find($checklistAsign['checklist_id']);
                    $checklistAsign['data_checklist']=$checklist;
                        
                    return response()->json([
                        'data' => $checklistAsign,
                        'message'=>'Ok'
                    ], 200);
                    break;
                default:
                    return response()->json([
                        'data' => null,
                        'message'=>'Valores incorrectos'
                    ], 200);
            }
            
        } catch (ValidationException $e) {
            dd($e);
            return response()->json([
                'data' => null,
                'message'=>'Valores incorrectos',
                'reason'=> $e.message
            ], 200);
        }
        
    }

    public function show(ChecklistAsign $checklistAsign)
    {
        return $checklistAsign->load('sede');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'sede_id' => 'nullable|exists:sedes,id',
            'user_id' => 'nullable|exists:users,id',
            'tipo_checklist' => 'nullable|string|max:255',
            'checklist_id' => 'nullable|integer',
        ]);
        $checklistAsign = ChecklistAsign::where("id",$id)->first();
        $checklistAsign->update($validated);

        return response()->json([
                    'data' => $checklistAsign,
                    'message'=>'Ok'
                ], 200);;
    }

    public function destroy($id)
    {
        $checklistAsign = ChecklistAsign::where("id",$id)->first();
        switch ($checklistAsign['tipo_checklist']) {
                case 'REG-001':
                    $checklist = ChecklistOt::where("id",$checklistAsign['checklist_id'])->first();
                    $checklist->delete();
                    break;
                case 'REG-002':
                    $checklist = ChecklistCar::where("id",$checklistAsign['checklist_id'])->first();
                    $checklist->delete();
                    break;
                case 'REG-003':
                    $checklist = ChecklistSga::where("id",$checklistAsign['checklist_id'])->first();
                    $checklist->delete();
                    break;
                case 'REG-004':
                    $checklist = ChecklistHe::where("id",$checklistAsign['checklist_id'])->first();
                    $checklist->delete();
                    break;
                case 'REG-005':
                    $checklist = ChecklistEpp::where("id",$checklistAsign['checklist_id'])->first();
                    $checklist->delete();
                    break;
                case 'REG-006':
                    $checklist = ChecklistVeh::where("id",$checklistAsign['checklist_id'])->first();
                    $checklist->delete();
                    break;
                case 'REG-007':
                    $checklist = ChecklistOl::where("id",$checklistAsign['checklist_id'])->first();
                    $checklist->delete();
                    break;
                default:
                    return response()->json([
                        'data' => null,
                        'message'=>'Valores incorrectos'
                    ], 200);
            }
        $checklistAsign->delete();

        return response()->json([
                'data' => null,
                'message'=>'Checklist y su asignación eliminados'
        ], 200);
    }
}
