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
 
Route::get('/species', [SpecieController::class, 'index']);
Route::post('/species', [SpecieController::class, 'store']);
Route::get('/species/photo/{filename}', [SpecieController::class, 'photo']);
 
// LOCATION
Route::get('/locations', [LocationController::class, 'index']);
// Route::get('/location', [LocationController::class, 'post']);
// Route::get('/location', [LocationController::class, 'show']);
Route::post('/locations', [LocationController::class, 'store']);
// Route::get('/location', [LocationController::class, 'update']);
//Route::get('/location', [LocationController::class, 'destroy']);
 
// LURE
Route::get('/lures', [LureController::class, 'index']);
Route::post('/lures', [LureController::class, 'store']);
 
// CATCH LOG
Route::get('/catchlogs', [CatchLogController::class, 'index']);
Route::get('/catchlogs/{id}', [CatchLogController::class, 'show']);
Route::post('/catchlogs', [CatchLogController::class, 'store']);
Route::patch('/catchlogs/{id}', [CatchLogController::class, 'update']);
Route::delete('/catchlogs/{id}', [CatchLogController::class, 'destroy']);
 
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

//region users
//User kezelés, login, logout
//Mindenki
Route::post('users/login', [UserController::class, 'login']);
Route::post('users/logout', [UserController::class, 'logout']);
Route::post('users', [UserController::class, 'store']);
Route::post('users', [UserController::class, 'show']);
 
 
//Mindenki
Route::post('users/login', [UserController::class, 'login']);
Route::post('users/logout', [UserController::class, 'logout']);
Route::post('users', [UserController::class, 'store']);
 
//Admin:
//minden user lekérdezése
Route::get('users', [UserController::class, 'index']);
 
//Egy user lekérése    
Route::get('users/{id}', [UserController::class, 'show']);
 
//User adatok módosítása      
Route::patch('users/{id}', [UserController::class, 'update']);
 
//User törlés
Route::delete('users/{id}', [UserController::class, 'destroy']);
//endregion
