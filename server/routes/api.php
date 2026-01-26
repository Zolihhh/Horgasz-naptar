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
Route::get('/x', function(){
    return 'API';
});
    // SPECIE
    Route::get('/specie', [SpecieController::class, 'index']);
    Route::post('/specie', [SpecieController::class, 'store']);

    // LOCATION
    Route::get('/location', [LocationController::class, 'index']);
    Route::post('/location', [LocationController::class, 'store']);

    // LURE
    Route::get('/lure', [LureController::class, 'index']);
    Route::post('/lure', [LureController::class, 'store']);

    // CATCH LOG
    Route::get('/catchlog', [CatchLogController::class, 'index']);
    Route::get('/catchlog/{id}', [CatchLogController::class, 'show']);
    Route::post('/catchlog', [CatchLogController::class, 'store']);
    Route::patch('/catchlog/{id}', [CatchLogController::class, 'update']);
    Route::delete('/catchlog/{id}', [CatchLogController::class, 'destroy']);

    // FISH CATCH
    Route::get('/fishcatch', [FishCatchController::class, 'index']);
    Route::get('/fishcatch/{id}', [FishCatchController::class, 'show']);
    Route::post('/fishcatch', [FishCatchController::class, 'store']);
    Route::delete('/fishcatch/{id}', [FishCatchController::class, 'destroy']);

Route::get('/specie', [SpecieController::class, 'index']);

//region users
//User kezelés, login, logout
//Mindenki
Route::post('users/login', [UserController::class, 'login']);
Route::post('users/logout', [UserController::class, 'logout']);
Route::post('users', [UserController::class, 'store']);

//Admin: 
//minden user lekérdezése
Route::get('users', [UserController::class, 'index'])
    ->middleware('auth:sanctum', 'ability:admin');
//Egy user lekérése    
Route::get('users/{id}', [UserController::class, 'show'])
    ->middleware('auth:sanctum', 'ability:admin');
//User adatok módosítása      
Route::patch('users/{id}', [UserController::class, 'update'])
->middleware('auth:sanctum', 'ability:admin');
//User törlés
Route::delete('users/{id}', [UserController::class, 'destroy'])
->middleware('auth:sanctum', 'ability:admin');  

//User self (Amit a user önmagával csinálhat) parancsok
Route::delete('usersme', [UserController::class, 'destroySelf'])
->middleware('auth:sanctum', 'ability:usersme:delete');

Route::patch('usersme', [UserController::class, 'updateSelf'])
->middleware('auth:sanctum', 'ability:usersme:patch');

Route::patch('usersmeupdatepassword', [UserController::class, 'updatePassword'])
->middleware('auth:sanctum', 'ability:usersme:updatePassword');

Route::get('usersme', [UserController::class, 'indexSelf'])
    ->middleware('auth:sanctum', 'ability:usersme:get'); 
//endregion

