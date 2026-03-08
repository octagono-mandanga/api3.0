<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class RefController
{
    /**
     * Lista todos los departamentos activos.
     * GET /api/ref/departamentos
     */
    public function departamentos()
    {
        $departamentos = Cache::remember('ref.departamentos', 3600, function () {
            return DB::table('ref.departamentos')
                ->where('estado', 'activo')
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'codigo']);
        });

        return response()->json($departamentos);
    }

    /**
     * Lista los municipios de un departamento.
     * GET /api/ref/municipios?departamento_id=X
     */
    public function municipios(Request $request)
    {
        $departamentoId = $request->query('departamento_id');

        if (!$departamentoId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Se requiere el parámetro departamento_id',
            ], 422);
        }

        $municipios = Cache::remember("ref.municipios.{$departamentoId}", 3600, function () use ($departamentoId) {
            return DB::table('ref.municipios')
                ->where('departamento_id', $departamentoId)
                ->where('estado', 'activo')
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'codigo']);
        });

        return response()->json($municipios);
    }
}
