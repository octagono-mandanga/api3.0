<?php

namespace App\Http\Controllers\Api\Root;

use App\Http\Controllers\Controller;
use App\Models\Core\NivelEducativo as Nivel;
use App\Models\Core\NivelInstitucion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NivelController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Nivel::orderBy('order')->get());
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'order' => 'nullable|integer'
            ]);

            $nivel = Nivel::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Nivel educativo creado exitosamente.',
                'data' => $nivel
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el nivel educativo',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $nivel = Nivel::findOrFail($id);
            return response()->json($nivel);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nivel educativo no encontrado',
                'details' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $nivel = Nivel::findOrFail($id);
            $data = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'order' => 'nullable|integer'
            ]);

            $nivel->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Nivel educativo actualizado exitosamente.',
                'data' => $nivel
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el nivel educativo',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $nivel = Nivel::findOrFail($id);
            $nivel->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Nivel educativo eliminado exitosamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al eliminar el nivel educativo',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get levels for a specific institution with active status.
     */
    public function getInstitutionLevels(Request $request): JsonResponse
    {
        $institutionId = $request->query('institution_id');

        if (!$institutionId) {
            return response()->json(['error' => 'El ID de la institución es requerido.'], 400);
        }

        $levels = Nivel::leftJoin('core.institution_educational_levels', function ($join) use ($institutionId) {
            $join->on('core.educational_levels.id', '=', 'core.institution_educational_levels.educational_level_id')
                 ->where('core.institution_educational_levels.institution_id', '=', $institutionId);
        })
        ->select(
            'core.educational_levels.*',
            DB::raw('COALESCE(core.institution_educational_levels.is_active, false) as is_enabled'),
            'core.institution_educational_levels.id as association_id'
        )
        ->orderBy('core.educational_levels.order')
        ->get();

        return response()->json($levels);
    }

    /**
     * Sync level association for an institution.
     */
    public function syncInstitutionLevel(Request $request): JsonResponse
    {
        try {
            $isEnabled = $request->input('is_enabled') ?? $request->input('is_active');

            if (is_null($isEnabled)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El campo is_enabled o is_active es requerido.'
                ], 422);
            }

            $request->validate([
                'institution_id' => 'required|uuid|exists:auth.institutions,id',
                'educational_level_id' => 'required|uuid|exists:core.educational_levels,id'
            ]);

            $association = NivelInstitucion::updateOrCreate(
                [
                    'institution_id' => $request->institution_id,
                    'educational_level_id' => $request->educational_level_id,
                ],
                [
                    'is_active' => $isEnabled
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Nivel educativo sincronizado correctamente.',
                'data' => $association
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al sincronizar el nivel educativo',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
