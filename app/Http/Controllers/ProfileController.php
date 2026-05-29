<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Show the user profile page.
     */
    public function show()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return Inertia::render('Profile/Show', [
            'user_data' => [
                'nombres' => $user->nombres,
                'apellidos' => $user->apellidos,
                'email' => $user->email,
                'telefono' => $user->telefono,
                'matricula' => $user->matricula,
                'rol' => $user->rol,
                'firma' => $user->firma,
                'firma_temp' => $user->firma_temp,
                'firma_status' => $user->firma_status,
            ],
        ]);
    }

    /**
     * Update the user's signature (pending review).
     */
    public function updateSignature(Request $request)
    {
        $request->validate([
            'signature' => 'required|string',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->firma_status === 'pending') {
            return back()->with('error', 'Ya tienes una firma en revisión por el Gestor.');
        }

        $sigData = $request->signature;

        if (preg_match('/^data:image\/(\w+);base64,/', $sigData, $type)) {
            $sigData = substr($sigData, strpos($sigData, ',') + 1);
            $type = strtolower($type[1]);
        } else {
            $type = 'png';
        }
        $sigData = base64_decode($sigData);

        if ($sigData !== false) {
            $filename = 'signatures/temp_' . $user->id . '_' . time() . '.' . $type;
            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $sigData);

            $user->update([
                'firma_temp' => $filename,
                'firma_status' => 'pending',
            ]);

            return back()->with('message', 'Nueva firma propuesta enviada a revisión por el Gestor.');
        }

        return back()->with('error', 'Formato de firma inválido.');
    }

    /**
     * Update basic profile information.
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:50',
        ]);

        $user->update([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'telefono' => $request->telefono,
        ]);

        return back()->with('message', 'Perfil actualizado correctamente.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.current_password' => 'La contraseña actual no es correcta.',
            'new_password.required' => 'La nueva contraseña es obligatoria.',
            'new_password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 6 caracteres.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('password_message', 'Contraseña actualizada exitosamente.');
    }
}
