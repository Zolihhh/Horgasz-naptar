<?php

use App\Http\Controllers\CatchLogController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\FishCatchController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LureController;
use App\Http\Controllers\SpecieController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/x', function () {
    return 'API';
});

// SPECIE
Route::get('/species', [SpecieController::class, 'index']);
Route::post('/species', [SpecieController::class, 'store']);
Route::get('/species/photo/{filename}', [SpecieController::class, 'photo']);

// LOCATION
Route::get('/locations', [LocationController::class, 'index']);
Route::post('/locations', [LocationController::class, 'store']);

// CITIES
Route::get('/cities', [CityController::class, 'index']);

// LURE
Route::get('/lures', [LureController::class, 'index']);
Route::post('/lures', [LureController::class, 'store']);

// CATCH LOG
Route::get('/catchlogs', [CatchLogController::class, 'index'])
    ->middleware(['auth:sanctum', 'ability:catchlogs:get']);
Route::get('/catchlogs/{id}', [CatchLogController::class, 'show'])
    ->middleware(['auth:sanctum', 'ability:catchlogs:get']);
Route::post('/catchlogs', [CatchLogController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:catchlogs:post']);
Route::patch('/catchlogs/{id}', [CatchLogController::class, 'update'])
    ->middleware(['auth:sanctum', 'ability:catchlogs:patch']);
Route::delete('/catchlogs/{id}', [CatchLogController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:catchlogs:delete']);

// FISH CATCH
Route::get('/fishcatches', [FishCatchController::class, 'index'])
    ->middleware(['auth:sanctum', 'ability:fishcatches:get']);
Route::get('/fishcatches/{id}', [FishCatchController::class, 'show'])
    ->middleware(['auth:sanctum', 'ability:fishcatches:get']);
Route::post('/fishcatches', [FishCatchController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:fishcatches:post']);
Route::delete('/fishcatches/{id}', [FishCatchController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:fishcatches:delete']);
Route::patch('/fishcatches/{id}', [FishCatchController::class, 'update'])
    ->middleware(['auth:sanctum', 'ability:fishcatches:patch']);

// USERS - public
Route::post('users/login', [UserController::class, 'login']);
Route::post('users/logout', [UserController::class, 'logout']);
Route::post('users', [UserController::class, 'store']);

// USERS - self
Route::get('usersme', [UserController::class, 'indexSelf'])
    ->middleware(['auth:sanctum', 'ability:usersme:get']);
Route::patch('usersme', [UserController::class, 'updateSelf'])
    ->middleware(['auth:sanctum', 'ability:usersme:patch']);
Route::patch('usersme/password', [UserController::class, 'updatePassword'])
    ->middleware(['auth:sanctum', 'ability:usersme:updatePassword']);
Route::delete('usersme', [UserController::class, 'destroySelf'])
    ->middleware(['auth:sanctum', 'ability:usersme:delete']);

// USERS - admin
Route::get('users', [UserController::class, 'index'])
    ->middleware(['auth:sanctum', 'ability:users:get']);
Route::get('users/{id}', [UserController::class, 'show'])
    ->middleware(['auth:sanctum', 'ability:users:get']);
Route::patch('users/{id}', [UserController::class, 'update'])
    ->middleware(['auth:sanctum', 'ability:users:patch']);
Route::delete('users/{id}', [UserController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:users:delete']);
