<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Configuration\Exception;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UserController extends Controller
{
    public function createExampleUser() {
        $user = User::where('email', 'email@example.com')->first();
        if (!$user) {
            User::create([
                'nombres' => 'Juan',
                'apellidos' => 'Perez',
                'email' => 'email@example.com',
                'password' => '12345678',
                'n_doc' => '12345678',
                'rol' => 'Administrador',
                'genero' => 'Masculino',
                'matricula' => 'E001',
                'estado' => true,
                'titulo' => 'LIC. EN HIG. Y SEG. LABORAL',
            ]);
        }
        return 'Usuario Creado';
    }

    public function login(Request $request) {
        $user = User::where('email', $request->input('email'))->first();
    
        if (!$user) {
            return response()->json([
                'data' => null,
                'message'=>'El correo electrónico no existe'
            ], 200);
        }
    
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'data' => null,
                'message'=>'Contraseña incorrecta'
            ], 200);
        }
    
        return response()->json([
            'user' => [
                'user_id' => $user->id,
                'nombres' => $user->nombres,
                'apellidos' => $user->apellidos,
                'email' => $user->email,
                'genero' => $user->genero,
                'telefono' => $user->telefono,
                'n_doc' => $user->n_doc,
                'rol' => $user->rol,
                'matricula' => $user->matricula,
                'estado' => $user->estado,
                'firma' => $user->firma,
                'titulo' => $user->titulo,
            ],
            'token' => $user->createToken($user->email)->plainTextToken,
            '_id' => $user->id
        ]);
    }

    public function index()
    {
        $users = User::all();
        foreach ($users as $user) {
            if($user->estado == 1) {
                $user->estado = true;
            } else {
                $user->estado=false;
            }
        }
        
        return response()->json([
                'data' => $users,
                'message'=>'Ok'
            ], 200);
    }

    public function admins()
    {
        return response()->json([
                'data' => User::where('rol', 'Administrador')->get(),
                'message'=>'Ok'
            ], 200);
    }

    public function colaboradores()
    {
        return response()->json([
                'data' => User::where('rol', 'Técnico')->get(),
                'message'=>'Ok'
            ], 200);
    }
    
    public function datos($id)
    {
        $user = User::where("id",$id)->first();
        return response()->json([
                'data' => $user,
                'message'=>'Usuario Creado'
            ], 200);
    }

    public function store(Request $request)
    {
        if ($request->user()->rol != "Administrador") {
            return response()->json([
                'data' => null,
                'message'=>'No cuenta con los permisos necesarios'
            ], 200);
        }
        try {
            $validated = $request->validate([
                'nombres' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                //'email' => 'email|unique:users,email',
                'email' => 'required|email|unique:users,email',
                //'genero' => 'in:Femenino,Masculino',
                'genero' => 'nullable|string|in:Femenino,Masculino',
                'telefono' => 'nullable|string|max:255',
                'n_doc' => 'required|string|max:255',
                //'rol' => 'in:Administrador,Técnico',
                'rol' => 'required|string|in:Administrador,Técnico',
                'firma' => 'nullable|string',
                'titulo' => 'nullable|string|max:255',
                'matricula' => 'nullable|string',
            ]);
            
        } catch (ValidationException $e) {
            //dd($e);
            return response()->json([
                'data' => null,
                'message'=>'Valores incorrectos',
                'detail'=> $e,
            ], 200);
        }
        
        if ($validated) {
            $validated['password']=$request->n_doc;
            $validated['estado']=true;
            $validated['genero']='Masculino';
            return response()->json([
                'data' => User::create($validated),
                'message'=>'Usuario Creado'
            ], 200);
        } else {
            return response()->json([
                'data' => null,
                'message'=>'Error en la creación de usuario'
            ], 200);
        }
    }

    public function show(User $user)
    {
        return $user;
    }

    public function update(Request $request, $id)
    {
        
        try {
            $validated = $request->validate([
                'nombres' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                //'email' => 'email|unique:users,email',
                'email' => 'nullable',
                //'genero' => 'in:Femenino,Masculino',
                'genero' => 'nullable|string|in:Femenino,Masculino',
                'telefono' => 'nullable|string|max:255',
                'n_doc' => 'required|string|max:255',
                //'rol' => 'in:Administrador,Técnico',
                'rol' => 'required|string|in:Administrador,Técnico',
                'firma' => 'nullable|string',
                'created_at' => 'nullable',
                'updated_at' => 'nullable',
                'id' => 'nullable',
                'matricula' => 'nullable',
                'titulo' => 'nullable',
                'estado' => 'nullable',
                'email_verified_at' => 'nullable',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'data' => null,
                'message'=>'Valores incorrectos',
                'detail'=> $e,
            ], 200);
        }
        $validated['password']=$request->n_doc;
        
        $user = User::where("id",$id)->first();
        $user->update($validated);

        return response()->json([
                'data' => $user,
                'message'=>'Usuario modificado'
            ], 200);
    }
    
    public function estado(Request $request, $id)
    {
        
        try {
            $validated = $request->validate([
                'estado' => 'required|boolean'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'data' => null,
                'message'=>'Valores incorrectos',
                'detail'=> $e,
            ], 200);
        }
        
        $user = User::where("id",$id)->first();
        
        $user->update($validated);

        return response()->json([
                'data' => $user,
                'message'=>'Estado cambiado'
            ], 200);
    }

    public function destroy($id)
    {
        try {
            $user = User::where("id",$id)->first();
            if ($user) {
                
                $user->delete();

                return response()->json([
                    'data' => null,
                    'message'=>'Usuario eliminado'
                ], 200);  
            }  else {
                throw ValidationException::withMessages([
                    'user' => __('Usuario no encontrado'),
                ]);
                return response()->json([
                    'data' => null,
                    'message'=>'Error'
                ], 200);  
            }
        } catch (Throwable $e) {
            return response()->json([
                'data' => null,
                'message'=>'Error',
                'detail' => $e
            ], 200);  
        }
    }
}
