<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ForcePasswordChangeController extends Controller
{
    public function show()
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->must_change_password) {
            return redirect()->intended(
                $user->isGestor() || $user->isSocio() ? route('gestor.dashboard') : route('dashboard')
            );
        }

        return Inertia::render('Auth/ChangePassword');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        $redirectUrl = ($user->isGestor() || $user->isSocio())
            ? route('gestor.dashboard')
            : route('dashboard');

        return redirect($redirectUrl)->with('message', 'Contraseña actualizada exitosamente. Bienvenido al sistema.');
    }
}
