<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpecieController;
use App\Http\Controllers\CatchLogController;
use App\Http\Controllers\FishCatchController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LureController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

//endpoint
Route::get('/x', function () {
    return 'API';
});
// SPECIE

Route::get('/specie', [SpecieController::class, 'index']);
Route::post('/specie', [SpecieController::class, 'store']);

// LOCATION
Route::get('/location', [LocationController::class, 'index']);
// Route::get('/location', [LocationController::class, 'post']);
// Route::get('/location', [LocationController::class, 'show']);
Route::post('/location', [LocationController::class, 'store']);
// Route::get('/location', [LocationController::class, 'update']);
//Route::get('/location', [LocationController::class, 'destroy']);

// LURE
Route::get('/lure', [LureController::class, 'index']);
Route::post('/lure', [LureController::class, 'store']);

// CATCH LOG
Route::get('/catchlog', [CatchLogController::class, 'index']);
Route::get('/catchlog/{id}', [CatchLogController::class, 'show']);
Route::post('/catchlog', [CatchLogController::class, 'store']);
// Route::patch('/catchlog/{id}', [CatchLogController::class, 'update']);
// Route::delete('/catchlog/{id}', [CatchLogController::class, 'destroy']);

// FISH CATCH
Route::get('/fishcatch', [FishCatchController::class, 'index']);
Route::get('/fishcatch/{id}', [FishCatchController::class, 'show']);
Route::post('/fishcatch', [FishCatchController::class, 'store']);
Route::delete('/fishcatch/{id}', [FishCatchController::class, 'destroy']);


//region users
//User kezelés, login, logout
//Mindenki
Route::post('user/login', [UserController::class, 'login']);
Route::post('user/logout', [UserController::class, 'logout']);
Route::post('user', [UserController::class, 'store']);
Route::post('user', [UserController::class, 'show']);


//Mindenki
Route::post('users/login', [UserController::class, 'login']);
Route::post('users/logout', [UserController::class, 'logout']);
Route::post('users', [UserController::class, 'store']);

//Admin: 
//minden user lekérdezése
Route::get('user', [UserController::class, 'index']);

//Egy user lekérése    
Route::get('user/{id}', [UserController::class, 'show']);

//User adatok módosítása      
Route::patch('user/{id}', [UserController::class, 'update']);

//User törlés
Route::delete('user/{id}', [UserController::class, 'destroy']);
//endregion

