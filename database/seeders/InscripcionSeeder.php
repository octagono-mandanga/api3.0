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
    private $nombresM = ['Santiago', 'Sebastián', 'Mateo', 'Nicolás', 'Samuel', 'Alejandro', 'Daniel', 'David', 'Tomás', 'Emmanuel', 'Gabriel', 'Felipe', 'Joaquín', 'Lucas', 'Martín'];
    private $nombresF = ['Sofía', 'Valentina', 'Isabella', 'Camila', 'Mariana', 'Gabriela', 'Sara', 'Daniela', 'Luciana', 'Victoria', 'Emma', 'Paula', 'María José', 'Salomé', 'Antonella'];
    private $apellidos = ['García', 'Rodríguez', 'Martínez', 'López', 'González', 'Hernández', 'Pérez', 'Sánchez', 'Ramírez', 'Torres', 'Flores', 'Rivera', 'Gómez', 'Díaz', 'Reyes', 'Cruz', 'Morales', 'Ortiz'];

    public function run(): void
    {
        // Tipos de parentesco - GLOBALES (campos: nombre, estado)
        $tiposParentesco = [
            ['nombre' => 'Padre', 'estado' => 'activo'],
            ['nombre' => 'Madre', 'estado' => 'activo'],
            ['nombre' => 'Abuelo(a)', 'estado' => 'activo'],
            ['nombre' => 'Tío(a)', 'estado' => 'activo'],
            ['nombre' => 'Hermano(a)', 'estado' => 'activo'],
            ['nombre' => 'Tutor Legal', 'estado' => 'activo'],
            ['nombre' => 'Otro', 'estado' => 'activo'],
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

        // Obtener roles directamente
        $rolDocente = Rol::where('codigo', 'DOCENTE')->first();
        $rolEstudiante = Rol::where('codigo', 'ESTUDIANTE')->first();
        $rolAcudiente = Rol::where('codigo', 'ACUDIENTE')->first();

        // Configuración según tamaño de institución
        $esGrande = $sedes->count() > 1;
        $estudiantesPorCurso = $esGrande ? 30 : 25;
        $cursosSeccionPorGrado = $esGrande ? ['A', 'B'] : ['A'];

        // Crear docentes y obtener sus IDs de usuario
        $docentesUsuarioIds = $this->crearDocentes($institucion, $sedes, $rolDocente, $esGrande ? 20 : 10);

        // Obtener asignaturas de la institución
        $asignaturas = Asignatura::where('institucion_id', $institucion->id)
            ->where('estado', 'activo')
            ->get();

        // Para cada sede
        foreach ($sedes as $sede) {
            // Para cada grado de la institución
            foreach ($gradosInst as $gradoInst) {
                // Crear cursos (secciones A, B, etc.)
                foreach ($cursosSeccionPorGrado as $seccion) {
                    $directorId = $docentesUsuarioIds->isNotEmpty() ? $docentesUsuarioIds->random() : null;

                    // Campos Curso: institucion_id, sede_id, lectivo_id, grado_id, jornada_id, nombre, codigo, director_id, capacidad, aula, estado
                    $curso = Curso::create([
                        'institucion_id' => $institucion->id,
                        'sede_id' => $sede->id,
                        'lectivo_id' => $lectivo->id,
                        'grado_id' => $gradoInst->grado_id,
                        'jornada_id' => 1, // Mañana
                        'nombre' => "{$gradoInst->grado->nombre} {$seccion}",
                        'codigo' => "{$gradoInst->grado->codigo}-{$seccion}",
                        'director_id' => $directorId,
                        'capacidad' => $estudiantesPorCurso + 5,
                        'aula' => "Aula " . fake()->numberBetween(101, 305),
                        'estado' => 'activo',
                    ]);

                    // Asignar docentes a asignaturas del curso
                    // Campos DocenteAsignatura: usuario_id, asignatura_id, curso_id, lectivo_id, es_titular, estado
                    foreach ($asignaturas as $asignatura) {
                        $docenteId = $docentesUsuarioIds->isNotEmpty() ? $docentesUsuarioIds->random() : null;
                        if ($docenteId) {
                            DocenteAsignatura::create([
                                'usuario_id' => $docenteId,
                                'asignatura_id' => $asignatura->id,
                                'curso_id' => $curso->id,
                                'lectivo_id' => $lectivo->id,
                                'es_titular' => fake()->boolean(30),
                                'estado' => 'activo',
                            ]);
                        }
                    }

                    // Crear estudiantes con sus acudientes y matrículas
                    for ($i = 0; $i < $estudiantesPorCurso; $i++) {
                        $this->crearEstudianteConAcudiente(
                            $institucion,
                            $sede,
                            $gradoInst,
                            $curso,
                            $rolEstudiante,
                            $rolAcudiente
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
            $genero = fake()->randomElement(['M', 'F']);
            $nombres = $genero === 'M' ? $this->nombresM : $this->nombresF;

            $usuario = Usuario::create([
                'tipo_documento_id' => 1, // CC
                'numero_documento' => fake()->unique()->numerify('##########'),
                'primer_nombre' => fake()->randomElement($nombres),
                'segundo_nombre' => fake()->optional(0.5)?->randomElement($nombres),
                'primer_apellido' => fake()->randomElement($this->apellidos),
                'segundo_apellido' => fake()->randomElement($this->apellidos),
                'email' => fake()->unique()->safeEmail(),
                'email_verificado_en' => now(),
                'password' => Hash::make('docente123'),
                'celular' => fake()->numerify('3#########'),
                'direccion' => fake('es_ES')->streetAddress(),
                'fecha_nacimiento' => fake()->dateTimeBetween('-55 years', '-25 years')->format('Y-m-d'),
                'genero' => $genero,
                'municipio_id' => $institucion->municipio_id,
                'eps_id' => fake()->numberBetween(1, 10),
                'estado' => 'activo',
            ]);

            $sedeAleatoria = $sedes->random();

            // Campos Perfil: usuario_id, institucion_id, sede_id, rol_id, es_principal, estado
            Perfil::create([
                'usuario_id' => $usuario->id,
                'institucion_id' => $institucion->id,
                'sede_id' => $sedeAleatoria->id,
                'rol_id' => $rolDocente->id,
                'es_principal' => true,
                'estado' => 'activo',
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
        // Determinar edad según grado
        $edadBase = $this->calcularEdadPorGrado($gradoInst->grado_id);
        $genero = fake()->randomElement(['M', 'F']);
        $nombres = $genero === 'M' ? $this->nombresM : $this->nombresF;
        $tipoDoc = $edadBase < 7 ? 3 : ($edadBase < 18 ? 2 : 1); // RC, TI o CC

        // Crear usuario del estudiante
        $usuarioEst = Usuario::create([
            'tipo_documento_id' => $tipoDoc,
            'numero_documento' => fake()->unique()->numerify('##########'),
            'primer_nombre' => fake()->randomElement($nombres),
            'segundo_nombre' => fake()->optional(0.4)?->randomElement($nombres),
            'primer_apellido' => fake()->randomElement($this->apellidos),
            'segundo_apellido' => fake()->randomElement($this->apellidos),
            'email' => $edadBase >= 10 ? fake()->unique()->safeEmail() : null,
            'password' => $edadBase >= 10 ? Hash::make('estudiante123') : null,
            'celular' => $edadBase >= 12 ? fake()->optional(0.3)?->numerify('3#########') : null,
            'fecha_nacimiento' => fake()->dateTimeBetween("-{$edadBase} years - 11 months", "-{$edadBase} years")->format('Y-m-d'),
            'genero' => $genero,
            'municipio_id' => $institucion->municipio_id,
            'etnia_id' => fake()->optional(0.1)?->numberBetween(1, 6),
            'discapacidad_id' => fake()->optional(0.05)?->numberBetween(1, 8),
            'eps_id' => fake()->numberBetween(1, 10),
            'estado' => 'activo',
        ]);

        // Crear perfil de estudiante
        // Campos Perfil: usuario_id, institucion_id, sede_id, rol_id, es_principal, estado
        Perfil::create([
            'usuario_id' => $usuarioEst->id,
            'institucion_id' => $institucion->id,
            'sede_id' => $sede->id,
            'rol_id' => $rolEstudiante->id,
            'es_principal' => true,
            'estado' => 'activo',
        ]);

        // Crear registro de estudiante
        // Campos Estudiante: usuario_id, institucion_id, codigo_estudiante, fecha_ingreso, observaciones, estado
        $estudiante = Estudiante::create([
            'usuario_id' => $usuarioEst->id,
            'institucion_id' => $institucion->id,
            'codigo_estudiante' => fake()->unique()->numerify('EST-######'),
            'fecha_ingreso' => fake()->dateTimeBetween('-5 years', '-1 month')->format('Y-m-d'),
            'observaciones' => fake()->optional(0.2)?->sentence(),
            'estado' => 'activo',
        ]);

        // Crear acudiente principal
        $generoAcud = fake()->randomElement(['M', 'F']);
        $nombresAcud = $generoAcud === 'M' ? $this->nombresM : $this->nombresF;
        $parentescoId = fake()->randomElement([1, 2, 3, 4]); // Padre, Madre, Abuelo, Tío

        $usuarioAcud = Usuario::create([
            'tipo_documento_id' => 1, // CC
            'numero_documento' => fake()->unique()->numerify('##########'),
            'primer_nombre' => fake()->randomElement($nombresAcud),
            'segundo_nombre' => fake()->optional(0.3)?->randomElement($nombresAcud),
            'primer_apellido' => $usuarioEst->primer_apellido, // Mismo apellido del estudiante
            'segundo_apellido' => fake()->randomElement($this->apellidos),
            'email' => fake()->unique()->safeEmail(),
            'email_verificado_en' => now(),
            'password' => Hash::make('acudiente123'),
            'celular' => fake()->numerify('3#########'),
            'telefono' => fake()->optional(0.4)?->numerify('#######'),
            'direccion' => fake('es_ES')->streetAddress(),
            'fecha_nacimiento' => fake()->dateTimeBetween('-60 years', '-25 years')->format('Y-m-d'),
            'genero' => $generoAcud,
            'municipio_id' => $institucion->municipio_id,
            'eps_id' => fake()->numberBetween(1, 10),
            'estado' => 'activo',
        ]);

        // Crear perfil de acudiente
        Perfil::create([
            'usuario_id' => $usuarioAcud->id,
            'institucion_id' => $institucion->id,
            'sede_id' => $sede->id,
            'rol_id' => $rolAcudiente->id,
            'es_principal' => true,
            'estado' => 'activo',
        ]);

        // Crear relación acudiente-estudiante
        // Campos Acudiente: usuario_id, estudiante_id, parentesco_id, es_principal, autorizado_recoger, estado
        Acudiente::create([
            'usuario_id' => $usuarioAcud->id,
            'estudiante_id' => $estudiante->id,
            'parentesco_id' => $parentescoId,
            'es_principal' => true,
            'autorizado_recoger' => true,
            'estado' => 'activo',
        ]);

        // Crear matrícula
        // Campos Matricula: estudiante_id, curso_id, codigo_matricula, fecha_matricula, tipo, repitente, estado
        Matricula::create([
            'estudiante_id' => $estudiante->id,
            'curso_id' => $curso->id,
            'codigo_matricula' => fake()->unique()->numerify('MAT-2026-######'),
            'fecha_matricula' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'tipo' => fake()->randomElement(['nuevo', 'antiguo', 'transferencia']),
            'repitente' => fake()->boolean(5), // 5% repitentes
            'estado' => 'activo',
        ]);
    }

    private function calcularEdadPorGrado(int $gradoId): int
    {
        $edades = [
            1 => 4,   // Prejardín
            2 => 5,   // Jardín
            3 => 6,   // Transición
            4 => 7,   // Primero
            5 => 8,   // Segundo
            6 => 9,   // Tercero
            7 => 10,  // Cuarto
            8 => 11,  // Quinto
            9 => 12,  // Sexto
            10 => 13, // Séptimo
            11 => 14, // Octavo
            12 => 15, // Noveno
            13 => 16, // Décimo
            14 => 17, // Undécimo
        ];

        return $edades[$gradoId] ?? 10;
    }
}
