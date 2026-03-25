<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Controller;
use App\Models\Core\Lectivo;
use App\Models\Core\LectivoNivel;
use App\Models\Evaluacion\Periodo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeriodoController extends Controller
{
    /**
     * Lista los periodos de un lectivo.
     */
    public function index(Request $request): JsonResponse
    {
        $institucionId = $this->institucionId($request);

        $request->validate([
            'lectivo_id' => 'required|uuid',
        ]);

        $periodos = Periodo::where('institucion_id', $institucionId)
            ->where('lectivo_id', $request->lectivo_id)
            ->orderBy('numero')
            ->get();

        return response()->json(['status' => 'success', 'data' => $periodos]);
    }

    /**
     * Crea un periodo validando que no se superponga con otros del mismo lectivo
     * y que esté dentro de las fechas del lectivo.
     */
    public function store(Request $request): JsonResponse
    {
        $institucionId = $this->institucionId($request);

        $validated = $request->validate([
            'lectivo_id'   => 'required|uuid|exists:core.lectivos,id',
            'numero'       => 'required|integer|min:1|max:6',
            'nombre'       => 'required|string|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after:fecha_inicio',
            'peso'         => 'required|numeric|min:0|max:100',
        ]);

        $lectivo = Lectivo::where('id', $validated['lectivo_id'])
            ->where('institucion_id', $institucionId)
            ->firstOrFail();

        // Validar que las fechas estén dentro del rango del lectivo
        $errorFechas = $this->validarRangoLectivo($lectivo, $validated['fecha_inicio'], $validated['fecha_fin']);
        if ($errorFechas) {
            return response()->json(['status' => 'error', 'message' => $errorFechas], 422);
        }

        // Validar superposición con otros periodos del mismo lectivo
        $errorSuperposicion = $this->validarSuperposicion(
            $institucionId, $validated['lectivo_id'],
            $validated['fecha_inicio'], $validated['fecha_fin']
        );
        if ($errorSuperposicion) {
            return response()->json(['status' => 'error', 'message' => $errorSuperposicion], 422);
        }

        $periodo = Periodo::create([
            'institucion_id' => $institucionId,
            'lectivo_id'     => $validated['lectivo_id'],
            'numero'         => $validated['numero'],
            'nombre'         => $validated['nombre'],
            'fecha_inicio'   => $validated['fecha_inicio'],
            'fecha_fin'      => $validated['fecha_fin'],
            'peso'           => $validated['peso'],
            'es_activo'      => false,
            'estado'         => 'activo',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Periodo creado exitosamente.',
            'data'    => $periodo,
        ], 201);
    }

    public function show(Request $request, Periodo $periodo): JsonResponse
    {
        $this->authorize($request, $periodo);

        return response()->json(['status' => 'success', 'data' => $periodo->load('lectivo')]);
    }

    /**
     * Actualiza un periodo con las mismas validaciones de superposición.
     */
    public function update(Request $request, Periodo $periodo): JsonResponse
    {
        $this->authorize($request, $periodo);

        $validated = $request->validate([
            'numero'       => 'sometimes|integer|min:1|max:6',
            'nombre'       => 'sometimes|string|max:100',
            'fecha_inicio' => 'sometimes|date',
            'fecha_fin'    => 'sometimes|date|after:fecha_inicio',
            'peso'         => 'sometimes|numeric|min:0|max:100',
            'es_activo'    => 'sometimes|boolean',
            'estado'       => 'sometimes|in:activo,inactivo',
        ]);

        $fechaInicio = $validated['fecha_inicio'] ?? $periodo->fecha_inicio->toDateString();
        $fechaFin    = $validated['fecha_fin'] ?? $periodo->fecha_fin->toDateString();

        $lectivo = $periodo->lectivo;

        // Validar rango del lectivo si las fechas cambian
        if (isset($validated['fecha_inicio']) || isset($validated['fecha_fin'])) {
            $errorFechas = $this->validarRangoLectivo($lectivo, $fechaInicio, $fechaFin);
            if ($errorFechas) {
                return response()->json(['status' => 'error', 'message' => $errorFechas], 422);
            }

            $errorSuperposicion = $this->validarSuperposicion(
                $periodo->institucion_id, $periodo->lectivo_id,
                $fechaInicio, $fechaFin, $periodo->id
            );
            if ($errorSuperposicion) {
                return response()->json(['status' => 'error', 'message' => $errorSuperposicion], 422);
            }
        }

        $periodo->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Periodo actualizado.',
            'data'    => $periodo->fresh(),
        ]);
    }

    public function destroy(Request $request, Periodo $periodo): JsonResponse
    {
        $this->authorize($request, $periodo);

        if ($periodo->actividades()->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No se puede eliminar el periodo porque tiene actividades registradas.',
            ], 409);
        }

        $periodo->delete();

        return response()->json(['status' => 'success', 'message' => 'Periodo eliminado.']);
    }

    /**
     * Valida que las fechas del periodo estén dentro del rango del lectivo.
     */
    protected function validarRangoLectivo(Lectivo $lectivo, string $fechaInicio, string $fechaFin): ?string
    {
        if ($fechaInicio < $lectivo->fecha_inicio->toDateString()) {
            return "La fecha de inicio del periodo ({$fechaInicio}) no puede ser anterior al inicio del año lectivo ({$lectivo->fecha_inicio->toDateString()}).";
        }

        if ($fechaFin > $lectivo->fecha_fin->toDateString()) {
            return "La fecha de fin del periodo ({$fechaFin}) no puede superar la fecha de fin del año lectivo ({$lectivo->fecha_fin->toDateString()}).";
        }

        return null;
    }

    /**
     * Valida que no haya superposición de fechas con otros periodos del mismo lectivo.
     */
    protected function validarSuperposicion(
        string $institucionId,
        string $lectivoId,
        string $fechaInicio,
        string $fechaFin,
        ?string $excluirPeriodoId = null
    ): ?string {
        $query = Periodo::where('institucion_id', $institucionId)
            ->where('lectivo_id', $lectivoId)
            ->where('estado', 'activo')
            ->where(function ($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                  ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                  ->orWhere(function ($q2) use ($fechaInicio, $fechaFin) {
                      $q2->where('fecha_inicio', '<=', $fechaInicio)
                         ->where('fecha_fin', '>=', $fechaFin);
                  });
            });

        if ($excluirPeriodoId) {
            $query->where('id', '!=', $excluirPeriodoId);
        }

        $periodoSuperpuesto = $query->first();

        if ($periodoSuperpuesto) {
            return "El periodo se superpone con '{$periodoSuperpuesto->nombre}' ({$periodoSuperpuesto->fecha_inicio->toDateString()} - {$periodoSuperpuesto->fecha_fin->toDateString()}).";
        }

        return null;
    }

    protected function authorize(Request $request, Periodo $periodo): void
    {
        if ($periodo->institucion_id !== $this->institucionId($request)) {
            abort(403, 'El periodo no pertenece a su institución.');
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
