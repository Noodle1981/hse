<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function __construct(protected ProfileService $profileService) {}

    public function show()
    {
        /** @var User $user */
        $user = Auth::user();

        return Inertia::render('Profile/Show', [
            'user_data' => $user->only([
                'nombres', 'apellidos', 'email', 'telefono',
                'matricula', 'n_doc', 'rol', 'firma',
                'firma_temp', 'firma_status', 'foto',
            ]),
        ]);
    }

    public function updateSignature(Request $request)
    {
        $request->validate(['signature' => 'required|string']);

        /** @var User $user */
        $user = Auth::user();
        $result = $this->profileService->updateSignature($user, $request->signature);

        return back()->with($result['ok'] ? 'message' : 'error', $result['message']);
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($request->has('telefono')) {
            $request->merge(['telefono' => trim((string) $request->telefono)]);
        }

        $validated = $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'telefono' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
        ], [
            'nombres.required' => 'El nombre es obligatorio.',
            'apellidos.required' => 'El apellido es obligatorio.',
            'telefono.unique' => 'Este teléfono ya está registrado por otro usuario.',
        ]);

        $this->profileService->updateProfile($user, $validated);

        return back()->with('message', 'Perfil actualizado correctamente.');
    }

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

        /** @var User $user */
        $user = Auth::user();
        $this->profileService->updatePassword($user, $request->new_password);

        return back()->with('password_message', 'Contraseña actualizada exitosamente.');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|max:15360',
        ], [
            'foto.required' => 'La foto es obligatoria.',
            'foto.image' => 'El archivo debe ser una imagen válida.',
            'foto.max' => 'La imagen no debe superar los 15 MB.',
        ]);

        /** @var User $user */
        $user = Auth::user();

        if ($request->hasFile('foto') && $this->profileService->updatePhoto($user, $request->file('foto'))) {
            return back()->with('message', 'Foto de perfil actualizada correctamente.');
        }

        return back()->with('error', 'Error al procesar la imagen.');
    }
}
