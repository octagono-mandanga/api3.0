<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\Usuario;
use App\Models\Auth\Rol;
use App\Models\Core\Institucion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Login del usuario - genera token y devuelve info con roles
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // 1. Buscar usuario por email
            $user = Usuario::where('email', $credentials['email'])
                           ->where('estado', 'activo')
                           ->first();

            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return response()->json(['message' => 'Credenciales inválidas'], 401);
            }

            // 2. Obtener roles del usuario a través de perfiles
            $perfiles = $user->perfiles()
                ->with(['rol', 'institucion:id,nombre_legal,nombre_corto'])
                ->where('estado', 'activo')
                ->get();

            $roles = $perfiles->map(function ($perfil) {
                return [
                    'perfil_id' => $perfil->id,
                    'rol_id' => $perfil->rol_id,
                    'codigo' => $perfil->rol->codigo,
                    'nombre' => $perfil->rol->nombre,
                    'institucion_id' => $perfil->institucion_id,
                    'institucion_nombre' => $perfil->institucion?->nombre_corto ?? $perfil->institucion?->nombre_legal ?? 'Sistema Global',
                    'es_principal' => $perfil->es_principal,
                ];
            });

            // 3. Determinar rol principal (el marcado como principal, o el primero)
            $rolPrincipal = $roles->firstWhere('es_principal', true) ?? $roles->first();

            // 4. Generar Token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'nombre_completo' => $user->nombre_completo,
                    'email' => $user->email,
                    'avatar_url' => $user->foto_url,
                ],
                'roles' => $roles,
                'rol_activo' => $rolPrincipal,
                'redirect_to' => $this->calculateRedirect($rolPrincipal['codigo'] ?? null),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Datos de entrada inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error Fatal en el Servidor',
                'details' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], 500);
        }
    }

    /**
     * Cerrar sesión - elimina token actual
     */
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

    /**
     * Obtener contexto del usuario autenticado
     */
    public function userContext(Request $request)
    {
        $user = $request->user();

        // Obtener perfiles/roles del usuario
        $perfiles = $user->perfiles()
            ->with(['rol', 'institucion:id,nombre_legal,nombre_corto,colores_marca'])
            ->where('estado', 'activo')
            ->get();

        $roles = $perfiles->map(function ($perfil) {
            return [
                'perfil_id' => $perfil->id,
                'rol_id' => $perfil->rol_id,
                'codigo' => $perfil->rol->codigo,
                'nombre' => $perfil->rol->nombre,
                'institucion_id' => $perfil->institucion_id,
                'institucion_nombre' => $perfil->institucion?->nombre_corto ?? $perfil->institucion?->nombre_legal ?? 'Sistema Global',
                'es_principal' => $perfil->es_principal,
            ];
        });

        // Determinar contexto activo
        $rolPrincipal = $roles->firstWhere('es_principal', true) ?? $roles->first();
        $perfilActivo = $perfiles->firstWhere('es_principal', true) ?? $perfiles->first();

        // Branding de la institución (si existe)
        $branding = $perfilActivo?->institucion?->colores_marca;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'full_name' => $user->nombre_completo,
                'avatar_url' => $user->foto_url,
                'email' => $user->email,
            ],
            'context' => [
                'institution_id' => $perfilActivo?->institucion_id,
                'institution_name' => $perfilActivo?->institucion?->nombre_legal ?? 'Sistema Global',
                'role_slug' => $rolPrincipal['codigo'] ?? 'no-role',
                'role_name' => $rolPrincipal['nombre'] ?? 'Sin Rol',
                'branding' => $branding,
            ],
            'roles' => $roles,
            'redirect_to' => $this->calculateRedirect($rolPrincipal['codigo'] ?? null)
        ]);
    }

    /**
     * Cambiar rol/contexto activo del usuario
     */
    public function switchRole(Request $request)
    {
        $request->validate([
            'perfil_id' => 'required|uuid',
        ]);

        $user = $request->user();

        // Verificar que el perfil pertenece al usuario
        $perfil = $user->perfiles()
            ->with(['rol', 'institucion'])
            ->where('id', $request->perfil_id)
            ->where('estado', 'activo')
            ->first();

        if (!$perfil) {
            return response()->json(['message' => 'Perfil no válido'], 403);
        }

        // Actualizar perfil principal
        $user->perfiles()->update(['es_principal' => false]);
        $perfil->update(['es_principal' => true]);

        return response()->json([
            'message' => 'Contexto cambiado exitosamente',
            'context' => [
                'institution_id' => $perfil->institucion_id,
                'institution_name' => $perfil->institucion?->nombre_legal ?? 'Sistema Global',
                'role_slug' => $perfil->rol->codigo,
                'role_name' => $perfil->rol->nombre,
                'branding' => $perfil->institucion?->colores_marca,
            ],
            'redirect_to' => $this->calculateRedirect($perfil->rol->codigo)
        ]);
    }

    /**
     * Lógica para determinar la ruta inicial según el rol
     */
    private function calculateRedirect(?string $codigo): string
    {
        return match ($codigo) {
            'root' => '/root',
            'manager' => '/manager',
            'rector' => '/rector',
            'academico' => '/academico',
            'disciplina' => '/disciplina',
            'docente' => '/docente',
            'alumno' => '/estudiante',
            'acudiente' => '/acudiente',
            'secretaria' => '/secretaria',
            default => '/auth/login',
        };
    }
}
