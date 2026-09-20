<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

echo "--- BẮT ĐẦU TEST API ---\n";

// 1. Tạo user & lấy token
echo "1. Đăng ký & lấy Token...\n";
$userEmail = 'test' . rand(100, 9999) . '@test.com';
$response = Http::post('http://localhost/api/auth/register', [
    'name' => 'Admin Test',
    'email' => $userEmail,
    'password' => 'password123'
]);

if ($response->failed()) {
    echo "LỖI ĐĂNG KÝ: " . $response->body() . "\n";
    exit(1);
}

$token = $response->json('access_token');
echo "=> Lấy Token thành công!\n\n";

// 2. Test Tạo Bắp Nước (Snacks)
echo "2. Test tạo Bắp Nước (POST /admin/snacks)...\n";
$snackResponse = Http::withToken($token)->post('http://localhost/api/admin/snacks', [
    'name' => 'Combo Bắp Nước Siêu To',
    'price' => 150000,
    'is_active' => true
]);
if ($snackResponse->successful()) {
    echo "=> TẠO BẮP NƯỚC THÀNH CÔNG: " . $snackResponse->json('data.name') . "\n\n";
} else {
    echo "=> LỖI TẠO BẮP NƯỚC: " . $snackResponse->body() . "\n\n";
}

// 3. Test Lấy danh sách Bắp Nước (GET /snacks)
echo "3. Test lấy danh sách Bắp Nước (GET /snacks)...\n";
$listSnacks = Http::get('http://localhost/api/snacks');
if ($listSnacks->successful()) {
    $count = count($listSnacks->json('data'));
    echo "=> LẤY DANH SÁCH THÀNH CÔNG: Có $count món bắp nước.\n\n";
} else {
    echo "=> LỖI LẤY DANH SÁCH BẮP NƯỚC: " . $listSnacks->body() . "\n\n";
}

// 4. Test Tạo Voucher (POST /admin/vouchers)
echo "4. Test tạo Voucher (POST /admin/vouchers)...\n";
$voucherResponse = Http::withToken($token)->post('http://localhost/api/admin/vouchers', [
    'code' => 'GIAM' . rand(100, 9999),
    'discount_value' => 50000,
    'discount_type' => 'fixed',
    'min_order_value' => 200000,
    'max_uses_per_user' => 1,
    'valid_from' => date('Y-m-d H:i:s'),
    'valid_to' => date('Y-m-d H:i:s', strtotime('+30 days')),
    'is_active' => true
]);
if ($voucherResponse->successful()) {
    echo "=> TẠO VOUCHER THÀNH CÔNG: " . $voucherResponse->json('data.code') . "\n\n";
} else {
    echo "=> LỖI TẠO VOUCHER: " . $voucherResponse->body() . "\n\n";
}

// 5. Test Lấy thông tin phim từ TMDB (POST /admin/movies/fetch-tmdb)
// Do user có thể chưa cấu hình TMDB_API_KEY nên API này có thể báo lỗi thiếu key. Ta vẫn test thử logic.
echo "5. Test lấy phim TMDB (POST /admin/movies/fetch-tmdb)...\n";
$tmdbResponse = Http::withToken($token)->post('http://localhost/api/admin/movies/fetch-tmdb', [
    'tmdb_id' => '533535' // Deadpool & Wolverine
]);
if ($tmdbResponse->successful()) {
    echo "=> FETCH TMDB THÀNH CÔNG: " . $tmdbResponse->json('data.title') . "\n\n";
} else {
    echo "=> LỖI FETCH TMDB (Có thể do chưa setup TMDB_API_KEY trong .env): " . $tmdbResponse->body() . "\n\n";
}

echo "--- TEST HOÀN TẤT ---\n";
