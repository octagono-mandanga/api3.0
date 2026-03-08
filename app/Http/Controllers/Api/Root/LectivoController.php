<?php

namespace App\Http\Controllers\Api\Root;

use App\Http\Controllers\Controller;
use App\Models\Core\Lectivo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LectivoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Lectivo::with('nivel');

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

            $lectivo = Lectivo::create($data);
            return response()->json($lectivo->load('nivel'), 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $lectivo = Lectivo::with('nivel')->findOrFail($id);
        return response()->json($lectivo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $lectivo = Lectivo::findOrFail($id);

        $data = $request->validate([
            'educational_level_id' => 'sometimes|required|uuid|exists:core.educational_levels,id',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'status' => 'sometimes|required|string|in:active,previous,inactive'
        ]);

        return DB::transaction(function () use ($lectivo, $data) {
            $levelId = $data['educational_level_id'] ?? $lectivo->educational_level_id;

            if (isset($data['status']) && $data['status'] !== $lectivo->status) {
                $this->handleStatusTransitions($levelId, $data['status'], $lectivo->id);
            }

            $lectivo->update($data);
            return response()->json($lectivo->load('nivel'));
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $lectivo = Lectivo::findOrFail($id);
        $lectivo->delete();
        return response()->json(null, 204);
    }

    /**
     * Handle business rules for status transitions.
     */
    private function handleStatusTransitions(string $levelId, string $newStatus, ?string $excludeId = null): void
    {
        if ($newStatus === 'active') {
            // El previo activo pasa a anterior
            $currentActive = Lectivo::where('educational_level_id', $levelId)
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
