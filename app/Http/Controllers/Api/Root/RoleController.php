<?php

namespace App\Http\Controllers\Api\Root;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of all roles, optionally with association status.
     */
    public function index(Request $request): JsonResponse
    {
        $institutionId = $request->query('institution_id');

        if ($institutionId) {
            $roles = Role::leftJoin('auth.institution_roles', function ($join) use ($institutionId) {
                $join->on('auth.roles.id', '=', 'auth.institution_roles.role_id')
                     ->where('auth.institution_roles.institution_id', '=', $institutionId);
            })
            ->select(
                'auth.roles.id',
                'auth.roles.slug',
                'auth.roles.name',
                'auth.roles.branding_colors',
                DB::raw('NULL as description'),
                'auth.institution_roles.id as association_id',
                DB::raw('COALESCE(auth.institution_roles.is_active, false) as is_enabled')
            )
            ->get();
        } else {
            $roles = Role::select(
                'id', 'slug', 'name', 'branding_colors',
                DB::raw('NULL as description')
            )->get();
        }

        return response()->json($roles);
    }
}
