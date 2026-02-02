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
     * Store or update the association of a role with an institution.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'institution_id' => 'required|uuid|exists:auth.institutions,id',
            'role_id' => 'required|uuid|exists:auth.roles,id',
            'is_active' => 'required|boolean'
        ]);

        $institutionRole = InstitutionRole::updateOrCreate(
            [
                'institution_id' => $request->institution_id,
                'role_id' => $request->role_id,
            ],
            [
                'is_active' => $request->is_active
            ]
        );

        return response()->json([
            'message' => 'Rol de institución actualizado exitosamente.',
            'data' => $institutionRole
        ]);
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
