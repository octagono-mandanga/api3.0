<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(UserProfileService $profileService)
    {
        $this->profileService = $profileService;
    }
    public function show(Request $request)
	{
	    // El usuario ya viene en el request gracias al middleware sanctum
	    $user = $request->user();

	    // Cargamos la relación de cuentas sociales definida en el modelo User
	    $user->load('socialAccounts');

	    return response()->json([
	        'status' => 'success',
	        'data' => [
	            'personal_info' => [
	                'id' => $user->id,
	                'first_name' => $user->first_name,
	                'middle_name' => $user->middle_name,
	                'last_name_1' => $user->last_name_1,
	                'last_name_2' => $user->last_name_2,
	                'email' => $user->email,
	                'avatar_url' => $user->avatar_url,
	                'global_status' => $user->global_status,
	            ],
	            'social_accounts' => $user->socialAccounts->map(function ($account) {
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
        $user = $request->user();

        $validated = $request->validate([
            'first_name'   => 'string|max:255',
            'last_name_1'  => 'string|max:255',
            'social_email' => 'nullable|email',
            'social_provider' => 'nullable|string'
        ]);

        $updatedUser = $this->profileService->updateProfile($user, $validated);

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
	$user = $request->user();
        $url = $this->profileService->updateAvatar($user, $request->input('avatar'));

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
