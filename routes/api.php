<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
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
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

// === Public (không cần login) ===
Route::get('/test-db', function () {
    $times = [];
    
    $times['bootstrap'] = defined('LARAVEL_START') ? microtime(true) - LARAVEL_START : 0;

    $start = microtime(true);
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    $times['connect'] = microtime(true) - $start;

    $start = microtime(true);
    $count = \App\Models\Movie::count();
    $times['count'] = microtime(true) - $start;
    
    $start = microtime(true);
    $movies = \App\Models\Movie::query()->whereIn('status', ['showing', 'coming_soon'])->orderBy('created_at', 'desc')->paginate(12);
    $times['paginate'] = microtime(true) - $start;
    
    $start = microtime(true);
    $json = json_encode($movies);
    $times['json'] = microtime(true) - $start;

    $times['total_php'] = defined('LARAVEL_START') ? microtime(true) - LARAVEL_START : 0;

    return response()->json(['times' => $times, 'count' => $count]);
});
Route::get('/movies', [MovieController::class, 'publicIndex']);
Route::get('/movies/{id}', [MovieController::class, 'publicShow']);
Route::get('/movies/{id}/reviews', [ReviewController::class, 'index']);
Route::get('/shows', [ShowController::class, 'publicIndex']);
Route::get('/shows/{id}/seats', [ShowController::class, 'getSeats']);
Route::get('/snacks', [SnackController::class, 'publicIndex']);

// === Protected routes (Requires Sanctum token) ===
Route::middleware('auth:sanctum')->group(function () {
    // Booking
    Route::prefix('orders')->group(function () {
        Route::post('/hold', [BookingController::class, 'holdSeats']);
        Route::put('/{id}/snacks', [BookingController::class, 'addSnacks']);
        Route::post('/{id}/voucher', [BookingController::class, 'applyVoucher']);
        Route::post('/{id}/pay', [BookingController::class, 'pay']);
        Route::post('/{id}/cancel', [BookingController::class, 'cancelOrder']);
        Route::get('/history', [BookingController::class, 'orderHistory']);
        Route::get('/{id}', [BookingController::class, 'orderDetail']);
    });

    // Reviews
    Route::post('/movies/{id}/reviews', [ReviewController::class, 'store']);

    // Staff
    Route::middleware([RoleMiddleware::class . ':staff,admin'])->prefix('staff')->group(function () {
        Route::post('/tickets/scan', [StaffController::class, 'scanTicket']);
    });

    // Admin
    Route::middleware([RoleMiddleware::class . ':admin'])->prefix('admin')->group(function () {
        // Movies
        Route::get('/movies', [MovieController::class, 'index']);
        Route::post('/movies', [MovieController::class, 'store']);
        Route::put('/movies/{id}', [MovieController::class, 'update']);
        Route::delete('/movies/{id}', [MovieController::class, 'destroy']);
        Route::post('/movies/fetch-tmdb', [MovieController::class, 'fetchTmdbList']);

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
});