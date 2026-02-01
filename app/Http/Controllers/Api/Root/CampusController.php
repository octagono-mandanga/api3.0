<?php

namespace App\Http\Controllers\Api\Root;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CampusController extends Controller
{
	public function index(Request $request)
	{
	    // Iniciamos la consulta cargando la relación con la institución
	    $query = Campus::with('institution');

	    // Si se pasa un institution_id en la URL, filtramos la consulta
	    if ($request->has('institution_id')) {
	        $query->where('institution_id', $request->institution_id);
	    }

	    return response()->json($query->get());
	}
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Usamos la clase del modelo para evitar errores de conexión por puntos
            'institution_id' => 'required|uuid|exists:App\Models\Institution,id',
            'name'           => 'required|string|max:150',
            'is_main'        => 'required|boolean',
            'status'         => 'required|in:activo,inactivo',
            'address'        => 'required|string',
            'phone'          => 'nullable|string|max:50',
            'city_id'        => 'nullable|integer',
            'location_lat'   => 'nullable|numeric',
            'location_lng'   => 'nullable|numeric'
        ]);

        try {
            // Manejo de la georreferenciación nativa POINT(long lat)
            if ($request->filled(['location_lat', 'location_lng'])) {
                $validated['location'] = DB::raw("point({$request->location_lng}, {$request->location_lat})");
            }

            $campus = Campus::create($validated);

            return response()->json([
                'status' => 'success',
                'data' => $campus
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show(Campus $campus)
    {
        return response()->json($campus->load('institution'));
    }

    public function update(Request $request, Campus $campus)
    {
        $validated = $request->validate([
            'name'         => 'sometimes|required|string|max:150',
            'is_main'      => 'sometimes|required|boolean',
            'status'       => 'sometimes|required|in:activo,inactivo',
            'address'      => 'sometimes|required|string',
            'phone'        => 'nullable|string|max:50',
            'location_lat' => 'nullable|numeric',
            'location_lng' => 'nullable|numeric'
        ]);

        if ($request->filled(['location_lat', 'location_lng'])) {
            $validated['location'] = DB::raw("point({$request->location_lng}, {$request->location_lat})");
        }

        $campus->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $campus
        ]);
    }

    public function destroy(Campus $campus)
    {
        $campus->delete();
        return response()->json(['status' => 'success', 'message' => 'Sede eliminada']);
    }
}
