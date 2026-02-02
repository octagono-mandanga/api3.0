<?php

namespace App\Http\Controllers\Api\Root;

use App\Http\Controllers\Controller;
use App\Models\InstitutionRole;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstitutionRoleController extends Controller
{
    /**
     * Display a listing of roles for a specific institution with their status.
     */
    public function index(Request $request, $institutionId = null): JsonResponse
    {
        // Priorizar el ID de la ruta, luego el del query string
        $institutionId = $institutionId ?? $request->query('institution_id');

        if (!$institutionId) {
            return response()->json(['error' => 'El ID de la institución es requerido.'], 400);
        }

        // Traer SOLO los roles que la institución tiene habilitados exclusivamente
        $roles = Role::join('auth.institution_roles', function ($join) use ($institutionId) {
            $join->on('auth.roles.id', '=', 'auth.institution_roles.role_id')
                 ->where('auth.institution_roles.institution_id', '=', $institutionId)
                 ->where('auth.institution_roles.is_active', '=', true);
        })
        ->select(
            'auth.roles.*',
            'auth.institution_roles.id as association_id',
            DB::raw('true as is_enabled')
        )
        ->get();

        return response()->json($roles);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $response = InstitutionRole::with('role')->find($id);

            if (!$response) {
                return response()->json([
                    'status' => 'error',
                    'message' => "No se encontró la asociación con ID: $id"
                ], 404);
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al consultar el registro',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store or update the association of a role with an institution.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $isEnabled = $request->input('is_enabled') ?? $request->input('is_active');
            
            if (is_null($isEnabled)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El campo is_enabled o is_active es requerido.'
                ], 422);
            }

            $request->validate([
                'institution_id' => 'required|uuid|exists:App\Models\Institution,id',
                'role_id' => 'required|uuid|exists:App\Models\Role,id'
            ]);

            $institutionRole = InstitutionRole::updateOrCreate(
                [
                    'institution_id' => $request->institution_id,
                    'role_id' => $request->role_id,
                ],
                [
                    'is_active' => $isEnabled
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Rol de institución creado/actualizado exitosamente.',
                'data' => $institutionRole
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al procesar la solicitud',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the association status.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $isEnabled = $request->input('is_enabled') ?? $request->input('is_active');
            
            if (is_null($isEnabled)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El campo is_enabled o is_active es requerido.'
                ], 422);
            }

            $institutionRole = InstitutionRole::findOrFail($id);
            $institutionRole->update([
                'is_active' => $isEnabled
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Estado del rol actualizado correctamente.',
                'data' => $institutionRole
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el registro',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the association.
     */
    public function destroy($id): JsonResponse
    {
        $institutionRole = InstitutionRole::findOrFail($id);
        $institutionRole->delete();

        return response()->json([
            'message' => 'Asociación de rol eliminada exitosamente.'
        ]);
    }
}
