<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Controller;
use App\Models\Core\Plan;
use Illuminate\Http\JsonResponse;

class PlanController extends Controller
{
    public function index(): JsonResponse
    {
        $planes = Plan::where('estado', 'activo')
            ->orderBy('id')
            ->get(['id', 'nombre', 'codigo', 'descripcion', 'max_usuarios', 'max_estudiantes']);

        return response()->json([
            'status' => 'success',
            'data'   => $planes,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $plan = Plan::where('estado', 'activo')
            ->select(['id', 'nombre', 'codigo', 'descripcion', 'max_usuarios', 'max_estudiantes'])
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $plan,
        ]);
    }
}
