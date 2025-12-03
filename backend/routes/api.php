<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;

/*
|--------------------------------------------------------------------------
| Public routes (no JWT)
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

/*
|--------------------------------------------------------------------------
| Protected routes (JWT + MySQL audit user)
|--------------------------------------------------------------------------
|
| Order is IMPORTANT:
| 1. jwt.auth   -> validates token & extracts payload
| 2. mysql.user -> sets SQL variable @user_id for audit triggers
|
*/

Route::middleware(['jwt.auth', 'mysql.user'])->group(function () {
    Route::post('/generate-pdf', [PdfController::class, 'generate']);

    // Example future protected endpoints:
    // Route::put('/user/update', [UserController::class, 'update']);
    // Route::delete('/user/{id}', [UserController::class, 'delete']);
});
