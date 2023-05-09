<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssetsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BiensScannesController;
use App\Http\Controllers\CentreController;
use App\Http\Controllers\DemandeCompteController;
use App\Http\Controllers\EquipeController;
use App\Http\Controllers\LocaliteController;
use App\Http\Controllers\UniteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
 Here is where you can register API routes for your application. These routes are loaded by the RouteServiceProvider and all of them willbe assigned to the "api" middleware group. Make something great!*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');

Route::controller(AdminController::class)->middleware('auth:sanctum')->group(function(){
    Route::put('/acceptDemandeCompte/{id}','acceptDemandeCompte');
    Route::put('/refuseDemandeCompte/{id}','refuseDemandeCompte');
    Route::put('/deactivateUser/{id}','deactivateUser');
    Route::delete('/deleteUser/{id}','deleteUser');

});
 Route::controller(LocaliteController::class)->middleware('auth:sanctum')->group(function(){
 Route::get('/NotVisited_Localites','NotVisited_Localites');
});
Route::controller(BiensScannesController::class)->middleware('auth:sanctum')->group(function(){
    Route::get('/biensScannes','index');
    Route::get('/inventoryList','inventoryList');
    Route::get('/localiteVisite','localiteVisite');
});

// Route::post('/storeEquipe',[EquipeController::class,'store']);
Route::get('/getDemandes',[DemandeCompteController::class,'getDemandes']);
Route::get('/mail',[DemandeCompteController::class,'mail'])->middleware('auth:sanctum');


Route::get('/getUnite',[UniteController::class,'index']);
Route::get('/role',[AdminController::class,'role'])->middleware('auth:sanctum');

Route::get('/infrastructureUnite',[BiensScannesController::class,'infrastructureUnite']);
Route::get('/infrastructureCentre',[BiensScannesController::class,'infrastructureCentre']);
Route::get('/infrastructureLocalite',[BiensScannesController::class,'infrastructureLocalite']);
Route::get('/fill_LOC_ID_INIT',[AssetsController::class,'fill_LOC_ID_INIT']);


