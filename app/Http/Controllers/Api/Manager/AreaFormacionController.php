<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Controller;
use App\Models\Academico\AreaFormacion;
use App\Models\Academico\AreaInstitucion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AreaFormacionController extends Controller
{
    /**
     * Lista las áreas de formación asociadas a la institución,
     * agrupadas opcionalmente por nivel.
     */
    public function index(Request $request): JsonResponse
    {
        $institucionId = $this->institucionId($request);

        $query = AreaInstitucion::where('institucion_id', $institucionId)
            ->with(['area', 'nivel']);

        if ($request->has('nivel_id')) {
            $query->where('nivel_id', $request->integer('nivel_id'));
        }

        return response()->json([
            'status' => 'success',
            'data'   => $query->get(),
        ]);
    }

    /**
     * Asocia un área de formación a la institución para un nivel.
     */
    public function store(Request $request): JsonResponse
    {
        $institucionId = $this->institucionId($request);

        $validated = $request->validate([
            'area_id'  => 'required|integer|exists:academico.areas_formacion,id',
            'nivel_id' => 'nullable|integer|exists:core.niveles_educativos,id',
        ]);

        $areaInstitucion = AreaInstitucion::updateOrCreate(
            [
                'institucion_id' => $institucionId,
                'area_id'        => $validated['area_id'],
                'nivel_id'       => $validated['nivel_id'] ?? null,
            ],
            ['estado' => 'activo']
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Área de formación asociada.',
            'data'    => $areaInstitucion->load(['area', 'nivel']),
        ], 201);
    }

    /**
     * Muestra una asociación área-institución.
     */
    public function show(Request $request, AreaInstitucion $area): JsonResponse
    {
        $this->authorize($request, $area);

        return response()->json([
            'status' => 'success',
            'data'   => $area->load(['area', 'nivel']),
        ]);
    }

    /**
     * Actualiza el estado de la asociación.
     */
    public function update(Request $request, AreaInstitucion $area): JsonResponse
    {
        $this->authorize($request, $area);

        $validated = $request->validate([
            'nivel_id' => 'sometimes|nullable|integer|exists:core.niveles_educativos,id',
            'estado'   => 'sometimes|in:activo,inactivo',
        ]);

        $area->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Área de formación actualizada.',
            'data'    => $area->fresh()->load(['area', 'nivel']),
        ]);
    }

    /**
     * Elimina la asociación del área con la institución.
     */
    public function destroy(Request $request, AreaInstitucion $area): JsonResponse
    {
        $this->authorize($request, $area);

        $area->delete();

        return response()->json(['status' => 'success', 'message' => 'Área de formación desvinculada.']);
    }

    /**
     * Catálogo general de áreas de formación disponibles.
     */
    public function catalogo(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => AreaFormacion::where('estado', 'activo')->orderBy('nombre')->get(),
        ]);
    }

    protected function authorize(Request $request, AreaInstitucion $area): void
    {
        if ($area->institucion_id !== $this->institucionId($request)) {
            abort(403, 'El área no pertenece a su institución.');
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
