<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\DoadorController;
use App\Http\Controllers\TipoSanguineoController;
use App\Http\Controllers\HemocentroController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Rotas para AuthController
Route::post('/password/email', [AuthController::class, 'sendResetLink']);
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Rotas para AuthController
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rotas para UserController
    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'store']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);

    // Rotas para TipoSnguíneoController
    Route::apiResource('tipos-sanguineos', TipoSanguineoController::class)->only(['index']);

    // Rotas para DoadorController
    Route::get('/doadores', [DoadorController::class, 'index']);
    Route::post('/doadores', [DoadorController::class, 'store']);
    Route::get('/doadores/{id}', [DoadorController::class, 'show']);
    Route::put('/doadores/{id}', [DoadorController::class, 'update'])->middleware('check.user.type:1');
    Route::delete('/doadores/{id}', [DoadorController::class, 'destroy'])->middleware('check.user.type:1');

    // Rotas para HemocentroController
    Route::get('/hemocentros', [HemocentroController::class, 'index']);
    Route::post('/hemocentros', [HemocentroController::class, 'store']);
    Route::get('/hemocentros/{id}', [HemocentroController::class, 'show']);
    Route::put('/hemocentros/{id}', [HemocentroController::class, 'update']);
    Route::delete('/hemocentros/{id}', [HemocentroController::class, 'destroy']);

});
