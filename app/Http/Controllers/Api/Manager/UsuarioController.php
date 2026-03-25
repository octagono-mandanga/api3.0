<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Controller;
use App\Models\Auth\Rol;
use App\Models\Auth\Usuario;
use App\Models\Core\Perfil;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
    /**
     * Lista usuarios de la institución filtrados por código de rol.
     * GET /api/manager/usuarios?rol=academico
     */
    public function index(Request $request): JsonResponse
    {
        $institucionId = $this->institucionId($request);

        $request->validate([
            'rol' => 'required|string|in:academico,disciplina,secretaria,docente',
        ]);

        $codigoRol = $request->query('rol');

        $perfiles = Perfil::where('institucion_id', $institucionId)
            ->whereHas('rol', fn ($q) => $q->where('codigo', $codigoRol))
            ->with(['usuario:id,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido,email,telefono,estado', 'sede:id,nombre', 'rol:id,nombre,codigo'])
            ->orderBy('estado')
            ->get();

        return response()->json(['status' => 'success', 'data' => $perfiles]);
    }

    /**
     * Registra un usuario y le asigna un perfil con el rol indicado.
     * POST /api/manager/usuarios
     *
     * Roles permitidos: academico, disciplina, secretaria, docente.
     */
    public function store(Request $request): JsonResponse
    {
        $institucionId = $this->institucionId($request);

        $validated = $request->validate([
            'rol'               => 'required|string|in:academico,disciplina,secretaria,docente',
            'email'             => 'required|email|max:100',
            'primer_nombre'     => 'required|string|max:50',
            'segundo_nombre'    => 'nullable|string|max:50',
            'primer_apellido'   => 'required|string|max:50',
            'segundo_apellido'  => 'nullable|string|max:50',
            'tipo_documento_id' => 'nullable|integer|exists:ref.tipos_documento,id',
            'numero_documento'  => 'nullable|string|max:25',
            'telefono'          => 'nullable|string|max:20',
            'celular'           => 'nullable|string|max:20',
            'sede_id'           => 'nullable|uuid',
            'cargo'             => 'nullable|string|max:100',
        ]);

        $rol = Rol::where('codigo', $validated['rol'])->firstOrFail();

        $resultado = DB::transaction(function () use ($validated, $institucionId, $rol) {
            // Buscar o crear usuario
            $usuario = Usuario::where('email', $validated['email'])->first();
            $esNuevo = false;

            if (!$usuario) {
                $esNuevo = true;
                $usuario = Usuario::create([
                    'email'            => $validated['email'],
                    'primer_nombre'    => $validated['primer_nombre'],
                    'segundo_nombre'   => $validated['segundo_nombre'] ?? null,
                    'primer_apellido'  => $validated['primer_apellido'],
                    'segundo_apellido' => $validated['segundo_apellido'] ?? null,
                    'tipo_documento_id'=> $validated['tipo_documento_id'] ?? null,
                    'numero_documento' => $validated['numero_documento'] ?? null,
                    'telefono'         => $validated['telefono'] ?? null,
                    'celular'          => $validated['celular'] ?? null,
                    'password'         => Hash::make(Str::random(12)),
                    'estado'           => 'activo',
                ]);
            }

            // Verificar que no tenga ya este rol en la misma institución
            $perfilExistente = Perfil::where('usuario_id', $usuario->id)
                ->where('institucion_id', $institucionId)
                ->where('rol_id', $rol->id)
                ->first();

            if ($perfilExistente) {
                // Reactivar si estaba inactivo
                if ($perfilExistente->estado === 'inactivo') {
                    $perfilExistente->update(['estado' => 'activo']);
                }

                return [
                    'usuario' => $usuario,
                    'perfil'  => $perfilExistente->fresh(),
                    'es_nuevo' => false,
                    'reactivado' => $perfilExistente->wasChanged('estado'),
                ];
            }

            $perfil = Perfil::create([
                'usuario_id'     => $usuario->id,
                'institucion_id' => $institucionId,
                'sede_id'        => $validated['sede_id'] ?? null,
                'rol_id'         => $rol->id,
                'cargo'          => $validated['cargo'] ?? null,
                'es_principal'   => false,
                'estado'         => 'activo',
            ]);

            return [
                'usuario'  => $usuario,
                'perfil'   => $perfil,
                'es_nuevo' => $esNuevo,
                'reactivado' => false,
            ];
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Usuario registrado con rol ' . $validated['rol'] . '.',
            'data'    => [
                'usuario'    => $resultado['usuario'],
                'perfil'     => $resultado['perfil'],
                'es_nuevo'   => $resultado['es_nuevo'],
                'reactivado' => $resultado['reactivado'],
            ],
        ], 201);
    }

    /**
     * Muestra un perfil específico de la institución.
     */
    public function show(Request $request, Perfil $perfil): JsonResponse
    {
        $this->authorize($request, $perfil);

        return response()->json([
            'status' => 'success',
            'data'   => $perfil->load([
                'usuario:id,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido,email,telefono,celular,estado',
                'sede:id,nombre',
                'rol:id,nombre,codigo',
            ]),
        ]);
    }

    /**
     * Actualiza datos del perfil (sede, cargo, estado).
     * Para actualizar datos del usuario mismo, usar endpoint de perfil de usuario.
     */
    public function update(Request $request, Perfil $perfil): JsonResponse
    {
        $this->authorize($request, $perfil);

        $validated = $request->validate([
            'sede_id' => 'sometimes|nullable|uuid',
            'cargo'   => 'sometimes|nullable|string|max:100',
            'estado'  => 'sometimes|in:activo,inactivo',
        ]);

        $perfil->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Perfil actualizado.',
            'data'    => $perfil->fresh()->load(['usuario', 'sede', 'rol']),
        ]);
    }

    /**
     * Desactiva el perfil de un usuario (no elimina el usuario).
     */
    public function destroy(Request $request, Perfil $perfil): JsonResponse
    {
        $this->authorize($request, $perfil);

        $perfil->update(['estado' => 'inactivo']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Perfil desactivado.',
        ]);
    }

    protected function authorize(Request $request, Perfil $perfil): void
    {
        if ($perfil->institucion_id !== $this->institucionId($request)) {
            abort(403, 'El perfil no pertenece a su institución.');
        }
    }

    protected function institucionId(Request $request): string
    {
        return $request->user()
            ->perfiles()
            ->where('estado', 'activo')
            ->whereHas('rol', fn ($q) => $q->where('codigo', 'manager'))
            ->firstOrFail()
            ->institucion_id;
    }
}
