<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Controller;
use App\Models\Core\Institucion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
                'municipio',
                'rector:id,primer_nombre,primer_apellido,email',
                'manager:id,primer_nombre,primer_apellido,email',
                'sedes',
            ]),
        ]);
    }

    /**
     * Actualiza información básica (no sensible) de la institución.
     */
    public function update(Request $request): JsonResponse
    {
        $institucion = $this->getInstitucion($request);

        $validated = $request->validate([
            'nombre_corto'     => 'sometimes|string|max:80',
            'direccion'        => 'sometimes|string|max:150',
            'telefono'         => 'sometimes|string|max:50',
            'email_oficial'    => 'sometimes|email|max:100',
            'sitio_web'        => 'sometimes|nullable|string|max:150',
            'colores_marca'    => 'sometimes|array',
        ]);

        $institucion->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Institución actualizada.',
            'data'    => $institucion->fresh(),
        ]);
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
