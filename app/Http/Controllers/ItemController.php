<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::all()->orderBy('created_at', 'desc')->get();
        return response()->json([
            'data' => $items,
            'message'=>'Ok'
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'informe_id'=> 'required|exists:informes,id',
                'observacion' => 'string|max:3000',
                'recomendacion' => 'string|max:3000',
                'imagen1' => 'nullable|string',
                'imagen2' => 'nullable|string',
            ]);
            if ($validated) {
                return response()->json([
                    'data' => Item::create($validated),
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

    public function show(Item $item)
    {
        return $item;
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'informe_id'=> 'required|exists:informes,id',
                'observacion' => 'string|max:3000',
                'recomendacion' => 'string|max:3000',
                'imagen1' => 'nullable|string',
                'imagen2' => 'nullable|string',
            ]);
            if ($validated) {
                $item = Item::where("id",$id)->first();
                $item->update($validated);
                return response()->json([
                    'data' => $item,
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
        $item = Item::where("id",$id)->first();
        return response()->json([
                'data' => $item,
                'message'=>'Ok'
        ], 200);
    }

    public function destroy($id)
    {

        $item = Item::where("id",$id)->first();
        $item->delete();

        return response()->json([
                'data' => null,
                'message'=>'Item eliminado'
        ], 200);
    }
    
    
}
