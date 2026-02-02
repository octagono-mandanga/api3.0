<?php

namespace App\Http\Controllers\Api\Root;

use App\Http\Controllers\Controller;
use App\Models\EducationalLevel;
use App\Models\InstitutionLevel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EducationalLevelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $institutionId = $request->query('institution_id');

        if ($institutionId) {
            $levels = EducationalLevel::leftJoin('core.institution_educational_levels', function ($join) use ($institutionId) {
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
        } else {
            $levels = EducationalLevel::orderBy('order')->get();
        }

        return response()->json($levels);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer'
        ]);

        $level = EducationalLevel::create($data);
        return response()->json($level, 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json(EducationalLevel::findOrFail($id));
    }

    public function update(Request $request, $id): JsonResponse
    {
        $level = EducationalLevel::findOrFail($id);
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer'
        ]);

        $level->update($data);
        return response()->json($level);
    }

    public function destroy($id): JsonResponse
    {
        $level = EducationalLevel::findOrFail($id);
        $level->delete();
        return response()->json(null, 204);
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

            $association = InstitutionLevel::updateOrCreate(
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
