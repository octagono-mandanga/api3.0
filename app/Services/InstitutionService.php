<?php

namespace App\Services;

use App\Models\Institution;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Log;

class InstitutionService
{
    /**
     * Procesa y actualiza el escudo (logo) de una institución educativa.
     * Requerimiento: 256x256px, formato JPG.
     */
    public function updateInstitutionLogo(Institution $institution, string $base64Image): string
    {
        try {
            // 1. Limpieza de cabecera Base64 si está presente
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image)) {
                $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            }

            $decoded = base64_decode($base64Image);
            
            // 2. Lectura de la imagen con Intervention Image
            $img = Image::read($decoded);

            // 3. Redimensionamiento exacto a 256x256px (ajuste de cobertura)
            $img->cover(256, 256);

            // 4. Definición de rutas y nombre de archivo
            // Se usa el ID para que el logo sea único por institución
            $fileName = "logo-{$institution->id}.jpg";
            $directory = "institutions/logos";
            $filePath = "{$directory}/{$fileName}";

            // Asegurar la existencia del directorio en el disco public
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // 5. Guardado físico en formato JPG con calidad 90
            Storage::disk('public')->put($filePath, $img->toJpeg(90));

            // Generar la URL pública (requiere php artisan storage:link)
            $publicUrl = Storage::url($filePath);

            // 6. Persistencia en la base de datos (auth.institutions)
            // El campo logo_url ya fue verificado en el esquema
            $institution->update(['logo_url' => $publicUrl]);

            return $publicUrl;

        } catch (\Exception $e) {
            Log::error("Error procesando logo para institución {$institution->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mantenimiento de colores institucionales (Branding).
     */
    public function updateBranding(Institution $institution, array $colors): Institution
    {
        // Actualiza el campo JSONB branding_colors
        $institution->update([
            'branding_colors' => [
                'primary'   => $colors['primary'] ?? '#000000',
                'secondary' => $colors['secondary'] ?? '#ffffff',
                'tertiary'  => $colors['tertiary'] ?? '#cccccc',
            ]
        ]);

        return $institution;
    }
}
