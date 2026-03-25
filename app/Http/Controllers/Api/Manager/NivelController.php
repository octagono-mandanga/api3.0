<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Controller;
use App\Models\Core\NivelInstitucion;
use App\Models\Core\GradoInstitucion;
use App\Models\Core\NivelEducativo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NivelController extends Controller
{
    /**
     * Lista los niveles de formación configurados para la institución,
     * incluyendo sus grados asociados.
     */
    public function index(Request $request): JsonResponse
    {
        $institucionId = $this->institucionId($request);

        $niveles = NivelInstitucion::where('institucion_id', $institucionId)
            ->with(['nivel.grados' => fn ($q) => $q->orderBy('orden')])
            ->get();

        return response()->json(['status' => 'success', 'data' => $niveles]);
    }

    /**
     * Asocia un nivel de formación a la institución.
     */
    public function store(Request $request): JsonResponse
    {
        $institucionId = $this->institucionId($request);

        $validated = $request->validate([
            'nivel_id' => 'required|integer|exists:core.niveles_educativos,id',
        ]);

        $nivel = NivelInstitucion::updateOrCreate(
            [
                'institucion_id' => $institucionId,
                'nivel_id'       => $validated['nivel_id'],
            ],
            ['estado' => 'activo']
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Nivel asociado a la institución.',
            'data'    => $nivel->load('nivel'),
        ], 201);
    }

    /**
     * Muestra un nivel de la institución con sus grados.
     */
    public function show(Request $request, NivelInstitucion $nivel): JsonResponse
    {
        $this->authorize($request, $nivel);

        $nivel->load(['nivel.grados' => fn ($q) => $q->orderBy('orden')]);

        // Agregar grados-institucion
        $gradosInstitucion = GradoInstitucion::where('institucion_id', $nivel->institucion_id)
            ->whereHas('grado', fn ($q) => $q->where('nivel_id', $nivel->nivel_id))
            ->with('grado')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'nivel'              => $nivel,
                'grados_institucion' => $gradosInstitucion,
            ],
        ]);
    }

    /**
     * Actualiza estado del nivel en la institución.
     */
    public function update(Request $request, NivelInstitucion $nivel): JsonResponse
    {
        $this->authorize($request, $nivel);

        $validated = $request->validate([
            'estado' => 'required|in:activo,inactivo',
        ]);

        $nivel->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Nivel actualizado.',
            'data'    => $nivel->fresh(),
        ]);
    }

    /**
     * Elimina la asociación del nivel (si no tiene dependencias).
     */
    public function destroy(Request $request, NivelInstitucion $nivel): JsonResponse
    {
        $this->authorize($request, $nivel);

        // Verificar integridad referencial: grados de institución en este nivel
        $tieneGrados = GradoInstitucion::where('institucion_id', $nivel->institucion_id)
            ->whereHas('grado', fn ($q) => $q->where('nivel_id', $nivel->nivel_id))
            ->exists();

        if ($tieneGrados) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No se puede eliminar el nivel porque tiene grados asociados.',
            ], 409);
        }

        $nivel->delete();

        return response()->json(['status' => 'success', 'message' => 'Nivel eliminado.']);
    }

    /**
     * Sincroniza grados de la institución para un nivel.
     * Recibe un array de grado_id con sus alias opcionales.
     */
    public function syncGrados(Request $request): JsonResponse
    {
        $institucionId = $this->institucionId($request);

        $validated = $request->validate([
            'nivel_id' => 'required|integer|exists:core.niveles_educativos,id',
            'grados'   => 'required|array',
            'grados.*.grado_id' => 'required|integer|exists:core.grados,id',
            'grados.*.alias'    => 'nullable|string|max:50',
        ]);

        $resultados = [];
        foreach ($validated['grados'] as $gradoData) {
            $resultados[] = GradoInstitucion::updateOrCreate(
                [
                    'institucion_id' => $institucionId,
                    'grado_id'       => $gradoData['grado_id'],
                ],
                [
                    'alias'  => $gradoData['alias'] ?? null,
                    'estado' => 'activo',
                ]
            );
        }

        return response()->json([
            'status'  => 'success',
            'message' => count($resultados) . ' grados sincronizados.',
            'data'    => $resultados,
        ]);
    }

    /**
     * Devuelve el catálogo general de niveles educativos.
     */
    public function catalogo(): JsonResponse
    {
        $niveles = NivelEducativo::with(['grados' => fn ($q) => $q->orderBy('orden')])
            ->orderBy('orden')
            ->get();

        return response()->json(['status' => 'success', 'data' => $niveles]);
    }

    protected function authorize(Request $request, NivelInstitucion $nivel): void
    {
        if ($nivel->institucion_id !== $this->institucionId($request)) {
            abort(403, 'El nivel no pertenece a su institución.');
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
