<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Controller;
use App\Models\Evaluacion\EscalaCalificacion;
use App\Models\Evaluacion\RangoEscala;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EscalaCalificacionController extends Controller
{
    /**
     * Lista las escalas de calificación de la institución.
     */
    public function index(Request $request): JsonResponse
    {
        $institucionId = $this->institucionId($request);

        $escalas = EscalaCalificacion::where('institucion_id', $institucionId)
            ->with('rangos')
            ->get();

        return response()->json(['status' => 'success', 'data' => $escalas]);
    }

    /**
     * Crea una escala con sus rangos.
     */
    public function store(Request $request): JsonResponse
    {
        $institucionId = $this->institucionId($request);

        $validated = $request->validate([
            'nombre'           => 'required|string|max:100',
            'nota_minima'      => 'required|numeric|min:0',
            'nota_maxima'      => 'required|numeric|gt:nota_minima',
            'nota_aprobatoria' => 'required|numeric|min:0',
            'usa_decimales'    => 'sometimes|boolean',
            'decimales'        => 'sometimes|integer|min:0|max:2',
            'es_default'       => 'sometimes|boolean',
            'rangos'           => 'sometimes|array',
            'rangos.*.desempeno'   => 'required_with:rangos|string|max:50',
            'rangos.*.abreviatura' => 'nullable|string|max:5',
            'rangos.*.desde'       => 'required_with:rangos|numeric',
            'rangos.*.hasta'       => 'required_with:rangos|numeric',
            'rangos.*.color'       => 'nullable|string|max:7',
        ]);

        $escala = DB::transaction(function () use ($institucionId, $validated) {
            // Si es default, desmarcar las demás
            if (!empty($validated['es_default'])) {
                EscalaCalificacion::where('institucion_id', $institucionId)
                    ->update(['es_default' => false]);
            }

            $escala = EscalaCalificacion::create([
                'institucion_id'  => $institucionId,
                'nombre'          => $validated['nombre'],
                'nota_minima'     => $validated['nota_minima'],
                'nota_maxima'     => $validated['nota_maxima'],
                'nota_aprobatoria'=> $validated['nota_aprobatoria'],
                'usa_decimales'   => $validated['usa_decimales'] ?? true,
                'decimales'       => $validated['decimales'] ?? 1,
                'es_default'      => $validated['es_default'] ?? false,
                'estado'          => 'activo',
            ]);

            if (!empty($validated['rangos'])) {
                foreach ($validated['rangos'] as $rango) {
                    RangoEscala::create([
                        'escala_id'   => $escala->id,
                        'desempeno'   => $rango['desempeno'],
                        'abreviatura' => $rango['abreviatura'] ?? null,
                        'desde'       => $rango['desde'],
                        'hasta'       => $rango['hasta'],
                        'color'       => $rango['color'] ?? null,
                    ]);
                }
            }

            return $escala;
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Escala de calificación creada.',
            'data'    => $escala->load('rangos'),
        ], 201);
    }

    /**
     * Muestra una escala con sus rangos.
     */
    public function show(Request $request, EscalaCalificacion $escala): JsonResponse
    {
        $this->authorize($request, $escala);

        return response()->json([
            'status' => 'success',
            'data'   => $escala->load('rangos'),
        ]);
    }

    /**
     * Actualiza una escala y opcionalmente reemplaza sus rangos.
     */
    public function update(Request $request, EscalaCalificacion $escala): JsonResponse
    {
        $this->authorize($request, $escala);
        $institucionId = $this->institucionId($request);

        $validated = $request->validate([
            'nombre'           => 'sometimes|string|max:100',
            'nota_minima'      => 'sometimes|numeric|min:0',
            'nota_maxima'      => 'sometimes|numeric',
            'nota_aprobatoria' => 'sometimes|numeric|min:0',
            'usa_decimales'    => 'sometimes|boolean',
            'decimales'        => 'sometimes|integer|min:0|max:2',
            'es_default'       => 'sometimes|boolean',
            'estado'           => 'sometimes|in:activo,inactivo',
            'rangos'           => 'sometimes|array',
            'rangos.*.desempeno'   => 'required_with:rangos|string|max:50',
            'rangos.*.abreviatura' => 'nullable|string|max:5',
            'rangos.*.desde'       => 'required_with:rangos|numeric',
            'rangos.*.hasta'       => 'required_with:rangos|numeric',
            'rangos.*.color'       => 'nullable|string|max:7',
        ]);

        DB::transaction(function () use ($escala, $validated, $institucionId) {
            if (!empty($validated['es_default'])) {
                EscalaCalificacion::where('institucion_id', $institucionId)
                    ->where('id', '!=', $escala->id)
                    ->update(['es_default' => false]);
            }

            $escala->update(collect($validated)->except('rangos')->toArray());

            // Si se envían rangos, reemplazar todos
            if (isset($validated['rangos'])) {
                $escala->rangos()->delete();
                foreach ($validated['rangos'] as $rango) {
                    RangoEscala::create([
                        'escala_id'   => $escala->id,
                        'desempeno'   => $rango['desempeno'],
                        'abreviatura' => $rango['abreviatura'] ?? null,
                        'desde'       => $rango['desde'],
                        'hasta'       => $rango['hasta'],
                        'color'       => $rango['color'] ?? null,
                    ]);
                }
            }
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Escala actualizada.',
            'data'    => $escala->fresh()->load('rangos'),
        ]);
    }

    protected function authorize(Request $request, EscalaCalificacion $escala): void
    {
        if ($escala->institucion_id !== $this->institucionId($request)) {
            abort(403, 'La escala no pertenece a su institución.');
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
