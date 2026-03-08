<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PerfilUsuarioService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected $perfilService;

    public function __construct(PerfilUsuarioService $perfilService)
    {
        $this->perfilService = $perfilService;
    }
    public function show(Request $request)
	{
	    // El usuario ya viene en el request gracias al middleware sanctum
	    $usuario = $request->user();

	    // Cargamos la relación de cuentas sociales definida en el modelo Usuario
	    $usuario->load('cuentasSociales');

	    return response()->json([
	        'status' => 'success',
	        'data' => [
	            'personal_info' => [
	                'id' => $usuario->id,
	                'first_name' => $usuario->first_name,
	                'middle_name' => $usuario->middle_name,
	                'last_name_1' => $usuario->last_name_1,
	                'last_name_2' => $usuario->last_name_2,
	                'email' => $usuario->email,
	                'avatar_url' => $usuario->avatar_url,
	                'global_status' => $usuario->global_status,
	            ],
	            'social_accounts' => $usuario->cuentasSociales->map(function ($account) {
	                return [
	                    'provider' => $account->provider,
	                    'provider_email' => $account->provider_email,
	                    'linked_at' => $account->linked_at,
	                ];
	            }),
	            'metadata' => [
	                'server_time' => now(),
	                'institution_context' => config('app.current_institution.name')
	            ]
	        ]
	    ]);
    }
    public function update(Request $request)
    {
        $usuario = $request->user();

        $validated = $request->validate([
            'first_name'   => 'string|max:255',
            'last_name_1'  => 'string|max:255',
            'social_email' => 'nullable|email',
            'social_provider' => 'nullable|string'
        ]);

        $updatedUser = $this->perfilService->actualizarPerfil($usuario, $validated);

        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'user' => $updatedUser
        ]);
    }

public function uploadAvatar(Request $request)
{
    $request->validate([
        'avatar' => 'required|string' // El string Base64
    ]);

    try {
	$usuario = $request->user();
        $url = $this->perfilService->actualizarAvatar($usuario, $request->input('avatar'));

        return response()->json([
            'status' => 'success',
            'message' => 'Avatar actualizado con éxito',
            'url' => $url
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al procesar imagen'], 500);
    }
}

}
