<?php

namespace App\Services;

use App\Models\Auth\Rol;
use App\Models\Auth\Usuario;
use App\Models\Core\Institucion;
use App\Models\Core\Perfil;
use Illuminate\Support\Facades\DB;

class InstitucionRolService
{
    /**
     * Cambia el representante legal (rector) de una institución.
     *
     * - Desactiva el perfil de rector del usuario anterior.
     * - Crea o activa el perfil de rector para el nuevo usuario.
     * - Actualiza rector_id en la institución.
     * - Garantiza que solo exista un rector activo por institución.
     */
    public function cambiarRector(Institucion $institucion, Usuario $nuevoRector): array
    {
        return $this->cambiarRolPrincipal($institucion, $nuevoRector, 'rector', 'rector_id');
    }

    /**
     * Cambia el administrador del sistema (manager) de una institución.
     *
     * - Desactiva el perfil de manager del usuario anterior.
     * - Crea o activa el perfil de manager para el nuevo usuario.
     * - Actualiza manager_id en la institución.
     * - Garantiza que solo exista un manager activo por institución.
     */
    public function cambiarManager(Institucion $institucion, Usuario $nuevoManager): array
    {
        return $this->cambiarRolPrincipal($institucion, $nuevoManager, 'manager', 'manager_id');
    }

    /**
     * Lógica genérica para cambiar un rol principal (rector o manager).
     */
    protected function cambiarRolPrincipal(
        Institucion $institucion,
        Usuario $nuevoUsuario,
        string $codigoRol,
        string $campoInstitucion
    ): array {
        return DB::transaction(function () use ($institucion, $nuevoUsuario, $codigoRol, $campoInstitucion) {
            $rol = Rol::where('codigo', $codigoRol)->firstOrFail();
            $anteriorId = $institucion->{$campoInstitucion};

            // 1. Desactivar TODOS los perfiles activos de este rol en esta institución
            Perfil::where('institucion_id', $institucion->id)
                ->where('rol_id', $rol->id)
                ->where('estado', 'activo')
                ->update(['estado' => 'inactivo']);

            // 2. Crear o reactivar el perfil del nuevo usuario
            $perfil = Perfil::updateOrCreate(
                [
                    'usuario_id'     => $nuevoUsuario->id,
                    'institucion_id' => $institucion->id,
                    'rol_id'         => $rol->id,
                ],
                [
                    'estado'      => 'activo',
                    'es_principal' => true,
                ]
            );

            // 3. Actualizar la referencia directa en la institución
            $institucion->update([$campoInstitucion => $nuevoUsuario->id]);

            return [
                'institucion'  => $institucion->fresh(),
                'perfil'       => $perfil,
                'anterior_id'  => $anteriorId,
                'nuevo_id'     => $nuevoUsuario->id,
            ];
        });
    }

    /**
     * Asigna rector y manager durante la configuración inicial de la institución.
     * Útil cuando se activa la cuenta por primera vez.
     */
    public function asignarRolesIniciales(
        Institucion $institucion,
        Usuario $rector,
        Usuario $manager
    ): array {
        return DB::transaction(function () use ($institucion, $rector, $manager) {
            $resultadoRector  = $this->cambiarRector($institucion, $rector);
            $resultadoManager = $this->cambiarManager($institucion, $manager);

            return [
                'rector'  => $resultadoRector,
                'manager' => $resultadoManager,
            ];
        });
    }
}
