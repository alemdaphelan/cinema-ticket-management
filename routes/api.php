<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\SnackController;
use App\Http\Controllers\VoucherController;

// Auth routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login'])->name('login');

// Public routes
Route::get('/snacks', [SnackController::class, 'index']);

// Protected admin routes (Requires Sanctum token)
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::post('/movies/fetch-tmdb', [MovieController::class, 'fetchTmdb']);
        Route::post('/snacks', [SnackController::class, 'store']);
        Route::post('/vouchers', [VoucherController::class, 'store']);
    });
});
