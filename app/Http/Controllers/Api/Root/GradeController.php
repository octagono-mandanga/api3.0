<?php

namespace App\Http\Controllers\Api\Root;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\InstitutionGrade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $institutionId = $request->query('institution_id');
        $levelId = $request->query('educational_level_id');

        if ($institutionId) {
            $query = Grade::leftJoin('core.institution_grades', function ($join) use ($institutionId) {
                $join->on('core.grades.id', '=', 'core.institution_grades.grade_id')
                     ->where('core.institution_grades.institution_id', '=', $institutionId);
            })
            ->select(
                'core.grades.*',
                DB::raw('COALESCE(core.institution_grades.is_active, false) as is_enabled'),
                'core.institution_grades.id as association_id'
            );

            if ($levelId) {
                $query->where('core.grades.educational_level_id', $levelId);
            }

            $grades = $query->orderBy('core.grades.order')->get();
        } else {
            $query = Grade::with('educationalLevel')->orderBy('order');
            if ($levelId) {
                $query->where('educational_level_id', $levelId);
            }
            $grades = $query->get();
        }

        return response()->json($grades);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'educational_level_id' => 'required|uuid|exists:core.educational_levels,id',
            'short_name' => 'required|string|max:50',
            'full_name' => 'required|string|max:255',
            'order' => 'nullable|integer'
        ]);

        $grade = Grade::create($data);
        return response()->json($grade, 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json(Grade::with('educationalLevel')->findOrFail($id));
    }

    public function update(Request $request, $id): JsonResponse
    {
        $grade = Grade::findOrFail($id);
        $data = $request->validate([
            'educational_level_id' => 'sometimes|required|uuid|exists:core.educational_levels,id',
            'short_name' => 'sometimes|required|string|max:50',
            'full_name' => 'sometimes|required|string|max:255',
            'order' => 'nullable|integer'
        ]);

        $grade->update($data);
        return response()->json($grade);
    }

    public function destroy($id): JsonResponse
    {
        $grade = Grade::findOrFail($id);
        $grade->delete();
        return response()->json(null, 204);
    }

    /**
     * Sync grade association for an institution.
     */
    public function syncInstitutionGrade(Request $request): JsonResponse
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
                'grade_id' => 'required|uuid|exists:core.grades,id'
            ]);

            $association = InstitutionGrade::updateOrCreate(
                [
                    'institution_id' => $request->institution_id,
                    'grade_id' => $request->grade_id,
                ],
                [
                    'is_active' => $isEnabled
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Grado sincronizado correctamente.',
                'data' => $association
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al sincronizar el grado',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
