<?php

namespace App\Services;

use App\Models\Core\Institucion;
use App\Models\Core\Sede;
use App\Models\Core\Perfil;
use App\Models\Core\Lectivo;
use App\Models\Core\LectivoNivel;
use App\Models\Core\NivelInstitucion;
use App\Models\Core\GradoInstitucion;
use App\Models\Auth\Usuario;
use App\Models\Academico\AreaInstitucion;
use App\Models\Evaluacion\EscalaCalificacion;
use App\Models\Evaluacion\RangoEscala;
use App\Models\Evaluacion\Periodo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ConfiguracionInicialService
{
    /**
     * Procesa la configuración inicial completa de una institución.
     * Este método maneja todo el proceso en una transacción.
     */
    public function procesarConfiguracionInicial(Institucion $institucion, array $datos): array
    {
        return DB::transaction(function () use ($institucion, $datos) {
            $resultados = [
                'institucion' => null,
                'sedes' => [],
                'responsable' => null,
                'estructura' => null,
                'lectivo' => null,
                'escala' => null,
                'areas' => [],
            ];

            // 1. Actualizar datos de la institución
            if (!empty($datos['institucion'])) {
                $resultados['institucion'] = $this->actualizarInstitucion($institucion, $datos['institucion']);
            }

            // 2. Crear sede principal si no existe
            if (!empty($datos['sede_principal'])) {
                $resultados['sedes'][] = $this->crearSede($institucion, $datos['sede_principal']);
            }

            // 3. Crear sedes adicionales
            if (!empty($datos['sedes_adicionales'])) {
                foreach ($datos['sedes_adicionales'] as $sedeData) {
                    $resultados['sedes'][] = $this->crearSede($institucion, $sedeData);
                }
            }

            // 4. Configurar responsable
            if (!empty($datos['responsable'])) {
                $resultados['responsable'] = $this->configurarResponsable($institucion, $datos['responsable']);
            }

            // 5. Configurar estructura académica (niveles y grados)
            if (!empty($datos['estructura_academica'])) {
                $resultados['estructura'] = $this->configurarEstructuraAcademica($institucion, $datos['estructura_academica']);
            }

            // 6. Configurar año lectivo y periodos
            if (!empty($datos['lectivo_periodos'])) {
                $resultados['lectivo'] = $this->configurarLectivoYPeriodos($institucion, $datos['lectivo_periodos']);
            }

            // 7. Configurar escala de calificación
            if (!empty($datos['escala_calificacion'])) {
                $resultados['escala'] = $this->configurarEscalaCalificacion($institucion, $datos['escala_calificacion']);
            }

            // 8. Configurar áreas curriculares
            if (!empty($datos['areas_institucion'])) {
                $resultados['areas'] = $this->configurarAreasInstitucion($institucion, $datos['areas_institucion']);
            }

            // 9. Marcar configuración como completada
            $institucion->update(['estado' => 'activo']);

            return $resultados;
        });
    }

    /**
     * Actualiza los datos básicos de la institución.
     */
    protected function actualizarInstitucion(Institucion $institucion, array $datos): Institucion
    {
        $institucion->update([
            'nombre_corto' => $datos['nombre_corto'] ?? $institucion->nombre_corto,
            'codigo_dane' => $datos['codigo_dane'] ?? $institucion->codigo_dane,
            'nit' => $datos['nit'] ?? $institucion->nit,
            'tipo_institucion' => $datos['naturaleza'] ?? $institucion->tipo_institucion,
            'direccion' => $datos['direccion'] ?? $institucion->direccion,
            'telefono' => $datos['telefono'] ?? $institucion->telefono,
            'email_oficial' => $datos['email'] ?? $institucion->email_oficial,
            'colores_marca' => [
                'primary' => $datos['color_primario'] ?? '#1e40af',
                'secondary' => $datos['color_secundario'] ?? '#059669',
            ],
        ]);

        return $institucion->fresh();
    }

    /**
     * Crea una sede para la institución.
     */
    protected function crearSede(Institucion $institucion, array $datos): Sede
    {
        return Sede::create([
            'institucion_id' => $institucion->id,
            'nombre' => $datos['nombre'],
            'codigo' => $datos['codigo'] ?? Str::upper(Str::slug($datos['nombre'], '_')),
            'direccion' => $datos['direccion'] ?? null,
            'telefono' => $datos['telefono'] ?? null,
            'es_principal' => $datos['es_principal'] ?? false,
            'estado' => 'activo',
        ]);
    }

    /**
     * Configura el responsable institucional.
     * Busca el usuario por email, si no existe lo crea.
     */
    protected function configurarResponsable(Institucion $institucion, array $datos): array
    {
        $usuarioData = $datos['usuario'];
        $perfilData = $datos['perfil'];

        // Buscar o crear usuario
        $usuario = Usuario::where('email', $usuarioData['email'])->first();

        if (!$usuario) {
            // Crear nuevo usuario con contraseña temporal
            $passwordTemporal = Str::random(12);
            $usuario = Usuario::create([
                'email' => $usuarioData['email'],
                'primer_nombre' => $usuarioData['primer_nombre'],
                'segundo_nombre' => $usuarioData['segundo_nombre'] ?? null,
                'primer_apellido' => $usuarioData['primer_apellido'],
                'segundo_apellido' => $usuarioData['segundo_apellido'] ?? null,
                'telefono' => $usuarioData['telefono'] ?? null,
                'password' => Hash::make($passwordTemporal),
                'estado' => 'pendiente', // Pendiente de activación
            ]);

            // TODO: Enviar email de invitación con contraseña temporal
        }

        // Crear perfil para el usuario en esta institución
        $perfil = Perfil::updateOrCreate(
            [
                'usuario_id' => $usuario->id,
                'institucion_id' => $institucion->id,
            ],
            [
                'rol_id' => $perfilData['rol_id'] ?? 2, // 2 = Coordinador/Responsable
                'cargo' => $perfilData['cargo'],
                'es_principal' => $perfilData['es_principal'] ?? true,
                'estado' => 'activo',
            ]
        );

        return [
            'usuario' => $usuario,
            'perfil' => $perfil,
            'es_nuevo' => !$usuario->wasRecentlyCreated,
        ];
    }

    /**
     * Configura la estructura académica (niveles y grados).
     */
    protected function configurarEstructuraAcademica(Institucion $institucion, array $datos): array
    {
        $nivelesCreados = [];
        $gradosCreados = [];

        foreach ($datos['niveles'] ?? [] as $nivelData) {
            // Crear o actualizar nivel de institución
            $nivelInstitucion = NivelInstitucion::updateOrCreate(
                [
                    'institucion_id' => $institucion->id,
                    'nivel_id' => $nivelData['nivel_id'],
                ],
                ['estado' => 'activo']
            );
            $nivelesCreados[] = $nivelInstitucion;

            // Crear grados para este nivel
            foreach ($nivelData['grados'] ?? [] as $gradoData) {
                $gradoInstitucion = GradoInstitucion::updateOrCreate(
                    [
                        'institucion_id' => $institucion->id,
                        'grado_id' => $gradoData['grado_id'],
                    ],
                    [
                        'alias' => $gradoData['nombre'] ?? null,
                        'estado' => 'activo',
                    ]
                );
                $gradosCreados[] = $gradoInstitucion;
            }
        }

        return [
            'niveles' => $nivelesCreados,
            'grados' => $gradosCreados,
        ];
    }

    /**
     * Configura el año lectivo y los periodos académicos.
     */
    protected function configurarLectivoYPeriodos(Institucion $institucion, array $datos): array
    {
        $lectivoData = $datos['lectivo'];

        // Crear año lectivo
        $lectivo = Lectivo::create([
            'institucion_id' => $institucion->id,
            'anio' => $lectivoData['anio'],
            'nombre' => $lectivoData['nombre'],
            'fecha_inicio' => $lectivoData['fecha_inicio'],
            'fecha_fin' => $lectivoData['fecha_fin'],
            'es_actual' => $lectivoData['es_actual'] ?? true,
            'estado' => 'activo',
        ]);

        // Si hay calendarios diferenciados por nivel
        $lectivosNivel = [];
        if (!($datos['mismo_periodo_para_todos'] ?? true) && !empty($datos['lectivos_nivel'])) {
            foreach ($datos['lectivos_nivel'] as $lectivoNivelData) {
                $lectivoNivel = LectivoNivel::create([
                    'lectivo_id' => $lectivo->id,
                    'nivel_id' => $lectivoNivelData['nivel_id'],
                    'fecha_inicio' => $lectivoNivelData['fecha_inicio'],
                    'fecha_fin' => $lectivoNivelData['fecha_fin'],
                    'estado' => 'activo',
                ]);
                $lectivosNivel[] = $lectivoNivel;
            }
        }

        // Crear periodos académicos
        $periodos = [];
        $numeroPeriodos = $datos['numero_periodos'] ?? 4;
        $pesoUniforme = round(100 / $numeroPeriodos, 2);

        // Calcular fechas de cada periodo
        $fechaInicio = new \DateTime($lectivoData['fecha_inicio']);
        $fechaFin = new \DateTime($lectivoData['fecha_fin']);
        $diasTotales = $fechaInicio->diff($fechaFin)->days;
        $diasPorPeriodo = floor($diasTotales / $numeroPeriodos);

        $nombresPeriodos = ['Primer', 'Segundo', 'Tercer', 'Cuarto'];

        for ($i = 1; $i <= $numeroPeriodos; $i++) {
            $inicioPeriodo = clone $fechaInicio;
            $inicioPeriodo->modify('+' . (($i - 1) * $diasPorPeriodo) . ' days');

            $finPeriodo = clone $fechaInicio;
            if ($i < $numeroPeriodos) {
                $finPeriodo->modify('+' . ($i * $diasPorPeriodo - 1) . ' days');
            } else {
                $finPeriodo = $fechaFin; // Último periodo hasta el final
            }

            $periodo = Periodo::create([
                'institucion_id' => $institucion->id,
                'lectivo_id' => $lectivo->id,
                'numero' => $i,
                'nombre' => ($nombresPeriodos[$i - 1] ?? "Periodo $i") . ' Periodo',
                'fecha_inicio' => $inicioPeriodo->format('Y-m-d'),
                'fecha_fin' => $finPeriodo->format('Y-m-d'),
                'peso' => $pesoUniforme,
                'es_activo' => $i === 1, // Solo el primer periodo activo
                'estado' => 'activo',
            ]);
            $periodos[] = $periodo;
        }

        return [
            'lectivo' => $lectivo,
            'lectivos_nivel' => $lectivosNivel,
            'periodos' => $periodos,
        ];
    }

    /**
     * Configura la escala de calificación y sus rangos.
     */
    protected function configurarEscalaCalificacion(Institucion $institucion, array $datos): array
    {
        $escalaData = $datos['escala'];

        // Crear escala de calificación
        $escala = EscalaCalificacion::create([
            'institucion_id' => $institucion->id,
            'nombre' => $escalaData['nombre'] ?? 'Escala Principal',
            'nota_minima' => $escalaData['valor_minimo'] ?? 1.0,
            'nota_maxima' => $escalaData['valor_maximo'] ?? 5.0,
            'nota_aprobatoria' => $escalaData['minimo_aprobatorio'] ?? 3.0,
            'usa_decimales' => true,
            'decimales' => 1,
            'es_default' => true,
            'estado' => 'activo',
        ]);

        // Crear rangos de la escala
        $rangosCreados = [];
        foreach ($datos['rangos'] ?? [] as $rangoData) {
            $rango = RangoEscala::create([
                'escala_id' => $escala->id,
                'desde' => $rangoData['valor_minimo'],
                'hasta' => $rangoData['valor_maximo'],
                'desempeno' => $rangoData['nombre'],
                'abreviatura' => $rangoData['abreviatura'] ?? substr($rangoData['nombre'], 0, 3),
                'color' => $rangoData['color'] ?? null,
            ]);
            $rangosCreados[] = $rango;
        }

        // Si no se especificaron rangos, crear los estándar colombianos
        if (empty($rangosCreados)) {
            $rangosDefault = [
                ['desde' => 1.0, 'hasta' => 2.9, 'desempeno' => 'Bajo', 'abreviatura' => 'DBJ', 'color' => '#ef4444'],
                ['desde' => 3.0, 'hasta' => 3.9, 'desempeno' => 'Básico', 'abreviatura' => 'DBS', 'color' => '#f59e0b'],
                ['desde' => 4.0, 'hasta' => 4.5, 'desempeno' => 'Alto', 'abreviatura' => 'DAL', 'color' => '#22c55e'],
                ['desde' => 4.6, 'hasta' => 5.0, 'desempeno' => 'Superior', 'abreviatura' => 'DSP', 'color' => '#3b82f6'],
            ];

            foreach ($rangosDefault as $rangoData) {
                $rango = RangoEscala::create([
                    'escala_id' => $escala->id,
                    ...$rangoData,
                ]);
                $rangosCreados[] = $rango;
            }
        }

        return [
            'escala' => $escala,
            'rangos' => $rangosCreados,
        ];
    }

    /**
     * Configura las áreas curriculares de la institución.
     */
    protected function configurarAreasInstitucion(Institucion $institucion, array $datos): array
    {
        $areasCreadas = [];

        // Áreas estándar por nivel
        foreach ($datos['areas'] ?? [] as $areaData) {
            $area = AreaInstitucion::updateOrCreate(
                [
                    'institucion_id' => $institucion->id,
                    'area_id' => $areaData['area_id'],
                    'nivel_id' => $areaData['nivel_id'] ?? null,
                ],
                ['estado' => 'activo']
            );
            $areasCreadas[] = $area;
        }

        // TODO: Manejar áreas personalizadas si se implementa esa funcionalidad

        return $areasCreadas;
    }
}
