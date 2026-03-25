<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Auth\Usuario;
use App\Models\Core\Institucion;
use App\Services\InstitucionRolService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstitucionRolController extends Controller
{
    protected InstitucionRolService $rolService;

    public function __construct(InstitucionRolService $rolService)
    {
        $this->rolService = $rolService;
    }

    /**
     * Cambia el representante legal (rector) de una institución.
     * PUT /api/instituciones/{institucion}/rector
     */
    public function cambiarRector(Request $request, Institucion $institucion): JsonResponse
    {
        $validated = $request->validate([
            'usuario_id' => 'required|uuid|exists:App\Models\Auth\Usuario,id',
        ]);

        $nuevoRector = Usuario::findOrFail($validated['usuario_id']);
        $resultado = $this->rolService->cambiarRector($institucion, $nuevoRector);

        return response()->json([
            'status'  => 'success',
            'message' => 'Representante legal actualizado exitosamente.',
            'data'    => [
                'rector_id'    => $resultado['nuevo_id'],
                'anterior_id'  => $resultado['anterior_id'],
                'perfil_id'    => $resultado['perfil']->id,
            ],
        ]);
    }

    /**
     * Cambia el administrador del sistema (manager) de una institución.
     * PUT /api/instituciones/{institucion}/manager
     */
    public function cambiarManager(Request $request, Institucion $institucion): JsonResponse
    {
        $validated = $request->validate([
            'usuario_id' => 'required|uuid|exists:App\Models\Auth\Usuario,id',
        ]);

        $nuevoManager = Usuario::findOrFail($validated['usuario_id']);
        $resultado = $this->rolService->cambiarManager($institucion, $nuevoManager);

        return response()->json([
            'status'  => 'success',
            'message' => 'Administrador del sistema actualizado exitosamente.',
            'data'    => [
                'manager_id'   => $resultado['nuevo_id'],
                'anterior_id'  => $resultado['anterior_id'],
                'perfil_id'    => $resultado['perfil']->id,
            ],
        ]);
    }

    /**
     * Obtiene el rector y manager actuales de una institución.
     * GET /api/instituciones/{institucion}/roles-principales
     */
    public function rolesPrincipales(Institucion $institucion): JsonResponse
    {
        $institucion->load(['rector', 'manager']);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'rector'  => $institucion->rector,
                'manager' => $institucion->manager,
            ],
        ]);
    }
}
