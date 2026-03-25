<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Middleware\IdentifyInstitution;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ConfiguracionInicialController;
use App\Http\Controllers\Api\SolicitudActivacionController;
use App\Http\Controllers\Api\InstitucionRolController;
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

//Manager
use App\Http\Controllers\Api\Manager\InstitucionController as ManagerInstitucionController;
use App\Http\Controllers\Api\Manager\SedeController as ManagerSedeController;
use App\Http\Controllers\Api\Manager\NivelController as ManagerNivelController;
use App\Http\Controllers\Api\Manager\EscalaCalificacionController;
use App\Http\Controllers\Api\Manager\AreaFormacionController;
use App\Http\Controllers\Api\Manager\PeriodoController;
use App\Http\Controllers\Api\Manager\LectivoController as ManagerLectivoController;
use App\Http\Controllers\Api\Manager\UsuarioController as ManagerUsuarioController;
use App\Http\Controllers\Api\Manager\PlanController as ManagerPlanController;

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

// Recuperación de contraseña (público, sin autenticación)
Route::prefix('password-reset')->group(function () {
    Route::post('/', [PasswordResetController::class, 'requestReset']);
    Route::post('/verify', [PasswordResetController::class, 'verifyToken']);
    Route::post('/reset', [PasswordResetController::class, 'resetPassword']);
});

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
    Route::post('/switch-role', [AuthController::class, 'switchRole']);
    //Generales
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile/update', [ProfileController::class, 'update']);
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);

    // Gestión de roles principales de institución (rector y manager)
    Route::put('/instituciones/{institucion}/rector', [InstitucionRolController::class, 'cambiarRector']);
    Route::put('/instituciones/{institucion}/manager', [InstitucionRolController::class, 'cambiarManager']);
    Route::get('/instituciones/{institucion}/roles-principales', [InstitucionRolController::class, 'rolesPrincipales']);
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
// MANAGER - Rutas del administrador de institución
// =====================================================
Route::middleware(['auth:sanctum'])->prefix('manager')->name('manager.')->group(function () {
    // Institución
    Route::get('institucion', [ManagerInstitucionController::class, 'show']);
    Route::put('institucion', [ManagerInstitucionController::class, 'update']);
    Route::post('institucion/logo', [ManagerInstitucionController::class, 'uploadLogo']);
    Route::post('institucion/portada', [ManagerInstitucionController::class, 'uploadPortada']);

    // Planes (catálogo de referencia)
    Route::get('planes', [ManagerPlanController::class, 'index']);
    Route::get('planes/{id}', [ManagerPlanController::class, 'show']);

    // Sedes
    Route::apiResource('sedes', ManagerSedeController::class);

    // Niveles
    Route::get('niveles/catalogo', [ManagerNivelController::class, 'catalogo']);
    Route::apiResource('niveles', ManagerNivelController::class);
    Route::post('niveles/sync-grados', [ManagerNivelController::class, 'syncGrados']);

    // Escalas de calificación
    Route::apiResource('escalas', EscalaCalificacionController::class)->except(['destroy']);

    // Áreas de formación
    Route::get('areas/catalogo', [AreaFormacionController::class, 'catalogo']);
    Route::apiResource('areas', AreaFormacionController::class);

    // Lectivos (años lectivos)
    Route::apiResource('lectivos', ManagerLectivoController::class);

    // Periodos
    Route::apiResource('periodos', PeriodoController::class);

    // Usuarios (coordinadores, secretaria, docentes)
    Route::apiResource('usuarios', ManagerUsuarioController::class);
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
