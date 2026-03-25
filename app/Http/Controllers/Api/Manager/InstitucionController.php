<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Controller;
use App\Models\Core\Institucion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InstitucionController extends Controller
{
    /**
     * Obtiene la institución del manager autenticado.
     */
    public function show(Request $request): JsonResponse
    {
        $institucion = $this->getInstitucion($request);

        return response()->json([
            'status' => 'success',
            'data'   => $institucion->load([
                'plan:id,nombre,codigo',
                'municipio.departamento:id,nombre',
                'rector:id,primer_nombre,primer_apellido,email',
                'manager:id,primer_nombre,primer_apellido,email',
                'sedes',
            ]),
        ]);
    }

    /**
     * Actualiza información editable de la institución.
     */
    public function update(Request $request): JsonResponse
    {
        $institucion = $this->getInstitucion($request);

        $validated = $request->validate([
            'nombre_corto'              => 'sometimes|nullable|string|max:80',
            'nit'                       => 'sometimes|string|max:20',
            'codigo_dane'               => 'sometimes|nullable|string|max:20',
            'tipo_institucion'          => 'sometimes|nullable|string|max:50',
            'municipio_id'              => 'sometimes|nullable|integer|exists:municipios,id',
            'direccion'                 => 'sometimes|nullable|string|max:150',
            'telefono'                  => 'sometimes|nullable|string|max:50',
            'sitio_web'                 => 'sometimes|nullable|string|max:150',
            'colores_marca'             => 'sometimes|nullable|array',
            'colores_marca.primario'    => 'required_with:colores_marca|string|max:20',
            'colores_marca.secundario'  => 'nullable|string|max:20',
            'colores_marca.terciario'   => 'nullable|string|max:20',
        ]);

        $institucion->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Institución actualizada.',
            'data'    => $institucion->fresh()->load([
                'plan:id,nombre,codigo',
                'municipio.departamento:id,nombre',
                'rector:id,primer_nombre,primer_apellido,email',
                'manager:id,primer_nombre,primer_apellido,email',
            ]),
        ]);
    }

    /**
     * Sube o reemplaza el logo de la institución.
     * POST /manager/institucion/logo
     */
    public function uploadLogo(Request $request): JsonResponse
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $institucion = $this->getInstitucion($request);
        $url = $this->storeImage($request->file('logo'), $institucion, 'logos', 'logo_url');

        return response()->json([
            'status'  => 'success',
            'message' => 'Logo actualizado.',
            'data'    => ['logo_url' => $url],
        ]);
    }

    /**
     * Sube o reemplaza la portada de la institución.
     * POST /manager/institucion/portada
     */
    public function uploadPortada(Request $request): JsonResponse
    {
        $request->validate([
            'portada' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $institucion = $this->getInstitucion($request);
        $url = $this->storeImage($request->file('portada'), $institucion, 'portadas', 'portada_url');

        return response()->json([
            'status'  => 'success',
            'message' => 'Portada actualizada.',
            'data'    => ['portada_url' => $url],
        ]);
    }

    /**
     * Almacena una imagen en storage y actualiza el campo correspondiente.
     */
    private function storeImage($file, Institucion $institucion, string $folder, string $field): string
    {
        // Eliminar archivo anterior si existe
        $oldUrl = $institucion->{$field};
        if ($oldUrl) {
            $oldPath = str_replace('/storage/', '', parse_url($oldUrl, PHP_URL_PATH) ?? '');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $ext = $file->getClientOriginalExtension();
        $fileName = Str::uuid() . '.' . $ext;
        $path = "instituciones/{$folder}/{$fileName}";

        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

        $publicUrl = Storage::url($path);
        $institucion->update([$field => $publicUrl]);

        return $publicUrl;
    }

    /**
     * Resuelve la institución del usuario autenticado a partir de su perfil activo.
     */
    protected function getInstitucion(Request $request): Institucion
    {
        $perfil = $request->user()
            ->perfiles()
            ->where('estado', 'activo')
            ->whereHas('rol', fn ($q) => $q->where('codigo', 'manager'))
            ->firstOrFail();

        return Institucion::findOrFail($perfil->institucion_id);
    }
}
