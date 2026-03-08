<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Core\Institucion;
use App\Models\Auth\Usuario;
use App\Services\ConfiguracionInicialService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ConfiguracionInicialController extends Controller
{
    protected ConfiguracionInicialService $configuracionService;

    public function __construct(ConfiguracionInicialService $configuracionService)
    {
        $this->configuracionService = $configuracionService;
    }

    /**
     * Guarda la configuración inicial completa de una institución.
     * POST /api/instituciones/{id}/configuracion-inicial
     */
    public function store(Request $request, string $id): JsonResponse
    {
        try {
            $institucion = Institucion::findOrFail($id);

            // Validar datos de entrada
            $validated = $request->validate([
                // Institución
                'institucion' => 'nullable|array',
                'institucion.nombre_legal'   => 'nullable|string|max:200',
                'institucion.nombre_corto'   => 'nullable|string|max:100',
                'institucion.codigo_dane'    => 'nullable|string|max:20',
                'institucion.nit'            => 'nullable|string|max:20',
                'institucion.naturaleza'     => 'nullable|string|in:oficial,privado,concesion,cooperativo',
                'institucion.departamento'   => 'nullable|integer',   // ref.departamentos.id
                'institucion.municipio'      => 'nullable|integer',   // ref.municipios.id
                'institucion.direccion'      => 'nullable|string|max:255',
                'institucion.telefono'       => 'nullable|string|max:20',
                'institucion.email'          => 'nullable|email|max:100',
                'institucion.sitio_web'      => 'nullable|url|max:255',
                'institucion.color_primario' => 'nullable|string|max:7',
                'institucion.color_secundario' => 'nullable|string|max:7',

                // Sede principal
                'sede_principal' => 'nullable|array',
                'sede_principal.nombre'       => 'nullable|string|max:100',
                'sede_principal.codigo'       => 'nullable|string|max:20',
                'sede_principal.municipio_id' => 'nullable|integer',  // ref.municipios.id
                'sede_principal.direccion'    => 'nullable|string|max:255',
                'sede_principal.telefono'     => 'nullable|string|max:20',
                'sede_principal.es_principal' => 'nullable|boolean',
                'sede_principal.latitud'      => 'nullable|numeric|between:-90,90',
                'sede_principal.longitud'     => 'nullable|numeric|between:-180,180',

                // Sedes adicionales
                'sedes_adicionales' => 'nullable|array',
                'sedes_adicionales.*.nombre' => 'required_with:sedes_adicionales|string|max:100',
                'sedes_adicionales.*.codigo' => 'nullable|string|max:20',
                'sedes_adicionales.*.direccion' => 'nullable|string|max:255',
                'sedes_adicionales.*.telefono' => 'nullable|string|max:20',

                // Responsable
                'responsable' => 'nullable|array',
                'responsable.usuario' => 'required_with:responsable|array',
                'responsable.usuario.email' => 'required_with:responsable.usuario|email',
                'responsable.usuario.primer_nombre' => 'required_with:responsable.usuario|string|max:50',
                'responsable.usuario.segundo_nombre' => 'nullable|string|max:50',
                'responsable.usuario.primer_apellido' => 'required_with:responsable.usuario|string|max:50',
                'responsable.usuario.segundo_apellido' => 'nullable|string|max:50',
                'responsable.usuario.telefono' => 'nullable|string|max:20',
                'responsable.perfil' => 'required_with:responsable|array',
                'responsable.perfil.rol_id' => 'nullable|integer',
                'responsable.perfil.cargo' => 'required_with:responsable.perfil|string|max:100',
                'responsable.perfil.es_principal' => 'nullable|boolean',

                // Estructura académica
                'estructura_academica' => 'nullable|array',
                'estructura_academica.niveles' => 'nullable|array',
                'estructura_academica.niveles.*.nivel_id' => 'required|integer',
                'estructura_academica.niveles.*.grados' => 'nullable|array',
                'estructura_academica.niveles.*.grados.*.grado_id' => 'required|integer',
                'estructura_academica.niveles.*.grados.*.nombre' => 'nullable|string|max:50',

                // Lectivo y periodos
                'lectivo_periodos' => 'nullable|array',
                'lectivo_periodos.lectivo' => 'required_with:lectivo_periodos|array',
                'lectivo_periodos.lectivo.anio' => 'required_with:lectivo_periodos.lectivo|integer',
                'lectivo_periodos.lectivo.nombre' => 'required_with:lectivo_periodos.lectivo|string|max:100',
                'lectivo_periodos.lectivo.fecha_inicio' => 'required_with:lectivo_periodos.lectivo|date',
                'lectivo_periodos.lectivo.fecha_fin' => 'required_with:lectivo_periodos.lectivo|date|after:lectivo_periodos.lectivo.fecha_inicio',
                'lectivo_periodos.lectivo.es_actual' => 'nullable|boolean',
                'lectivo_periodos.numero_periodos' => 'nullable|integer|min:1|max:6',
                'lectivo_periodos.mismo_periodo_para_todos' => 'nullable|boolean',
                'lectivo_periodos.lectivos_nivel' => 'nullable|array',
                'lectivo_periodos.lectivos_nivel.*.nivel_id' => 'required|integer',
                'lectivo_periodos.lectivos_nivel.*.fecha_inicio' => 'required|date',
                'lectivo_periodos.lectivos_nivel.*.fecha_fin' => 'required|date',

                // Escala de calificación
                'escala_calificacion' => 'nullable|array',
                'escala_calificacion.escala' => 'required_with:escala_calificacion|array',
                'escala_calificacion.escala.nombre' => 'nullable|string|max:100',
                'escala_calificacion.escala.tipo' => 'nullable|string|in:numerica,conceptual,personalizada',
                'escala_calificacion.escala.valor_minimo' => 'nullable|numeric|min:0',
                'escala_calificacion.escala.valor_maximo' => 'nullable|numeric',
                'escala_calificacion.escala.minimo_aprobatorio' => 'nullable|numeric',
                'escala_calificacion.escala.aplica_todos_niveles' => 'nullable|boolean',
                'escala_calificacion.rangos' => 'nullable|array',
                'escala_calificacion.rangos.*.nombre' => 'required|string|max:50',
                'escala_calificacion.rangos.*.abreviatura' => 'nullable|string|max:5',
                'escala_calificacion.rangos.*.valor_minimo' => 'required|numeric',
                'escala_calificacion.rangos.*.valor_maximo' => 'required|numeric',
                'escala_calificacion.rangos.*.color' => 'nullable|string|max:7',

                // Áreas
                'areas_institucion' => 'nullable|array',
                'areas_institucion.areas' => 'nullable|array',
                'areas_institucion.areas.*.area_id' => 'required|integer',
                'areas_institucion.areas.*.nivel_id' => 'nullable|integer',
                'areas_institucion.areas_personalizadas' => 'nullable|array',
            ]);

            // Procesar configuración
            $resultados = $this->configuracionService->procesarConfiguracionInicial($institucion, $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Configuración inicial guardada exitosamente',
                'data' => [
                    'institucion_id' => $institucion->id,
                    'resumen' => [
                        'institucion_actualizada' => !is_null($resultados['institucion']),
                        'sedes_creadas' => count($resultados['sedes']),
                        'responsable_configurado' => !is_null($resultados['responsable']),
                        'niveles_configurados' => count($resultados['estructura']['niveles'] ?? []),
                        'grados_configurados' => count($resultados['estructura']['grados'] ?? []),
                        'lectivo_creado' => !is_null($resultados['lectivo']['lectivo'] ?? null),
                        'periodos_creados' => count($resultados['lectivo']['periodos'] ?? []),
                        'escala_configurada' => !is_null($resultados['escala']['escala'] ?? null),
                        'areas_configuradas' => count($resultados['areas']),
                    ],
                ],
            ], 201);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Institución no encontrada',
            ], 404);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error en configuración inicial', [
                'institucion_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Error al procesar la configuración inicial',
                'details' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor',
            ], 500);
        }
    }

    /**
     * Busca un usuario por correo electrónico.
     * GET /api/usuarios/buscar?email=correo@ejemplo.com
     */
    public function buscarUsuario(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario) {
            return response()->json([
                'encontrado' => false,
                'usuario' => null,
            ]);
        }

        return response()->json([
            'encontrado' => true,
            'usuario' => [
                'id' => $usuario->id,
                'email' => $usuario->email,
                'primer_nombre' => $usuario->primer_nombre,
                'segundo_nombre' => $usuario->segundo_nombre,
                'primer_apellido' => $usuario->primer_apellido,
                'segundo_apellido' => $usuario->segundo_apellido,
                'telefono' => $usuario->telefono ?? $usuario->celular,
            ],
        ]);
    }

    /**
     * Marca la configuración inicial como completada.
     * POST /api/instituciones/{id}/configuracion-completada
     */
    public function marcarCompletada(string $id): JsonResponse
    {
        try {
            $institucion = Institucion::findOrFail($id);
            $institucion->update(['estado' => 'activo']);

            return response()->json([
                'status' => 'success',
                'message' => 'Configuración marcada como completada',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Institución no encontrada',
            ], 404);
        }
    }
}
