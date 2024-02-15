<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlanetController;
use App\Http\Controllers\CrewController;
use App\Http\Controllers\TechController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\VerifyToken;

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



// Route::post('/planet', [PlanetController::class, 'store']);``

Route::middleware(['verifytoken'])->group(function () {

    Route::get('/user', [AuthController::class, 'giveUserInfo']);
    Route::post('/user/edit/name', [AuthController::class, 'changeUserName']);
    Route::post('/user/edit/mail', [AuthController::class, 'changeUserMail']);
    Route::post('/user/edit/password', [AuthController::class, 'changeUserPasswd']);

    Route::get('/admin/planets', [PlanetController::class, 'index']);
    Route::post('/planet', [PlanetController::class, 'store']);
    Route::get('/admin/planet/{id}/destroy', [PlanetController::class, 'destroy']);
    Route::post('/admin/planet/{id}/edit', [PlanetController::class, 'update']);

    Route::get('/admin/crews', [CrewController::class, 'index']);
    Route::post('/crew', [CrewController::class, 'store']);
    Route::get('/admin/crew/{id}/destroy', [CrewController::class, 'destroy']);
    Route::post('/admin/crew/{id}/edit', [CrewController::class, 'update']);

    Route::get('/admin/teches', [TechController::class, 'index']);
    Route::post('/tech', [TechController::class, 'store']);
    Route::get('/admin/tech/{id}/destroy', [TechController::class, 'destroy']);
    Route::post('/admin/tech/{id}/edit', [TechController::class, 'update']);
});

// Route::post('/planet', [PlanetController::class, 'store'])->middleware('verifytoken');

Route::get('/planet/{id}', [PlanetController::class, 'show']);
Route::get('/planets', [PlanetController::class, 'indexForMenu']);

Route::get('/planetImg/{imgName}', [PlanetController::class, 'getImg']);

Route::get('/crew/{id}', [CrewController::class, 'show']);
Route::get('/crews', [CrewController::class, 'indexForMenu']);

Route::get('/crewImg/{imgName}', [CrewController::class, 'getImg']);

Route::get('/tech/{id}', [TechController::class, 'show']);
Route::get('/teches', [TechController::class, 'indexForMenu']);

Route::get('/techImg/{imgName}', [TechController::class, 'getImg']);


Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::post('/token', [AuthController::class, 'checkToken']);
