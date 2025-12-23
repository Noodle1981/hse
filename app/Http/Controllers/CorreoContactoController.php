<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\Contacto;
use App\Mail\ContactoConfirm;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\App;

class CorreoContactoController extends Controller
{
    public function test ()
    {
        $name = "Juan";
        Mail::mailer('smtp2')->to("carlostaglio34@gmail.com")->send(new Contacto($name));
        return "Ok";
    }
    
    public function consulta (Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:60',
                'company' => 'required|string|max:60',
                'email' => 'required|email',
                'text' => 'required|string|max:255',
            ]);
            if ($validated) {
                Mail::mailer('smtp2')->to($validated["email"])->send(new Contacto($validated['name']));    
            
                Mail::mailer('smtp2')->to("contacto@equipopreventivo.com.ar")->send(new ContactoConfirm($validated['name'], $validated['company'], $validated['email'], $validated['text']));
                return "OK";
                
            } else {
                return response()->json([
                    'data' => null,
                    'message'=>'Error'
                ], 400);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'data' => null,
                'message'=>'Valores incorrectos',
                'detail'=> $e,
            ], 400);
        }
    }
}
