<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\ShowController;
use App\Http\Controllers\SnackController;
use App\Http\Controllers\VoucherController;

// Auth Routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login'])->name('login');

// Public Routes
Route::get('/movies', [MovieController::class, 'index']);
Route::get('/movies/{id}', [MovieController::class, 'show']);
Route::get('/shows', [ShowController::class, 'index']);
Route::get('/shows/{id}/seats', [ShowController::class, 'seats']);
Route::get('/snacks', [SnackController::class, 'index']);

// Protected routes (Requires Sanctum token)
Route::middleware('auth:sanctum')->group(function () {
    // Booking Routes
    Route::post('/orders/hold', [BookingController::class, 'holdSeats']);
    Route::put('/orders/{id}/snacks', [BookingController::class, 'addSnacks']);
    Route::post('/orders/{id}/voucher', [BookingController::class, 'applyVoucher']);
    Route::post('/orders/{id}/pay', [BookingController::class, 'pay']);
    Route::get('/orders/history', [OrderController::class, 'history']);

    // Admin Routes
    Route::prefix('admin')->group(function () {
        Route::post('/movies/fetch-tmdb', [MovieController::class, 'fetchTmdb']);
        Route::get('/movies', [MovieController::class, 'index']);
        Route::post('/movies', [MovieController::class, 'store']);
        Route::put('/movies/{id}', [MovieController::class, 'update']);
        Route::delete('/movies/{id}', [MovieController::class, 'destroy']);
        
        Route::post('/shows', [ShowController::class, 'store']);
        Route::post('/snacks', [SnackController::class, 'store']);
        Route::post('/vouchers', [VoucherController::class, 'store']);
    });
});

// Webhooks
// Route::post('/webhooks/payment', [WebhookController::class, 'payment']);

// Staff Routes
// Route::post('/staff/tickets/scan', [TicketController::class, 'scan']);
