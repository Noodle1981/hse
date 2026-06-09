<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
                'n_doc' => $user->n_doc,
                'rol' => $user->rol,
                'firma' => $user->firma,
                'firma_temp' => $user->firma_temp,
                'firma_status' => $user->firma_status,
                'foto' => $user->foto,
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
            $filename = 'signatures/temp_'.$user->id.'_'.time().'.'.$type;
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

        if ($request->has('telefono')) {
            $request->merge(['telefono' => trim($request->telefono)]);
        }

        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'telefono' => ['nullable', 'string', 'max:50', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
        ], [
            'nombres.required' => 'El nombre es obligatorio.',
            'apellidos.required' => 'El apellido es obligatorio.',
            'telefono.unique' => 'Este teléfono ya está registrado por otro usuario.',
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
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('password_message', 'Contraseña actualizada exitosamente.');
    }

    /**
     * Update the user's profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|max:15360', // max 15MB to allow heavy mobile photos
        ], [
            'foto.required' => 'La foto es obligatoria.',
            'foto.image' => 'El archivo debe ser una imagen válida.',
            'foto.max' => 'La imagen no debe superar los 15 MB.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($request->hasFile('foto')) {
            $path = $this->compressAndSaveImage($request->file('foto'));

            if ($path) {
                // Delete old photo if exists
                if ($user->foto) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->foto);
                }

                $user->update([
                    'foto' => $path,
                ]);

                return back()->with('message', 'Foto de perfil actualizada correctamente.');
            }
        }

        return back()->with('error', 'Error al procesar la imagen.');
    }

    /**
     * Compress and save the profile photo.
     */
    private function compressAndSaveImage($file)
    {
        $maxSize = 400; // maximum width/height
        $quality = 70;  // compression quality

        // Get original dimensions and type
        list($width, $height, $type) = getimagesize($file->getRealPath());

        // Create image resource based on type
        switch ($type) {
            case IMAGETYPE_JPEG:
                $srcImage = @imagecreatefromjpeg($file->getRealPath());
                break;
            case IMAGETYPE_PNG:
                $srcImage = @imagecreatefrompng($file->getRealPath());
                break;
            case IMAGETYPE_WEBP:
                $srcImage = @imagecreatefromwebp($file->getRealPath());
                break;
            default:
                return null;
        }

        if (!$srcImage) {
            return null;
        }

        // Calculate new dimensions (proportional resize)
        $newWidth = $width;
        $newHeight = $height;

        if ($width > $maxSize || $height > $maxSize) {
            if ($width > $height) {
                $newWidth = $maxSize;
                $newHeight = (int)($height * ($maxSize / $width));
            } else {
                $newHeight = $maxSize;
                $newWidth = (int)($width * ($maxSize / $height));
            }
        }

        // Create canvas for the new image
        $dstImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG and WebP
        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
            imagefilledrectangle($dstImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Resize
        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Generate a unique file path
        $filename = 'profile_photos/' . uniqid('photo_') . '.webp';
        $fullPath = storage_path('app/public/' . $filename);

        // Create directories if they do not exist
        if (!file_exists(dirname($fullPath))) {
            @mkdir(dirname($fullPath), 0755, true);
        }

        // Save as WebP for optimal compression
        @imagewebp($dstImage, $fullPath, $quality);

        // Free memory
        imagedestroy($srcImage);
        imagedestroy($dstImage);

        return $filename;
    }
}
