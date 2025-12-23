<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registro;

class RegistroController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->rol == "Administrador") { 
            return response()->json([
                'data' => Registro::orderBy('created_at', 'desc')->take(10)->get(),
                'message'=>'Ok'
            ], 200);
        } else {
            return response()->json([
                'data' => Registro::where('user_id', $request->user()->id)->orderBy('created_at', 'desc')->take(10)->get(),
                'message'=>'Ok'
            ], 200);
        }
    }

}
