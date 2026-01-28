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
<<<<<<< HEAD
Route::get('/catchlog', [CatchLogController::class, 'index']);
Route::get('/catchlog/{id}', [CatchLogController::class, 'show']);
Route::post('/catchlog', [CatchLogController::class, 'store']);
Route::patch('/catchlog/{id}', [CatchLogController::class, 'update']);
Route::delete('/catchlog/{id}', [CatchLogController::class, 'destroy']);

=======
Route::get('/catchlogs', [CatchLogController::class, 'index']);
Route::get('/catchlogs/{id}', [CatchLogController::class, 'show']);
Route::post('/catchlogs', [CatchLogController::class, 'store']);
Route::patch('/catchlogs/{id}', [CatchLogController::class, 'update']);
Route::delete('/catchlogs/{id}', [CatchLogController::class, 'destroy']);
 
>>>>>>> 2b0f748797368cd95b35601c30b2252f98f8b8a2
// FISH CATCH
Route::get('/fishcatches', [FishCatchController::class, 'index']);
Route::get('/fishcatches/{id}', [FishCatchController::class, 'show']);
Route::post('/fishcatches', [FishCatchController::class, 'store']);
Route::delete('/fishcatches/{id}', [FishCatchController::class, 'destroy']);
 
 
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