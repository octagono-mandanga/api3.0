<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Middleware\IdentifyInstitution;
use App\Http\Controllers\Api\ProfileController;

//Root
use App\Http\Controllers\Api\Root\InstitutionController;
use App\Http\Controllers\Api\Root\CampusController;
use App\Http\Controllers\Api\Root\RoleController;
use App\Http\Controllers\Api\Root\InstitutionRoleController;
use App\Http\Controllers\Api\Root\EducationalLevelController;
use App\Http\Controllers\Api\Root\GradeController;
use App\Http\Controllers\Api\Root\FormationAreaController;
use App\Http\Controllers\Api\Root\AcademicYearController;

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
        
    
    Route::get('institutionrole/{id}', [InstitutionRoleController::class, 'show']);
    Route::get('institution-roles/{institution_id?}', [InstitutionRoleController::class, 'index']);
    Route::post('institution-roles', [InstitutionRoleController::class, 'store']);
    Route::delete('institution-roles/{id}', [InstitutionRoleController::class, 'destroy']);

    Route::post('institutions/upload-logo', [InstitutionController::class, 'uploadLogo']);
    Route::apiResource('institutions', InstitutionController::class);
    Route::apiResource('campuses', CampusController::class);
    Route::get('roles', [RoleController::class, 'index']);

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
    
    // Educational Structure
    Route::apiResource('educational-levels', EducationalLevelController::class);
    Route::get('institution-levels', [EducationalLevelController::class, 'getInstitutionLevels']);
    Route::post('institution-levels/sync', [EducationalLevelController::class, 'syncInstitutionLevel']); 

    Route::apiResource('grades', GradeController::class);
    Route::get('institution-grades', [GradeController::class, 'getInstitutionGrades']);
    Route::post('institution-grades/sync', [GradeController::class, 'syncInstitutionGrade']);

    Route::apiResource('formation-areas', FormationAreaController::class);
    Route::apiResource('academic-years', AcademicYearController::class);
});
