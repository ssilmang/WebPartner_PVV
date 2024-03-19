<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndicateurQualiController;
use App\Http\Controllers\IndicateurQuantiController;
use App\Http\Controllers\PallierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AnneeController;
use App\Http\Controllers\SemestreController;
use App\Http\Controllers\OutilController;
use App\Http\Controllers\ObjectifController;
use App\Http\Controllers\RAController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::controller(IndicateurQualiController::class)->prefix('Sonatel_dv')->group(function(){
    Route::post('create/quali','store');
    Route::put('update/quali/{indicateurQuali}','update');
});
Route::controller(IndicateurQuantiController::class)->prefix('Sonatel_dv')->group(function(){
    Route::post('create/quanti','store');
    Route::get('index/indicateur','index');
    Route::put('update/quanti/{indicateurQuanti}','update');
});
Route::controller(PallierController::class)->prefix('Sonatel_dv')->group(function(){
    Route::post('create/pallier','store');
    Route::put('update/pallier/{pallier}','update');
});
Route::controller(UserController::class)->prefix('Sonatel_dv')->group(function(){
    Route::post('create/user','store');
    Route::get('index/user/{id}','indexCC');
    Route::post('objectif/update/{id}','updateObjectif');
});
Route::controller(RoleController::class)->prefix('Sonatel_dv')->group(function(){
    Route::post('create/role','store');
});
Route::controller(AnneeController::class)->prefix('Sonatel_dv')->group(function(){
    Route::post('create/annee','store');
});
Route::controller(SemestreController::class)->prefix('Sonatel_dv')->group(function(){
    Route::post('create/semestre','store');
});
Route::controller(OutilController::class)->prefix('Sonatel_dv')->group(function(){
    Route::post('create/outil','store');
    Route::get('index/outil','index');
});
Route::controller(ObjectifController::class)->prefix('Sonatel_dv')->group(function(){
    Route::post('create/objectif','store');
    Route::get('index/objectif','index');
    Route::delete('delete/objectif/{id}/{annee}','delete');
   
    
});
Route::controller(RAController::class)->prefix('Sonatel_dv')->group(function(){
    // Route::post('create/ra','store');
    // Route::delete('delete/ra/{id}/{annee}','delete');
    Route::get('index/ra','index');
    
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
