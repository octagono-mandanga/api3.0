<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Controller;
use App\Models\Core\Sede;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SedeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $institucionId = $this->institucionId($request);

        $sedes = Sede::where('institucion_id', $institucionId)
            ->with('municipio')
            ->orderBy('es_principal', 'desc')
            ->orderBy('nombre')
            ->get();

        return response()->json(['status' => 'success', 'data' => $sedes]);
    }

    public function store(Request $request): JsonResponse
    {
        $institucionId = $this->institucionId($request);

        $validated = $request->validate([
            'nombre'       => 'required|string|max:100',
            'codigo'       => 'nullable|string|max:20',
            'municipio_id' => 'nullable|integer|exists:ref.municipios,id',
            'direccion'    => 'nullable|string|max:150',
            'telefono'     => 'nullable|string|max:50',
            'es_principal' => 'sometimes|boolean',
            'latitud'      => 'nullable|numeric|between:-90,90',
            'longitud'     => 'nullable|numeric|between:-180,180',
        ]);

        $validated['institucion_id'] = $institucionId;
        $validated['codigo'] = $validated['codigo'] ?? Str::upper(Str::slug($validated['nombre'], '_'));
        $validated['estado'] = 'activo';

        $sede = Sede::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Sede creada exitosamente.',
            'data'    => $sede,
        ], 201);
    }

    public function show(Request $request, Sede $sede): JsonResponse
    {
        $this->authorize($request, $sede);

        return response()->json([
            'status' => 'success',
            'data'   => $sede->load('municipio'),
        ]);
    }

    public function update(Request $request, Sede $sede): JsonResponse
    {
        $this->authorize($request, $sede);

        $validated = $request->validate([
            'nombre'       => 'sometimes|string|max:100',
            'codigo'       => 'sometimes|string|max:20',
            'municipio_id' => 'nullable|integer|exists:ref.municipios,id',
            'direccion'    => 'nullable|string|max:150',
            'telefono'     => 'nullable|string|max:50',
            'es_principal' => 'sometimes|boolean',
            'latitud'      => 'nullable|numeric|between:-90,90',
            'longitud'     => 'nullable|numeric|between:-180,180',
            'estado'       => 'sometimes|in:activo,inactivo',
        ]);

        $sede->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Sede actualizada.',
            'data'    => $sede->fresh(),
        ]);
    }

    public function destroy(Request $request, Sede $sede): JsonResponse
    {
        $this->authorize($request, $sede);

        if ($sede->cursos()->exists() || $sede->perfiles()->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No se puede eliminar la sede porque tiene cursos o personal asociado.',
            ], 409);
        }

        $sede->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Sede eliminada.',
        ]);
    }

    /**
     * Verifica que la sede pertenezca a la institución del manager.
     */
    protected function authorize(Request $request, Sede $sede): void
    {
        $institucionId = $this->institucionId($request);
        if ($sede->institucion_id !== $institucionId) {
            abort(403, 'La sede no pertenece a su institución.');
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
