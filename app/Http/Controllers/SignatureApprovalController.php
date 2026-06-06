<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SignatureApprovalController extends Controller
{
    /**
     * List all users with pending signature reviews (Only for Gestores/Admins).
     */
    public function pending()
    {
        $this->ensureGestor();

        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        $query = User::where('firma_status', 'pending');

        if (! $currentUser->isSuperAdmin()) {
            $query->where('consultora_id', $currentUser->consultora_id);
        }

        $pendingUsers = $query->orderBy('apellidos')->get();

        return response()->json($pendingUsers);
    }

    /**
     * Approve the proposed signature.
     */
    public function approve(User $user)
    {
        $this->ensureGestor();
        $this->ensureTenancy($user);

        if ($user->firma_status !== 'pending' || ! $user->firma_temp) {
            return back()->with('error', 'El usuario no tiene una firma pendiente de aprobación.');
        }

        $tempPath = $user->firma_temp;
        $extension = pathinfo($tempPath, PATHINFO_EXTENSION) ?: 'png';

        // Definir nueva ruta definitiva con nombre único aleatorio para preservar el historial forense
        $newFilename = 'signatures/user_sig_'.$user->id.'_'.Str::random(12).'.'.$extension;

        if (Storage::disk('public')->exists($tempPath)) {
            // Copiar físicamente el archivo al almacenamiento permanente
            Storage::disk('public')->copy($tempPath, $newFilename);

            // Eliminar el archivo temporal original
            Storage::disk('public')->delete($tempPath);
        }

        // Actualizar base de datos
        $user->update([
            'firma' => $newFilename,
            'firma_temp' => null,
            'firma_status' => 'approved',
        ]);

        return back()->with('message', "Firma de {$user->full_name} aprobada e inyectada con éxito.");
    }

    /**
     * Reject the proposed signature.
     */
    public function reject(User $user)
    {
        $this->ensureGestor();
        $this->ensureTenancy($user);

        if ($user->firma_status !== 'pending' || ! $user->firma_temp) {
            return back()->with('error', 'El usuario no tiene una firma pendiente de aprobación.');
        }

        $tempPath = $user->firma_temp;

        // Eliminar físicamente el archivo temporal del disco
        if (Storage::disk('public')->exists($tempPath)) {
            Storage::disk('public')->delete($tempPath);
        }

        // Actualizar base de datos
        $user->update([
            'firma_temp' => null,
            'firma_status' => 'rejected',
        ]);

        return back()->with('message', "Propuesta de firma de {$user->full_name} rechazada correctamente.");
    }

    /**
     * Helper to ensure the active user is a Gestor/Admin.
     */
    private function ensureGestor()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (! $user->isAdmin()) {
            abort(403, 'Acceso denegado. Se requieren privilegios de Gestor.');
        }
    }

    /**
     * Helper to ensure consultora tenancy alignment.
     */
    private function ensureTenancy(User $user)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        if (! $currentUser->isSuperAdmin() && $user->consultora_id !== $currentUser->consultora_id) {
            abort(403, 'Acceso denegado. Inconsistencia de consultora.');
        }
    }
}
