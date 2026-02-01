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
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'institution_id' => 'required|uuid|exists:auth.institutions,id'
        ]);

        $institutionId = $request->institution_id;

        // Traer todos los roles y adjuntar el estado de la institución seleccionada
        $roles = Role::leftJoin('auth.institution_roles', function ($join) use ($institutionId) {
            $join->on('roles.id', '=', 'institution_roles.role_id')
                 ->where('institution_roles.institution_id', '=', $institutionId);
        })
        ->select(
            'auth.roles.*',
            'auth.institution_roles.id as association_id',
            DB::raw('COALESCE(auth.institution_roles.is_active, false) as is_enabled')
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
