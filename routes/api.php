<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndicateurQualiController;
use App\Http\Controllers\IndicateurQuantiController;
use App\Http\Controllers\PallierController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AnneeController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\SemestreController;
use App\Http\Controllers\OutilsController;
use App\Http\Controllers\ObjectifController;
use App\Http\Controllers\PrestataireController;
use App\Http\Controllers\RAController;
use App\Http\Controllers\RealisationCCController;

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
Route::post('import',[ExportController::class,'import']);
Route::controller(UsersController::class)->prefix('Sonatel_dv')->group(function(){
    Route::post('login/user','login');
    Route::post('registre/user','registrer');
    Route::post('updatePassword/user','updatePassword');
});
Route::middleware('auth:sanctum')->prefix('Sonatel_dv')->group(function(){
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::controller(IndicateurQualiController::class)->group(function(){
        Route::post('create/quali','store');
        Route::put('update/quali/{indicateurQuali}','update');
    });
    Route::controller(IndicateurQuantiController::class)->group(function(){
        Route::post('create/quanti','store');
        Route::get('index/indicateur','index');
        Route::put('update/quanti/{indicateurQuanti}','update');
    });
    Route::controller(RAController::class)->group(function(){
        Route::post('create/ra','store');
       // Route::delete('delete/ra/{id}/{annee}','delete');
       Route::get('index/ra','index');
       Route::get('declinaison/ra','declinaison');
       Route::get('all/ras','ra');
       Route::post('demande/ra/{id}','demandeTraitement');
    });
    Route::controller(PallierController::class)->group(function(){
        Route::post('create/pallier','store');
        Route::put('update/pallier/{pallier}','update');
    });
    Route::controller(UsersController::class)->group(function(){
        Route::post('create/user','store');
        Route::get('index/user/{id}','indexCC');
        Route::post('objectif/update/{id}','updateObjectif');
        Route::get('getra/{id}','getRa');
        Route::post('objectif/update','update');
        Route::get('logout/user','logout');
    });
    Route::controller(RoleController::class)->group(function(){
        Route::post('create/role','store');
    });
    Route::controller(AnneeController::class)->group(function(){
        Route::post('create/annee','store');
    });
    Route::controller(SemestreController::class)->group(function(){
        Route::post('create/semestre','store');
    });
    Route::controller(OutilsController::class)->group(function(){
        Route::post('create/outil','store');
        Route::get('index/outil','index');
    });
    Route::controller(ObjectifController::class)->group(function(){
        Route::post('create/objectif','store');
        Route::get('index/objectif','index');
        Route::delete('delete/objectif/{id}/{annee}','delete');     
    });
    Route::controller(RealisationCCController::class)->group(function(){
        // Route::post('create/ra','store');
        Route::post('chargement/request','chargement');
        Route::get('index/realisation','index');
        
    });
    Route::controller(PrestataireController::class)->group(function(){
        Route::post('prestataire','store');
        Route::get('prestataire/all/{libelle}','index');
    });
});
