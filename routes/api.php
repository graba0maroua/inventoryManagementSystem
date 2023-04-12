<?php

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
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/register',[AuthController::class,'register']);
 Route::post('/login',[AuthController::class,'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

 Route::get('/unites',[UniteController::class,'index']); //GetAllUnites
 Route::get('/UCM/{id}',[UniteController::class,'GetLOC_by_UCM']); //Getalllocalites by Unite

 Route::get('/COP/LOC',[CentreController::class,'GetLOC']); //GetAlllocalities in centers

 Route::controller(BiensScannesController::class)->group(function(){
    Route::get('/localitesNonVisites','localitesNonVisites');
    Route::get('/localitesVisites','localitesVisites');
    Route::get('/biensScannes','index');

});



