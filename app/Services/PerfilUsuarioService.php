<?php

namespace App\Services;

use App\Models\Usuario;
use App\Models\CuentaSocialUsuario;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class PerfilUsuarioService
{
    public function actualizarPerfil(Usuario $usuario, array $data)
    {
        return DB::transaction(function () use ($usuario, $data) {
            // 1. Actualizar datos básicos en auth.users
            $usuario->update([
                'first_name'   => $data['first_name'] ?? $usuario->first_name,
                'middle_name'  => $data['middle_name'] ?? $usuario->middle_name,
                'last_name_1'  => $data['last_name_1'] ?? $usuario->last_name_1,
                'last_name_2'  => $data['last_name_2'] ?? $usuario->last_name_2,
                'avatar_url'   => $data['avatar_url'] ?? $usuario->avatar_url,
            ]);

            // 2. Actualizar o vincular cuenta social si se provee
            if (isset($data['social_provider'])) {
                CuentaSocialUsuario::updateOrCreate(
                    ['user_id' => $usuario->id, 'provider' => $data['social_provider']],
                    [
                        'provider_user_id' => $data['social_id'],
                        'provider_email'   => $data['social_email'],
                        'linked_at'        => now()
                    ]
                );
            }

            return $usuario->load('cuentasSociales');
        });
    }


public function actualizarAvatar(Usuario $usuario, string $base64Image)
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
            $fileName = "{$usuario->id}-{$timestamp}.webp";
            $directory = "avatars/{$usuario->id}";
            $filePath = "{$directory}/{$fileName}";

            // Asegurar directorio
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Guardar
            Storage::disk('public')->put($filePath, $img->toWebp(80));

            // 4. LOGICA DE LIMPIEZA
            $this->enforceAvatarLimit($usuario, $directory);

	    $publicUrl = Storage::url($filePath);

            // 5. Actualizar URL
           \App\Models\Usuario::where('id', $usuario->id)->update([
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
    private function enforceAvatarLimit(Usuario $usuario, string $directory)
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
