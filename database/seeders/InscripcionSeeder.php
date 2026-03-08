<?php

namespace Database\Seeders;

use App\Models\Core\Institucion;
use App\Models\Core\Sede;
use App\Models\Core\Lectivo;
use App\Models\Core\GradoInstitucion;
use App\Models\Core\Perfil;
use App\Models\Auth\Rol;
use App\Models\Auth\Usuario;
use App\Models\Academico\Asignatura;
use App\Models\Inscripcion\Estudiante;
use App\Models\Inscripcion\TipoParentesco;
use App\Models\Inscripcion\Acudiente;
use App\Models\Inscripcion\Curso;
use App\Models\Inscripcion\Matricula;
use App\Models\Inscripcion\DocenteAsignatura;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InscripcionSeeder extends Seeder
{
    // Modo desarrollo: menos registros para deploys rápidos
    const ESTUDIANTES_POR_CURSO = 5;

    private $nombresM = ['Santiago', 'Sebastián', 'Mateo', 'Nicolás', 'Samuel', 'Alejandro', 'Daniel', 'David', 'Tomás', 'Emmanuel', 'Gabriel', 'Felipe', 'Joaquín', 'Lucas', 'Martín'];
    private $nombresF = ['Sofía', 'Valentina', 'Isabella', 'Camila', 'Mariana', 'Gabriela', 'Sara', 'Daniela', 'Luciana', 'Victoria', 'Emma', 'Paula', 'María José', 'Salomé', 'Antonella'];
    private $apellidos = ['García', 'Rodríguez', 'Martínez', 'López', 'González', 'Hernández', 'Pérez', 'Sánchez', 'Ramírez', 'Torres', 'Flores', 'Rivera', 'Gómez', 'Díaz', 'Reyes', 'Cruz', 'Morales', 'Ortiz'];
    private $calles = ['Cra 5', 'Calle 12', 'Av 6', 'Cra 15', 'Diag 8', 'Transv 4', 'Calle 30'];
    private $docCounter = 1000000;

    public function run(): void
    {
        $tiposParentesco = [
            ['nombre' => 'Padre',       'estado' => 'activo'],
            ['nombre' => 'Madre',       'estado' => 'activo'],
            ['nombre' => 'Abuelo(a)',   'estado' => 'activo'],
            ['nombre' => 'Tío(a)',      'estado' => 'activo'],
            ['nombre' => 'Hermano(a)',  'estado' => 'activo'],
            ['nombre' => 'Tutor Legal', 'estado' => 'activo'],
            ['nombre' => 'Otro',        'estado' => 'activo'],
        ];

        foreach ($tiposParentesco as $tipo) {
            TipoParentesco::create($tipo);
        }

        $instituciones = Institucion::with('sedes')->get();

        foreach ($instituciones as $institucion) {
            $this->seedInstitucion($institucion);
        }
    }

    private function seedInstitucion(Institucion $institucion): void
    {
        $lectivo = Lectivo::where('institucion_id', $institucion->id)
            ->where('es_actual', true)
            ->first();

        if (!$lectivo) return;

        $sedes = $institucion->sedes;
        $gradosInst = GradoInstitucion::where('institucion_id', $institucion->id)
            ->where('estado', 'activo')
            ->with('grado')
            ->get();

        $rolDocente    = Rol::where('codigo', 'DOCENTE')->first();
        $rolEstudiante = Rol::where('codigo', 'ESTUDIANTE')->first();
        $rolAcudiente  = Rol::where('codigo', 'ACUDIENTE')->first();

        $esGrande              = $sedes->count() > 1;
        $cursosSeccionPorGrado = $esGrande ? ['A', 'B'] : ['A'];
        $cantidadDocentes      = $esGrande ? 8 : 4;

        $docentesUsuarioIds = $this->crearDocentes($institucion, $sedes, $rolDocente, $cantidadDocentes);

        $asignaturas = Asignatura::where('institucion_id', $institucion->id)
            ->where('estado', 'activo')
            ->get();

        foreach ($sedes as $sede) {
            foreach ($gradosInst as $gradoInst) {
                foreach ($cursosSeccionPorGrado as $seccion) {
                    $directorId = $docentesUsuarioIds->isNotEmpty() ? $docentesUsuarioIds->random() : null;
                    $codigoSede = strtoupper(substr($sede->codigo ?? $sede->nombre, 0, 3));

                    $curso = Curso::create([
                        'institucion_id' => $institucion->id,
                        'sede_id'        => $sede->id,
                        'lectivo_id'     => $lectivo->id,
                        'grado_id'       => $gradoInst->grado_id,
                        'jornada_id'     => 1,
                        'nombre'         => "{$gradoInst->grado->nombre} {$seccion}",
                        'codigo'         => "{$codigoSede}-{$gradoInst->grado->codigo}-{$seccion}",
                        'director_id'    => $directorId,
                        'capacidad'      => self::ESTUDIANTES_POR_CURSO + 5,
                        'aula'           => 'Aula ' . $this->rnd(101, 305),
                        'estado'         => 'activo',
                    ]);

                    foreach ($asignaturas as $asignatura) {
                        $docenteId = $docentesUsuarioIds->isNotEmpty() ? $docentesUsuarioIds->random() : null;
                        if ($docenteId) {
                            DocenteAsignatura::create([
                                'usuario_id'    => $docenteId,
                                'asignatura_id' => $asignatura->id,
                                'curso_id'      => $curso->id,
                                'lectivo_id'    => $lectivo->id,
                                'es_titular'    => ($this->rnd(0, 9) < 3), // 30%
                                'estado'        => 'activo',
                            ]);
                        }
                    }

                    for ($i = 0; $i < self::ESTUDIANTES_POR_CURSO; $i++) {
                        $this->crearEstudianteConAcudiente(
                            $institucion, $sede, $gradoInst, $curso,
                            $rolEstudiante, $rolAcudiente
                        );
                    }
                }
            }
        }
    }

    private function crearDocentes(Institucion $institucion, $sedes, Rol $rolDocente, int $cantidad): \Illuminate\Support\Collection
    {
        $usuarioIds = collect();

        for ($i = 0; $i < $cantidad; $i++) {
            $genero  = ($i % 2 === 0) ? 'M' : 'F';
            $nombres = $genero === 'M' ? $this->nombresM : $this->nombresF;

            $usuario = Usuario::create([
                'tipo_documento_id'  => 1,
                'numero_documento'   => $this->nextDoc(),
                'primer_nombre'      => $this->pick($nombres),
                'segundo_nombre'     => ($i % 3 === 0) ? $this->pick($nombres) : null,
                'primer_apellido'    => $this->pick($this->apellidos),
                'segundo_apellido'   => $this->pick($this->apellidos),
                'email'              => "docente{$this->docCounter}@test.edu.co",
                'email_verificado_en'=> now(),
                'password'           => Hash::make('docente123'),
                'celular'            => '3' . $this->rnd(100, 999) . $this->rnd(1000, 9999),
                'direccion'          => $this->pick($this->calles) . ' # ' . $this->rnd(1, 99),
                'fecha_nacimiento'   => now()->subYears($this->rnd(28, 55))->format('Y-m-d'),
                'genero'             => $genero,
                'municipio_id'       => $institucion->municipio_id,
                'eps_id'             => $this->rnd(1, 10),
                'estado'             => 'activo',
            ]);

            $sedeAleatoria = $sedes->random();

            Perfil::create([
                'usuario_id'    => $usuario->id,
                'institucion_id'=> $institucion->id,
                'sede_id'       => $sedeAleatoria->id,
                'rol_id'        => $rolDocente->id,
                'es_principal'  => true,
                'estado'        => 'activo',
            ]);

            $usuarioIds->push($usuario->id);
        }

        return $usuarioIds;
    }

    private function crearEstudianteConAcudiente(
        Institucion $institucion,
        Sede $sede,
        GradoInstitucion $gradoInst,
        Curso $curso,
        Rol $rolEstudiante,
        Rol $rolAcudiente
    ): void {
        $edadBase = $this->calcularEdadPorGrado($gradoInst->grado_id);
        $genero   = ($this->docCounter % 2 === 0) ? 'M' : 'F';
        $nombres  = $genero === 'M' ? $this->nombresM : $this->nombresF;
        $tipoDoc  = $edadBase < 7 ? 3 : ($edadBase < 18 ? 2 : 1);

        $usuarioEst = Usuario::create([
            'tipo_documento_id' => $tipoDoc,
            'numero_documento'  => $this->nextDoc(),
            'primer_nombre'     => $this->pick($nombres),
            'segundo_nombre'    => ($this->docCounter % 3 === 0) ? $this->pick($nombres) : null,
            'primer_apellido'   => $this->pick($this->apellidos),
            'segundo_apellido'  => $this->pick($this->apellidos),
            'email'             => $edadBase >= 10 ? "est{$this->docCounter}@test.com" : null,
            'password'          => $edadBase >= 10 ? Hash::make('estudiante123') : null,
            'celular'           => $edadBase >= 12 ? '3' . $this->rnd(100, 999) . $this->rnd(1000, 9999) : null,
            'fecha_nacimiento'  => now()->subYears($edadBase)->subMonths($this->rnd(0, 11))->format('Y-m-d'),
            'genero'            => $genero,
            'municipio_id'      => $institucion->municipio_id,
            'eps_id'            => $this->rnd(1, 10),
            'estado'            => 'activo',
        ]);

        Perfil::create([
            'usuario_id'    => $usuarioEst->id,
            'institucion_id'=> $institucion->id,
            'sede_id'       => $sede->id,
            'rol_id'        => $rolEstudiante->id,
            'es_principal'  => true,
            'estado'        => 'activo',
        ]);

        $estudiante = Estudiante::create([
            'usuario_id'        => $usuarioEst->id,
            'institucion_id'    => $institucion->id,
            'codigo_estudiante' => 'EST-' . $this->nextDoc(),
            'fecha_ingreso'     => now()->subMonths($this->rnd(1, 60))->format('Y-m-d'),
            'estado'            => 'activo',
        ]);

        // Acudiente
        $generoAcud  = ($this->docCounter % 2 === 0) ? 'M' : 'F';
        $nombresAcud = $generoAcud === 'M' ? $this->nombresM : $this->nombresF;
        $parentescoId = ($this->docCounter % 4) + 1;

        $usuarioAcud = Usuario::create([
            'tipo_documento_id'  => 1,
            'numero_documento'   => $this->nextDoc(),
            'primer_nombre'      => $this->pick($nombresAcud),
            'primer_apellido'    => $usuarioEst->primer_apellido,
            'segundo_apellido'   => $this->pick($this->apellidos),
            'email'              => "acud{$this->docCounter}@test.com",
            'email_verificado_en'=> now(),
            'password'           => Hash::make('acudiente123'),
            'celular'            => '3' . $this->rnd(100, 999) . $this->rnd(1000, 9999),
            'direccion'          => $this->pick($this->calles) . ' # ' . $this->rnd(1, 99),
            'fecha_nacimiento'   => now()->subYears($this->rnd(28, 55))->format('Y-m-d'),
            'genero'             => $generoAcud,
            'municipio_id'       => $institucion->municipio_id,
            'eps_id'             => $this->rnd(1, 10),
            'estado'             => 'activo',
        ]);

        Perfil::create([
            'usuario_id'    => $usuarioAcud->id,
            'institucion_id'=> $institucion->id,
            'sede_id'       => $sede->id,
            'rol_id'        => $rolAcudiente->id,
            'es_principal'  => true,
            'estado'        => 'activo',
        ]);

        Acudiente::create([
            'usuario_id'        => $usuarioAcud->id,
            'estudiante_id'     => $estudiante->id,
            'parentesco_id'     => $parentescoId,
            'es_principal'      => true,
            'autorizado_recoger'=> true,
            'estado'            => 'activo',
        ]);

        Matricula::create([
            'estudiante_id'    => $estudiante->id,
            'curso_id'         => $curso->id,
            'codigo_matricula' => 'MAT-2026-' . $this->nextDoc(),
            'fecha_matricula'  => now()->subMonths($this->rnd(0, 3))->format('Y-m-d'),
            'tipo'             => ['nuevo', 'antiguo', 'transferencia'][$this->docCounter % 3],
            'repitente'        => false,
            'estado'           => 'activo',
        ]);
    }

    // ── Helpers sin faker ──────────────────────────────────────────

    private function rnd(int $min, int $max): int
    {
        return rand($min, $max);
    }

    private function pick(array $array): mixed
    {
        return $array[array_rand($array)];
    }

    /** Contador incremental para documentos únicos garantizados */
    private function nextDoc(): string
    {
        return (string) ++$this->docCounter;
    }

    private function calcularEdadPorGrado(int $gradoId): int
    {
        $edades = [
            1 => 4,  2 => 5,  3 => 6,  4 => 7,  5 => 8,
            6 => 9,  7 => 10, 8 => 11, 9 => 12, 10 => 13,
            11 => 14, 12 => 15, 13 => 16, 14 => 17,
        ];

        return $edades[$gradoId] ?? 10;
    }
}
