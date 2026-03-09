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
    protected ResendService $resendService;

    public function __construct(ResendService $resendService)
    {
        $this->resendService = $resendService;
    }
    /**
     * Procesa la configuración inicial completa de una institución.
     * Este método maneja todo el proceso en una transacción.
     */
    public function procesarConfiguracionInicial(Institucion $institucion, array $datos): array
    {
        // Procesamos todo en transacción EXCEPTO el envío de email.
        // Si el email falla no debe revertir los datos ya guardados en BD.
        $resultados = DB::transaction(function () use ($institucion, $datos) {
            $resultados = [
                'institucion'  => null,
                'sedes'        => [],
                'responsable'  => null,
                'estructura'   => null,
                'lectivo'      => null,
                'escala'       => null,
                'areas'        => [],
                'email_payload'=> null, // datos para envío posterior al commit
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

            // 4. Configurar responsable (devuelve payload de email si es usuario nuevo)
            if (!empty($datos['responsable'])) {
                $resultados['responsable'] = $this->configurarResponsable($institucion, $datos['responsable']);
                // Guardar payload de email para enviar después del commit
                $resultados['email_payload'] = $resultados['responsable']['email_payload'] ?? null;
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

        // Enviar email FUERA de la transacción para que un fallo de correo
        // no revierta los datos ya guardados correctamente en la BD.
        if (!empty($resultados['email_payload'])) {
            $p = $resultados['email_payload'];
            $this->resendService->enviarBienvenidaAdministrador(
                $p['correo'],
                $p['nombre_completo'],
                $p['nombre_institucion'],
                $p['password_temporal'],
                $p['url_cliente_web']
            );
        }
        unset($resultados['email_payload']); // No exponer datos sensibles en la respuesta

        return $resultados;
    }

    /**
     * Actualiza los datos básicos de la institución.
     */
    protected function actualizarInstitucion(Institucion $institucion, array $datos): Institucion
    {
        $updateData = [
            'nombre_legal'  => $datos['nombre_legal'] ?? $institucion->nombre_legal,
            'nombre_corto'  => $datos['nombre_corto'] ?? $institucion->nombre_corto,
            'tipo_institucion' => $datos['naturaleza'] ?? $institucion->tipo_institucion,
            'municipio_id'  => $datos['municipio'] ?? $institucion->municipio_id,
            'direccion'     => $datos['direccion'] ?? $institucion->direccion,
            'telefono'      => $datos['telefono'] ?? $institucion->telefono,
            'email_oficial' => $datos['email'] ?? $institucion->email_oficial,
            'sitio_web'     => $datos['sitio_web'] ?? $institucion->sitio_web,
            'colores_marca' => [
                'primary'   => $datos['color_primario'] ?? '#1e40af',
                'secondary' => $datos['color_secundario'] ?? '#059669',
            ],
        ];

        // NIT: solo actualizar si es diferente y válido (no vacío, no placeholder)
        if (!empty($datos['nit']) && $datos['nit'] !== '1' && $datos['nit'] !== $institucion->nit) {
            // Verificar que no exista en otra institución
            $nitExiste = Institucion::where('nit', $datos['nit'])
                ->where('id', '!=', $institucion->id)
                ->exists();

            if (!$nitExiste) {
                $updateData['nit'] = $datos['nit'];
            }
        }

        // Código DANE: solo actualizar si es diferente y válido
        if (!empty($datos['codigo_dane']) && $datos['codigo_dane'] !== '1' && $datos['codigo_dane'] !== $institucion->codigo_dane) {
            $codigoExiste = Institucion::where('codigo_dane', $datos['codigo_dane'])
                ->where('id', '!=', $institucion->id)
                ->exists();

            if (!$codigoExiste) {
                $updateData['codigo_dane'] = $datos['codigo_dane'];
            }
        }

        $institucion->update($updateData);

        return $institucion->fresh();
    }

    /**
     * Crea una sede para la institución.
     */
    protected function crearSede(Institucion $institucion, array $datos): Sede
    {
        return Sede::create([
            'institucion_id' => $institucion->id,
            'nombre'         => $datos['nombre'],
            'codigo'         => $datos['codigo'] ?? Str::upper(Str::slug($datos['nombre'], '_')),
            'municipio_id'   => $datos['municipio_id'] ?? null,
            'direccion'      => $datos['direccion'] ?? null,
            'telefono'       => $datos['telefono'] ?? null,
            'latitud'        => $datos['latitud'] ?? null,
            'longitud'       => $datos['longitud'] ?? null,
            'es_principal'   => $datos['es_principal'] ?? false,
            'estado'         => 'activo',
        ]);
    }

    /**
     * Configura el responsable institucional.
     * Busca el usuario por email, si no existe lo crea.
     */
    protected function configurarResponsable(Institucion $institucion, array $datos): array
    {
        $usuarioData = $datos['usuario'];
        $perfilData  = $datos['perfil'];
        $emailPayload = null;

        // Buscar o crear usuario
        $usuario = Usuario::where('email', $usuarioData['email'])->first();

        if (!$usuario) {
            // Usuario nuevo: crear con contraseña temporal
            $passwordTemporal = Str::random(12);
            $usuario = Usuario::create([
                'email'           => $usuarioData['email'],
                'primer_nombre'   => $usuarioData['primer_nombre'],
                'segundo_nombre'  => $usuarioData['segundo_nombre'] ?? null,
                'primer_apellido' => $usuarioData['primer_apellido'],
                'segundo_apellido'=> $usuarioData['segundo_apellido'] ?? null,
                'telefono'        => $usuarioData['telefono'] ?? null,
                'password'        => Hash::make($passwordTemporal),
                'estado'          => 'pendiente',
            ]);

            // Preparar payload para envío de email DESPUÉS del commit de transacción
            $nombreCompleto = trim(implode(' ', array_filter([
                $usuarioData['primer_nombre'],
                $usuarioData['segundo_nombre'] ?? null,
                $usuarioData['primer_apellido'],
                $usuarioData['segundo_apellido'] ?? null,
            ])));
            $emailPayload = [
                'correo'           => $usuarioData['email'],
                'nombre_completo'  => $nombreCompleto,
                'nombre_institucion'=> $institucion->nombre_legal,
                'password_temporal' => $passwordTemporal,
                'url_cliente_web'  => config('app.cliente_web_url', 'http://localhost:4200'),
            ];
        } else {
            // Usuario existente: actualizar datos básicos si se proporcionaron
            $usuario->update([
                'primer_nombre'   => $usuarioData['primer_nombre'] ?? $usuario->primer_nombre,
                'segundo_nombre'  => $usuarioData['segundo_nombre'] ?? $usuario->segundo_nombre,
                'primer_apellido' => $usuarioData['primer_apellido'] ?? $usuario->primer_apellido,
                'segundo_apellido'=> $usuarioData['segundo_apellido'] ?? $usuario->segundo_apellido,
                'telefono'        => $usuarioData['telefono'] ?? $usuario->telefono,
            ]);
        }

        // Crear o actualizar perfil para el usuario en esta institución
        $perfil = Perfil::updateOrCreate(
            [
                'usuario_id'     => $usuario->id,
                'institucion_id' => $institucion->id,
            ],
            [
                'rol_id'      => $perfilData['rol_id'] ?? 2,
                'cargo'       => $perfilData['cargo'],
                'es_principal'=> $perfilData['es_principal'] ?? true,
                'estado'      => 'activo',
            ]
        );

        return [
            'usuario'      => $usuario,
            'perfil'       => $perfil,
            'es_nuevo'     => $usuario->wasRecentlyCreated,
            'email_payload'=> $emailPayload, // null si usuario ya existía
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
