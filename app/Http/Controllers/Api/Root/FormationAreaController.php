<?php

namespace App\Http\Controllers\Api\Root;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Area::with('nivel');

        if ($request->has('educational_level_id')) {
            $query->where('educational_level_id', $request->educational_level_id);
        }

        return response()->json($query->orderBy('name')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'educational_level_id' => 'required|uuid|exists:core.educational_levels,id',
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
            'status' => 'required|string|in:active,inactive',
            'is_mandatory' => 'required|boolean'
        ]);

        $area = Area::create($data);
        return response()->json($area->load('nivel'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $area = Area::with('nivel')->findOrFail($id);
        return response()->json($area);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $area = Area::findOrFail($id);

        $data = $request->validate([
            'educational_level_id' => 'sometimes|required|uuid|exists:core.educational_levels,id',
            'name' => 'sometimes|required|string|max:255',
            'short_name' => 'sometimes|required|string|max:50',
            'status' => 'sometimes|required|string|in:active,inactive',
            'is_mandatory' => 'sometimes|required|boolean'
        ]);

        $area->update($data);
        return response()->json($area->load('nivel'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $area = Area::findOrFail($id);
        $area->delete();
        return response()->json(null, 204);
    }
}
