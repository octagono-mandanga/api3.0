<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Controller;
use App\Models\Core\Lectivo;
use App\Models\Core\LectivoNivel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LectivoController extends Controller
{
    /**
     * Lista los años lectivos de la institución.
     */
    public function index(Request $request): JsonResponse
    {
        $institucionId = $this->institucionId($request);

        $lectivos = Lectivo::where('institucion_id', $institucionId)
            ->with('lectivosNivel.nivel')
            ->orderByDesc('anio')
            ->get();

        return response()->json(['status' => 'success', 'data' => $lectivos]);
    }

    /**
     * Crea un año lectivo con sus calendarios por nivel.
     */
    public function store(Request $request): JsonResponse
    {
        $institucionId = $this->institucionId($request);

        $validated = $request->validate([
            'anio'         => 'required|integer|min:2020|max:2050',
            'nombre'       => 'required|string|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after:fecha_inicio',
            'es_actual'    => 'sometimes|boolean',
            'lectivos_nivel'              => 'sometimes|array',
            'lectivos_nivel.*.nivel_id'   => 'required_with:lectivos_nivel|integer|exists:core.niveles_educativos,id',
            'lectivos_nivel.*.fecha_inicio' => 'required_with:lectivos_nivel|date',
            'lectivos_nivel.*.fecha_fin'    => 'required_with:lectivos_nivel|date|after:lectivos_nivel.*.fecha_inicio',
        ]);

        $lectivo = DB::transaction(function () use ($institucionId, $validated) {
            // Si es_actual, desmarcar los demás lectivos de la institución
            if (!empty($validated['es_actual'])) {
                Lectivo::where('institucion_id', $institucionId)
                    ->update(['es_actual' => false]);
            }

            $lectivo = Lectivo::create([
                'institucion_id' => $institucionId,
                'anio'           => $validated['anio'],
                'nombre'         => $validated['nombre'],
                'fecha_inicio'   => $validated['fecha_inicio'],
                'fecha_fin'      => $validated['fecha_fin'],
                'es_actual'      => $validated['es_actual'] ?? false,
                'estado'         => 'activo',
            ]);

            if (!empty($validated['lectivos_nivel'])) {
                foreach ($validated['lectivos_nivel'] as $ln) {
                    LectivoNivel::create([
                        'lectivo_id'   => $lectivo->id,
                        'nivel_id'     => $ln['nivel_id'],
                        'fecha_inicio' => $ln['fecha_inicio'],
                        'fecha_fin'    => $ln['fecha_fin'],
                        'estado'       => 'activo',
                    ]);
                }
            }

            return $lectivo;
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Año lectivo creado.',
            'data'    => $lectivo->load('lectivosNivel.nivel'),
        ], 201);
    }

    public function show(Request $request, Lectivo $lectivo): JsonResponse
    {
        $this->authorize($request, $lectivo);

        return response()->json([
            'status' => 'success',
            'data'   => $lectivo->load(['lectivosNivel.nivel', 'periodos']),
        ]);
    }

    /**
     * Actualiza un año lectivo.
     */
    public function update(Request $request, Lectivo $lectivo): JsonResponse
    {
        $this->authorize($request, $lectivo);
        $institucionId = $this->institucionId($request);

        $validated = $request->validate([
            'nombre'       => 'sometimes|string|max:100',
            'fecha_inicio' => 'sometimes|date',
            'fecha_fin'    => 'sometimes|date|after:fecha_inicio',
            'es_actual'    => 'sometimes|boolean',
            'estado'       => 'sometimes|in:activo,inactivo',
            'lectivos_nivel'              => 'sometimes|array',
            'lectivos_nivel.*.nivel_id'   => 'required_with:lectivos_nivel|integer|exists:core.niveles_educativos,id',
            'lectivos_nivel.*.fecha_inicio' => 'required_with:lectivos_nivel|date',
            'lectivos_nivel.*.fecha_fin'    => 'required_with:lectivos_nivel|date',
        ]);

        DB::transaction(function () use ($lectivo, $validated, $institucionId) {
            if (!empty($validated['es_actual'])) {
                Lectivo::where('institucion_id', $institucionId)
                    ->where('id', '!=', $lectivo->id)
                    ->update(['es_actual' => false]);
            }

            $lectivo->update(collect($validated)->except('lectivos_nivel')->toArray());

            // Si se envían lectivos_nivel, reemplazar
            if (isset($validated['lectivos_nivel'])) {
                $lectivo->lectivosNivel()->delete();
                foreach ($validated['lectivos_nivel'] as $ln) {
                    LectivoNivel::create([
                        'lectivo_id'   => $lectivo->id,
                        'nivel_id'     => $ln['nivel_id'],
                        'fecha_inicio' => $ln['fecha_inicio'],
                        'fecha_fin'    => $ln['fecha_fin'],
                        'estado'       => 'activo',
                    ]);
                }
            }
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Año lectivo actualizado.',
            'data'    => $lectivo->fresh()->load('lectivosNivel.nivel'),
        ]);
    }

    public function destroy(Request $request, Lectivo $lectivo): JsonResponse
    {
        $this->authorize($request, $lectivo);

        if ($lectivo->cursos()->exists() || $lectivo->periodos()->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No se puede eliminar el lectivo porque tiene cursos o periodos asociados.',
            ], 409);
        }

        $lectivo->lectivosNivel()->delete();
        $lectivo->delete();

        return response()->json(['status' => 'success', 'message' => 'Año lectivo eliminado.']);
    }

    protected function authorize(Request $request, Lectivo $lectivo): void
    {
        if ($lectivo->institucion_id !== $this->institucionId($request)) {
            abort(403, 'El lectivo no pertenece a su institución.');
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
