<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSocialAccount;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class UserProfileService
{
    public function updateProfile(User $user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            // 1. Actualizar datos básicos en auth.users
            $user->update([
                'first_name'   => $data['first_name'] ?? $user->first_name,
                'middle_name'  => $data['middle_name'] ?? $user->middle_name,
                'last_name_1'  => $data['last_name_1'] ?? $user->last_name_1,
                'last_name_2'  => $data['last_name_2'] ?? $user->last_name_2,
                'avatar_url'   => $data['avatar_url'] ?? $user->avatar_url,
            ]);

            // 2. Actualizar o vincular cuenta social si se provee
            if (isset($data['social_provider'])) {
                UserSocialAccount::updateOrCreate(
                    ['user_id' => $user->id, 'provider' => $data['social_provider']],
                    [
                        'provider_user_id' => $data['social_id'],
                        'provider_email'   => $data['social_email'],
                        'linked_at'        => now()
                    ]
                );
            }

            return $user->load('socialAccounts');
        });
    }


public function updateAvatar(User $user, string $base64Image)
    {
        try {
            // 1. Limpieza de cabecera Base64
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image)) {
                $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            }

            $decoded = base64_decode($base64Image);
            
            // 2. Procesar imagen
            $img = Image::read($decoded);

            // 3. Redimensionar
            if ($img->width() > 128 || $img->height() > 128) {
                $img->scale(width: 128);
            }

            $timestamp = (int) (microtime(true) * 1000);
            $fileName = "{$user->id}-{$timestamp}.webp";
            $directory = "avatars/{$user->id}";
            $filePath = "{$directory}/{$fileName}";

            // Asegurar directorio
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Guardar
            Storage::disk('public')->put($filePath, $img->toWebp(80));

            // 4. LOGICA DE LIMPIEZA (Aquí estaba el error de nombre)
            $this->enforceAvatarLimit($user, $directory);

	    $publicUrl = Storage::url($filePath);

            // 5. Actualizar URL
           \App\Models\User::where('id', $user->id)->update([
		    'avatar_url' => $publicUrl
	   ]);

	    return $publicUrl;

        } catch (\Exception $e) {
            Log::error("Error en Avatar: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mantiene solo las últimas 20 imágenes del usuario
     */
    private function enforceAvatarLimit(User $user, string $directory)
    {
        $files = Storage::disk('public')->files($directory);
        
        if (count($files) > 20) {
            // Ordenamos por nombre (contiene el timestamp) y eliminamos las más viejas
            collect($files)
                ->sort()
                ->slice(0, count($files) - 20)
                ->each(function ($file) {
                    Storage::disk('public')->delete($file);
                });
        }
    }



}
