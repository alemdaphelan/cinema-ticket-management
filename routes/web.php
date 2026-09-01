<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route hiển thị giao diện Swagger UI
Route::get('/api/docs', function () {
    return view('swagger');
});

// Route để Swagger UI đọc nội dung file swagger_api.yaml
Route::get('/api/docs/yaml', function () {
    return response()->file(base_path('docs/swagger_api.yaml'));
});
