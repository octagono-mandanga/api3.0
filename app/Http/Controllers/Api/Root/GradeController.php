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
        $query = Grade::with('educationalLevel')->orderBy('order');
        
        if ($request->has('educational_level_id')) {
            $query->where('educational_level_id', $request->educational_level_id);
        }

        return response()->json($query->get());
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
     * Get grades for a specific institution with active status.
     */
    public function getInstitutionGrades(Request $request): JsonResponse
    {
        $request->validate([
            'institution_id' => 'required|uuid|exists:auth.institutions,id',
            'educational_level_id' => 'sometimes|uuid|exists:core.educational_levels,id'
        ]);

        $institutionId = $request->institution_id;
        $levelId = $request->educational_level_id;

        $query = Grade::leftJoin('core.institution_grades', function ($join) use ($institutionId) {
            $join->on('grades.id', '=', 'institution_grades.grade_id')
                 ->where('institution_grades.institution_id', '=', $institutionId);
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

        return response()->json($grades);
    }

    /**
     * Sync grade association for an institution.
     */
    public function syncInstitutionGrade(Request $request): JsonResponse
    {
        $request->validate([
            'institution_id' => 'required|uuid|exists:auth.institutions,id',
            'grade_id' => 'required|uuid|exists:core.grades,id',
            'is_active' => 'required|boolean'
        ]);

        $association = InstitutionGrade::updateOrCreate(
            [
                'institution_id' => $request->institution_id,
                'grade_id' => $request->grade_id,
            ],
            [
                'is_active' => $request->is_active
            ]
        );

        return response()->json($association);
    }
}
