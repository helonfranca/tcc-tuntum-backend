<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\DoadorController;
use App\Http\Controllers\TipoSanguineoController;
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
    Route::apiResource('users', UserController::class);

    // Rotas para TipoSnguíneoController
    Route::apiResource('tipos-sanguineos', TipoSanguineoController::class)->only(['index']);

    // Rotas para DoadorController
    Route::get('/doadores', [DoadorController::class, 'index'])->middleware('check.user.type:1');
    Route::post('/doadores', [DoadorController::class, 'store']);
    Route::get('/doadores/{id}', [DoadorController::class, 'show']);
    Route::put('/doadores/{id}', [DoadorController::class, 'update'])->middleware('check.user.type:1');
    Route::delete('/doadores/{id}', [DoadorController::class, 'destroy'])->middleware('check.user.type:1');
});
