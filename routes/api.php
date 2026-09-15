<?php

<<<<<<< HEAD
=======
use Illuminate\Support\Facades\Route;
>>>>>>> 6d96c5f588b159ad8f9acbbb71f865b15c9dbfa8
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\OrderController;
<<<<<<< HEAD
use App\Http\Controllers\ShowController;
use App\Http\Controllers\SnackController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\VoucherController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// === Auth ===
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

// === Public (không cần login) ===
Route::get('/movies', [MovieController::class, 'publicIndex']);
Route::get('/movies/{id}', [MovieController::class, 'publicShow']);
Route::get('/shows', [ShowController::class, 'publicIndex']);
Route::get('/shows/{id}/seats', [ShowController::class, 'getSeats']);
Route::get('/snacks', [SnackController::class, 'publicIndex']);

// === Booking (cần login user) ===
Route::middleware('auth')->prefix('orders')->group(function () {
    Route::post('/hold', [BookingController::class, 'holdSeats']);
    Route::put('/{id}/snacks', [BookingController::class, 'addSnacks']);
    Route::post('/{id}/voucher', [BookingController::class, 'applyVoucher']);
    Route::post('/{id}/pay', [BookingController::class, 'pay']);
    Route::get('/history', [BookingController::class, 'orderHistory']);
    Route::get('/{id}', [BookingController::class, 'orderDetail']);
});

// === Staff (cần login + role staff/admin) ===
Route::middleware(['auth', RoleMiddleware::class . ':staff,admin'])->prefix('staff')->group(function () {
    Route::post('/tickets/scan', [StaffController::class, 'scanTicket']);
});

// === Admin ===
Route::middleware(['auth', RoleMiddleware::class . ':admin'])->prefix('admin')->group(function () {
    // Movies
    Route::get('/movies', [MovieController::class, 'index']);
    Route::post('/movies', [MovieController::class, 'store']);
    Route::put('/movies/{id}', [MovieController::class, 'update']);
    Route::delete('/movies/{id}', [MovieController::class, 'destroy']);
    Route::post('/movies/fetch-tmdb', [MovieController::class, 'fetchFromTmdb']);

    // Shows
    Route::get('/shows', [ShowController::class, 'index']);
    Route::post('/shows', [ShowController::class, 'store']);
    Route::put('/shows/{id}', [ShowController::class, 'update']);
    Route::delete('/shows/{id}', [ShowController::class, 'destroy']);

    // Snacks
    Route::get('/snacks', [SnackController::class, 'index']);
    Route::post('/snacks', [SnackController::class, 'store']);
    Route::put('/snacks/{id}', [SnackController::class, 'update']);
    Route::delete('/snacks/{id}', [SnackController::class, 'destroy']);

    // Vouchers
    Route::get('/vouchers', [VoucherController::class, 'index']);
    Route::post('/vouchers', [VoucherController::class, 'store']);
    Route::put('/vouchers/{id}', [VoucherController::class, 'update']);
    Route::delete('/vouchers/{id}', [VoucherController::class, 'destroy']);

    // Orders & Statistics
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/statistics', [OrderController::class, 'statistics']);
});
=======
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
>>>>>>> 6d96c5f588b159ad8f9acbbb71f865b15c9dbfa8
