<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\ShowController;
use App\Http\Controllers\SnackController;
use App\Http\Controllers\VoucherController;

// Auth Routes (Assuming AuthController exists or will be created)
// Route::post('/auth/login', [AuthController::class, 'login']);
// Route::post('/auth/register', [AuthController::class, 'register']);

// Public Routes
Route::get('/movies', [MovieController::class, 'index']);
Route::get('/movies/{id}', [MovieController::class, 'show']);

Route::get('/shows', [ShowController::class, 'index']);
Route::get('/shows/{id}/seats', [ShowController::class, 'seats']);

Route::get('/snacks', [SnackController::class, 'index']);

// Booking Routes
Route::post('/orders/hold', [BookingController::class, 'holdSeats']);
Route::put('/orders/{id}/snacks', [BookingController::class, 'addSnacks']);
Route::post('/orders/{id}/voucher', [BookingController::class, 'applyVoucher']);
Route::post('/orders/{id}/pay', [BookingController::class, 'pay']);
Route::get('/orders/history', [OrderController::class, 'history']);

// Webhooks
// Route::post('/webhooks/payment', [WebhookController::class, 'payment']);

// Staff Routes
// Route::post('/staff/tickets/scan', [TicketController::class, 'scan']);

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
