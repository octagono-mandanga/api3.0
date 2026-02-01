<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Middleware\IdentifyInstitution;
use App\Http\Controllers\Api\ProfileController;

//Root
use App\Http\Controllers\Api\Root\InstitutionController;
use App\Http\Controllers\Api\Root\CampusController;

//Route::post('/login', [AuthController::class, 'login'])->middleware(IdentifyInstitution::class);
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
    Route::post('institutions/upload-logo', [InstitutionController::class, 'uploadLogo']);
    Route::apiResource('institutions', InstitutionController::class);
    Route::apiResource('campuses', CampusController::class);

});
