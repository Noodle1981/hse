<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    public function updateSignature(User $user, string $sigData): array
    {
        if ($user->firma_status === 'pending') {
            return ['ok' => false, 'message' => 'Ya tienes una firma en revisión por el Gestor.'];
        }

        if (preg_match('/^data:image\/(\w+);base64,/', $sigData, $type)) {
            $sigData = substr($sigData, strpos($sigData, ',') + 1);
            $type = strtolower($type[1]);
        } else {
            $type = 'png';
        }

        $decoded = base64_decode($sigData);
        if ($decoded === false) {
            return ['ok' => false, 'message' => 'Formato de firma inválido.'];
        }

        $filename = 'signatures/temp_'.$user->id.'_'.time().'.'.$type;
        Storage::disk('public')->put($filename, $decoded);

        $user->update([
            'firma_temp' => $filename,
            'firma_status' => 'pending',
        ]);

        return ['ok' => true, 'message' => 'Nueva firma propuesta enviada a revisión por el Gestor.'];
    }

    public function updateProfile(User $user, array $data): void
    {
        $user->update($data);
    }

    public function updatePassword(User $user, string $newPassword): void
    {
        $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }

    public function updatePhoto(User $user, UploadedFile $file): bool
    {
        $path = $this->compressAndSaveImage($file);
        if (! $path) {
            return false;
        }

        if ($user->foto) {
            Storage::disk('public')->delete($user->foto);
        }

        $user->update(['foto' => $path]);

        return true;
    }

    private function compressAndSaveImage(UploadedFile $file): ?string
    {
        $maxSize = 400;
        $quality = 70;
        [$width, $height, $type] = getimagesize($file->getRealPath());

        $srcImage = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($file->getRealPath()),
            IMAGETYPE_PNG => @imagecreatefrompng($file->getRealPath()),
            IMAGETYPE_WEBP => @imagecreatefromwebp($file->getRealPath()),
            default => null,
        };

        if (! $srcImage) {
            return null;
        }

        $newWidth = $width;
        $newHeight = $height;

        if ($width > $maxSize || $height > $maxSize) {
            if ($width > $height) {
                $newWidth = $maxSize;
                $newHeight = (int) ($height * ($maxSize / $width));
            } else {
                $newHeight = $maxSize;
                $newWidth = (int) ($width * ($maxSize / $height));
            }
        }

        $dstImage = imagecreatetruecolor($newWidth, $newHeight);

        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
            imagefilledrectangle($dstImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $filename = 'profile_photos/'.uniqid('photo_').'.webp';
        $fullPath = storage_path('app/public/'.$filename);

        if (! file_exists(dirname($fullPath))) {
            @mkdir(dirname($fullPath), 0755, true);
        }

        @imagewebp($dstImage, $fullPath, $quality);
        imagedestroy($srcImage);
        imagedestroy($dstImage);

        return $filename;
    }
}
