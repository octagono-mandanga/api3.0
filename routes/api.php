<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Middleware\IdentifyInstitution;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ConfiguracionInicialController;
use App\Http\Controllers\Api\SolicitudActivacionController;
use App\Http\Controllers\Api\RefController;

//Root
use App\Http\Controllers\Api\Root\InstitucionController;
use App\Http\Controllers\Api\Root\SedeController;
use App\Http\Controllers\Api\Root\RolController;
use App\Http\Controllers\Api\Root\RolInstitucionController;
use App\Http\Controllers\Api\Root\NivelController;
use App\Http\Controllers\Api\Root\GradoController;
use App\Http\Controllers\Api\Root\AreaController;
use App\Http\Controllers\Api\Root\LectivoController;
use App\Http\Controllers\Api\Root\AuditoriaController;

// Página por defecto de la API
Route::get('/', function() {
    return response()->json([
        'name' => config('app.name', 'API'),
        'version' => '3.0',
        'status' => 'running',
        'timestamp' => now()->toIso8601String()
    ]);
});

//Route::post('/login', [AuthController::class, 'login'])->middleware(IdentifyInstitution::class);
Route::get('/ping', function() { return response()->json(['status' => 'ok', 'time' => now()]); });


Route::post('/login', [AuthController::class, 'login']);
Route::apiResource('instituciones', InstitucionController::class);

// Datos de referencia (sin autenticción, usados en el wizard de configuración)
Route::prefix('ref')->group(function () {
    Route::get('/departamentos', [RefController::class, 'departamentos']);
    Route::get('/municipios', [RefController::class, 'municipios']);
});

// Configuración inicial de instituciones (sin autenticación para el wizard inicial) ++
Route::post('/instituciones/{id}/configuracion-inicial', [ConfiguracionInicialController::class, 'store']);
Route::post('/instituciones/{id}/configuracion-completada', [ConfiguracionInicialController::class, 'marcarCompletada']);
Route::get('/usuarios/buscar', [ConfiguracionInicialController::class, 'buscarUsuario']);

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
    Route::apiResource('instituciones', InstitucionController::class)->names('root.instituciones');
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

    // Auditoría
    Route::get('auditoria/online-users', [AuditoriaController::class, 'getOnlineUsers']);
    Route::get('auditoria/access-logs', [AuditoriaController::class, 'getAccessLogs']);
});

// =====================================================
// SOLICITUDES DE ACTIVACIÓN (públicas, con rate limiting)
// =====================================================
Route::prefix('solicitudes')->middleware('throttle.solicitudes:general')->group(function () {
    // Crear solicitud (paso 1) - limitado a 3 por hora
    Route::post('/', [SolicitudActivacionController::class, 'crear'])
        ->middleware('throttle.solicitudes:crear');

    // Obtener datos de una solicitud (para pre-llenar la página de configuración)
    Route::get('/{id}', [SolicitudActivacionController::class, 'obtener']);

    // Verificar código email (paso 2)
    Route::post('/{id}/verificar-email', [SolicitudActivacionController::class, 'verificarEmail'])
        ->middleware('throttle.solicitudes:codigo');

    // Verificar código SMS (paso 3)
    Route::post('/{id}/verificar-sms', [SolicitudActivacionController::class, 'verificarSms'])
        ->middleware('throttle.solicitudes:codigo');

    // Reenviar códigos
    Route::post('/{id}/reenviar-email', [SolicitudActivacionController::class, 'reenviarCodigoEmail'])
        ->middleware('throttle.solicitudes:reenviar');

    Route::post('/{id}/reenviar-sms', [SolicitudActivacionController::class, 'reenviarCodigoSms'])
        ->middleware('throttle.solicitudes:reenviar');
});
