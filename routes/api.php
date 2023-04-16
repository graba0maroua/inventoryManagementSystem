<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BiensScannesController;
use App\Http\Controllers\CentreController;
use App\Http\Controllers\UniteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
 Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
*/
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// ->middleware('auth:sanctum')
 Route::controller(BiensScannesController::class)->middleware('auth:sanctum')->group(function(){
    Route::get('/localitesNonVisites','localitesNonVisites');
    Route::get('/localitesVisites','localitesVisites');
    Route::get('/biensScannes','index');
    // Route::get('/liste/{id}','liste');
    Route::get('/listeInventairesScannes','listeInventairesScannes');
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



