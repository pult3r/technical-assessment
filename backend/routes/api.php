<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/generate-pdf', [PdfController::class, 'generate']);
Route::post('/register', [RegisterController::class, 'register']);
