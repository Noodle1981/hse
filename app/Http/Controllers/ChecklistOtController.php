<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Models\ChecklistOt;

class ChecklistOtController extends Controller
{
    public function index()
    {
        return ChecklistOt::all();
    }

    public function store(Request $request)
    {
        // $validated = $request->validate([
        //     'nombre' => 'required|string|max:255',
        //     'empresa_id' => 'required|exists:empresas,id',
        //     'direccion' => 'string|max:255',
        // ]);
        if ($validated) {
            return ChecklistOt::create();  //$validated);
        } else {
            return "Error";
        }
    }

    public function show(ChecklistOt $checklistOt)
    {
        return $checklistOt;
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'empresa_id' => 'required|exists:empresa,id',
            'direccion' => 'string|max:255',
        ]);
        
        $checklistOt = ChecklistOt::where("id",$id)->first();
        $checklistOt->update($validated);

        return $checklistOt;
    }

    public function destroy($id)
    {
        $checklistOt = ChecklistOt::where("id",$id)->first();
        $checklistOt->delete();

        return response()->noContent();
    }
}
