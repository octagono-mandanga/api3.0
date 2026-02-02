<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
	    public function login(Request $request)
	    {
	        try {
	            $credentials = $request->validate([
	                'email' => 'required|email',
	                'password' => 'required',
	            ]);

	            // 1. Validar Usuario y Password
	            $user = User::where('email', $credentials['email'])
//	                        ->where('global_status', 'ACTIVE')
	                        ->first();

	            if (!$user || !Hash::check($credentials['password'], $user->password_hash)) {
	                return response()->json(['message' => 'Credenciales inválidas'], 401);
	            }
/*
	            XXX - xxx - xxx
	            $institution = config('app.current_institution');
	            if (!$institution) {
	                return response()->json(['message' => 'Institución no detectada'], 400);
	            }
	            
	            $hasRole = $user->institutionRoles()
	                ->where('institution_id', $institution->id)
	                ->where('assignment_status', 'ACTIVE')
	                ->exists();

	            if (!$hasRole) {
	                return response()->json(['message' => 'Sin permisos en esta institución'], 403);
	            }
*/
	            // 4. Generar Token
	            $token = $user->createToken('auth_token')->plainTextToken;

	            return response()->json([
	                'access_token' => $token,
	                'user' => [
	                    'name' => "{$user->first_name} {$user->last_name_1}",
	                    'email' => $user->email
	                ]
	            ]);

	        } catch (\Exception $e) {	            
	            return response()->json([
	                'error' => 'Error Fatal en el Servidor',
	                'details' => $e->getMessage()
	            ], 500);
	        }
	    }


public function logout(Request $request)
{
    try {        
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Sesión cerrada satisfactoriamente'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Ocurrió un error al intentar cerrar la sesión'
        ], 500);
    }
}

public function userContext(Request $request)
{
    $user = $request->user();
    
    // 1. Intentar obtener la institución desde la config global (Middleware)
    $institution = config('app.current_institution');

    // 2. Si es null, intentar identificarla manualmente por el Host como "Plan B"
    if (!$institution) {
        $host = $request->getHost();
        $institution = \App\Models\Institution::where('website_url', $host)->first();
    }

    // 3. Si sigue siendo null, intentar ver si es un usuario ROOT (sin institución)
    if (!$institution) {
        $rootRole = $user->institutionRoles()
            ->whereNull('institution_id')
            ->whereHas('role', function ($q) {
                $q->where('slug', 'root');
            })
            ->with('role')
            ->first();

        if ($rootRole) {
            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'full_name' => "{$user->first_name} {$user->last_name_1}",
                    'avatar_url' => $user->avatar_url,
                    'email' => $user->email,
                ],
                'context' => [
                    'institution_name' => 'Root System Console',
                    'role_slug' => 'root',
                    'branding' => null,
                ],
                'redirect_to' => $this->calculateRedirect('root')
            ]);
        }

        return response()->json([
            'error' => 'No se pudo identificar la institución para el host: ' . $request->getHost()
        ], 400);
    }

    // 4. Buscar el rol (normal para otros usuarios)
    $activeRole = $user->institutionRoles()
        ->where('institution_id', $institution->id)
        ->where('assignment_status', 'ACTIVE')
        ->with('role')
        ->first();

    return response()->json([
        'user' => [
            'id' => $user->id,
            'full_name' => "{$user->first_name} {$user->last_name_1}",
            'avatar_url' => $user->avatar_url,
            'email' => $user->email,
        ],
        'context' => [
            'institution_name' => $institution->legal_name ?? $institution->short_name,
            'role_slug' => $activeRole->role->slug ?? 'no-role',
            'branding' => $institution->branding_colors,
        ],
        'redirect_to' => $this->calculateRedirect($activeRole->role->slug ?? null)
    ]);
}

/**
 * Lógica simple para ayudar a Angular a decidir la ruta inicial 
 */
private function calculateRedirect(?string $slug): string
{
    return match ($slug) {
        'root'  => '/admin/dashboard',
        'admin' => '/school/dashboard',
        default => '/login',
    };
}

}
