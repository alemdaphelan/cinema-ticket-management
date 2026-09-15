<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// === Trang chủ ===
Route::get('/', function () {
    return view('pages.home');
})->name('home');

// === Auth Web Routes ===
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('pages.auth.login');
    })->name('login');

    Route::post('/login', [AuthController::class, 'webLogin'])->name('login.submit');

    Route::get('/register', function () {
        return view('pages.auth.register');
    })->name('register');

    Route::post('/register', [AuthController::class, 'webRegister'])->name('register.submit');
});

Route::post('/logout', [AuthController::class, 'webLogout'])->name('logout')->middleware('auth');

// === Phim ===
Route::get('/movies/{id}', function (int $id) {
    return view('pages.movie-detail', ['movieId' => $id]);
})->name('movie.detail');

// === Lịch chiếu ===
Route::get('/schedule', function () {
    return view('pages.schedule');
})->name('schedule');

// === Booking (cần login) ===
Route::middleware('auth')->prefix('booking')->group(function () {
    Route::get('/seats/{showId}', function (int $showId) {
        return view('pages.booking.select-seats', ['showId' => $showId]);
    })->name('booking.seats');

    Route::get('/snacks/{orderId}', function (int $orderId) {
        return view('pages.booking.select-snacks', ['orderId' => $orderId]);
    })->name('booking.snacks');

    Route::get('/payment/{orderId}', function (int $orderId) {
        return view('pages.booking.payment', ['orderId' => $orderId]);
    })->name('booking.payment');

    Route::get('/success/{orderId}', function (int $orderId) {
        return view('pages.booking.success', ['orderId' => $orderId]);
    })->name('booking.success');

    Route::get('/history', function () {
        return view('pages.user.ticket-history');
    })->name('booking.history');
});

// === Admin (cần login + role admin) ===
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/movies', function () {
        return view('admin.movies.index');
    })->name('admin.movies');

    Route::get('/movies/create', function () {
        return view('admin.movies.create');
    })->name('admin.movies.create');

    Route::get('/movies/{id}/edit', function (int $id) {
        return view('admin.movies.edit', ['movieId' => $id]);
    })->name('admin.movies.edit');

    Route::get('/shows', function () {
        return view('admin.shows.index');
    })->name('admin.shows');

    Route::get('/shows/create', function () {
        return view('admin.shows.create');
    })->name('admin.shows.create');

    Route::get('/snacks', function () {
        return view('admin.snacks.index');
    })->name('admin.snacks');

    Route::get('/vouchers', function () {
        return view('admin.vouchers.index');
    })->name('admin.vouchers');

    Route::get('/orders', function () {
        return view('admin.orders.index');
    })->name('admin.orders');
});

// === Staff ===
Route::middleware('auth')->prefix('staff')->group(function () {
    Route::get('/scan', function () {
        return view('staff.scan');
    })->name('staff.scan');
});

// === Swagger API Docs ===
Route::get('/api/docs', function () {
    return view('swagger');
});

Route::get('/api/docs/yaml', function () {
    return response()->file(base_path('docs/swagger_api.yaml'));
});
