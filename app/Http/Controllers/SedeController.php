<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Models\Sede;

class SedeController extends Controller
{
    public function index()
    {
        return Sede::with('empresa')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'empresa_id' => 'required|exists:empresas,id',
            'direccion' => 'string|max:255',
        ]);
        if ($validated) {
            return Sede::create($validated);
        } else {
            return "Error";
        }
    }

    public function show(Sede $sede)
    {
        return $sede->load('empresa');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'empresa_id' => 'required|exists:empresa,id',
            'direccion' => 'string|max:255',
        ]);
        
        $sede = Sede::where("id",$id)->first();
        $sede->update($validated);

        return $sede;
    }

    public function destroy($id)
    {
        $sede = Sede::where("id",$id)->first();
        $sede->delete();

        return response()->noContent();
    }
}
