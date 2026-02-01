<?php

namespace App\Http\Controllers\Api\Root;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = AcademicYear::with('educationalLevel');

        if ($request->has('educational_level_id')) {
            $query->where('educational_level_id', $request->educational_level_id);
        }

        return response()->json($query->orderBy('start_date', 'desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'educational_level_id' => 'required|uuid|exists:core.educational_levels,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|string|in:active,previous,inactive'
        ]);

        return DB::transaction(function () use ($data) {
            $this->handleStatusTransitions($data['educational_level_id'], $data['status']);
            
            $academicYear = AcademicYear::create($data);
            return response()->json($academicYear->load('educationalLevel'), 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $academicYear = AcademicYear::with('educationalLevel')->findOrFail($id);
        return response()->json($academicYear);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $academicYear = AcademicYear::findOrFail($id);
        
        $data = $request->validate([
            'educational_level_id' => 'sometimes|required|uuid|exists:core.educational_levels,id',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'status' => 'sometimes|required|string|in:active,previous,inactive'
        ]);

        return DB::transaction(function () use ($academicYear, $data) {
            $levelId = $data['educational_level_id'] ?? $academicYear->educational_level_id;
            
            if (isset($data['status']) && $data['status'] !== $academicYear->status) {
                $this->handleStatusTransitions($levelId, $data['status'], $academicYear->id);
            }

            $academicYear->update($data);
            return response()->json($academicYear->load('educationalLevel'));
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $academicYear = AcademicYear::findOrFail($id);
        $academicYear->delete();
        return response()->json(null, 204);
    }

    /**
     * Handle business rules for status transitions.
     */
    private function handleStatusTransitions(string $levelId, string $newStatus, ?string $excludeId = null): void
    {
        if ($newStatus === 'active') {
            // El previo activo pasa a anterior
            $currentActive = AcademicYear::where('educational_level_id', $levelId)
                ->where('status', 'active');
            
            if ($excludeId) {
                $currentActive->where('id', '!=', $excludeId);
            }
                
            $currentActive = $currentActive->first();

            if ($currentActive) {
                // El previo anterior pasa a inactivo
                AcademicYear::where('educational_level_id', $levelId)
                    ->where('status', 'previous')
                    ->update(['status' => 'inactive']);

                $currentActive->update(['status' => 'previous']);
            }
        } elseif ($newStatus === 'previous') {
            // El previo anterior pasa a inactivo
            $query = AcademicYear::where('educational_level_id', $levelId)
                ->where('status', 'previous');

            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            $query->update(['status' => 'inactive']);
        }
    }
}
