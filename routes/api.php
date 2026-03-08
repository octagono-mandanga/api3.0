<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Middleware\IdentifyInstitution;
use App\Http\Controllers\Api\ProfileController;

//Root
use App\Http\Controllers\Api\Root\InstitucionController;
use App\Http\Controllers\Api\Root\SedeController;
use App\Http\Controllers\Api\Root\RolController;
use App\Http\Controllers\Api\Root\RolInstitucionController;
use App\Http\Controllers\Api\Root\NivelController;
use App\Http\Controllers\Api\Root\GradoController;
use App\Http\Controllers\Api\Root\AreaController;
use App\Http\Controllers\Api\Root\LectivoController;

//Route::post('/login', [AuthController::class, 'login'])->middleware(IdentifyInstitution::class);
Route::get('/ping', function() { return response()->json(['status' => 'ok', 'time' => now()]); });

// TEMPORAL: Limpiar caché de rutas (ELIMINAR EN PRODUCCIÓN)
Route::get('/clear-route-cache', function() {
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    return response()->json(['message' => 'Route cache cleared!', 'output' => \Illuminate\Support\Facades\Artisan::output()]);
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user-context', [AuthController::class, 'userContext']);
    //Generales
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile/update', [ProfileController::class, 'update']);
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
});


Route::middleware(['auth:sanctum'])->prefix('root')->group(function () {
    Route::get('ping', function() { return response()->json(['status' => 'root-ok']); });


    Route::get('roles-institucion/{id}', [RolInstitucionController::class, 'show']);
    Route::get('roles-institucion/{institution_id?}', [RolInstitucionController::class, 'index']);
    Route::post('roles-institucion', [RolInstitucionController::class, 'store']);
    Route::put('roles-institucion/{id}', [RolInstitucionController::class, 'update']);
    Route::delete('roles-institucion/{id}', [RolInstitucionController::class, 'destroy']);

    Route::post('instituciones/upload-logo', [InstitucionController::class, 'uploadLogo']);
    Route::apiResource('instituciones', InstitucionController::class);
    Route::apiResource('sedes', SedeController::class);
    Route::get('roles', [RolController::class, 'index']);

  // RUTA 1: Test directo en institution-roles
  /*
  Route::get('institution-roles/{institution_id?}', function($institution_id = null) {
        return response()->json([
            'message' => 'TEST DIRECTO: Estas en InstitutionRole',
            'recibi_id' => $institution_id,
            'vps_time' => now()->toDateTimeString()
        ]);
    });
*/
    // RUTA 2: Test directo en roles
    /*
    Route::get('roles', function() {
        return response()->json([
            'message' => 'TEST DIRECTO: Estas en RoleController',
            'vps_time' => now()->toDateTimeString()
        ]);
    });
    */

    // Estructura Educativa
    Route::apiResource('niveles', NivelController::class);
    Route::get('niveles-institucion', [NivelController::class, 'getInstitutionLevels']);
    Route::post('niveles-institucion/sync', [NivelController::class, 'syncInstitutionLevel']);
    Route::apiResource('grados', GradoController::class);
    Route::post('grados-institucion/sync', [GradoController::class, 'syncInstitutionGrade']);

    Route::apiResource('areas', AreaController::class);
    Route::apiResource('lectivos', LectivoController::class);
});
