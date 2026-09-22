<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\Order;
use App\Models\Room;
use App\Models\Seat;
use App\Models\Show;
use App\Models\Snack;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // === Users ===
        User::create([
            'name' => 'Admin Cinema',
            'email' => 'admin@cinema.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Nhân viên Nguyễn Văn A',
            'email' => 'staff@cinema.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        User::create([
            'name' => 'Khách hàng Trần Thị B',
            'email' => 'user@cinema.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // === Rooms & Seats ===
        $rooms = [];
        for ($i = 1; $i <= 10; $i++) {
            $rooms[] = ['name' => "Phòng $i - " . ($i % 3 === 0 ? 'VIP' : 'Standard')];
        }

        foreach ($rooms as $roomData) {
            $room = Room::create($roomData);

            // 8 hàng (A-H) × 10 ghế, hàng G-H là VIP
            $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
            foreach ($rows as $row) {
                for ($seatNum = 1; $seatNum <= 10; $seatNum++) {
                    Seat::create([
                        'room_id' => $room->id,
                        'row_label' => $row,
                        'seat_number' => $seatNum,
                        'type' => in_array($row, ['G', 'H']) ? 'vip' : 'standard',
                    ]);
                }
            }
        }

        // === Movies ===
        // Thay vì dùng data giả, ta sẽ kéo thẳng từ TMDB bằng command vừa tạo
        \Illuminate\Support\Facades\Artisan::call('movies:fetch-tmdb');

        // === Shows (3 ngày tới) ===
        $showingMovies = Movie::where('status', 'showing')->get();
        $allRooms = Room::all();
        $times = ['08:00', '10:30', '13:00', '15:30', '18:00', '20:30'];
        $prices = [75000, 85000, 95000, 100000, 120000];

        // Assign exactly 1 or 2 shows per movie per day, avoiding room conflicts
        for ($day = 0; $day < 3; $day++) {
            $date = now()->addDays($day)->format('Y-m-d');
            $slotIndex = 0;
            $maxSlots = $allRooms->count() * count($times);

            foreach ($showingMovies as $movie) {
                // Each movie gets 1 show per day, unless we run out of slots
                if ($slotIndex >= $maxSlots) {
                    break; 
                }

                $roomIndex = floor($slotIndex / count($times));
                $timeIndex = $slotIndex % count($times);
                $room = $allRooms[$roomIndex];
                $time = $times[$timeIndex];

                $startTime = \Carbon\Carbon::parse("{$date} {$time}");
                $endTime = $startTime->copy()->addMinutes($movie->duration_minutes + 20);

                Show::create([
                    'movie_id' => $movie->id,
                    'room_id' => $room->id,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'price' => $prices[$roomIndex % count($prices)] ?? 75000,
                ]);

                $slotIndex++;
            }
        }

        // === Snacks ===
        $snacks = [
            ['name' => 'Combo 1 - Bắp nhỏ + Nước ngọt', 'price' => 49000, 'image_url' => '', 'is_active' => true],
            ['name' => 'Combo 2 - Bắp lớn + 2 Nước ngọt', 'price' => 79000, 'image_url' => '', 'is_active' => true],
            ['name' => 'Combo Đôi - 2 Bắp lớn + 2 Nước ngọt', 'price' => 109000, 'image_url' => '', 'is_active' => true],
            ['name' => 'Combo Gia đình - 2 Bắp lớn + 4 Nước ngọt', 'price' => 159000, 'image_url' => '', 'is_active' => true],
            ['name' => 'Nước ngọt Pepsi', 'price' => 29000, 'image_url' => '', 'is_active' => true],
            ['name' => 'Nước suối Aquafina', 'price' => 15000, 'image_url' => '', 'is_active' => true],
            ['name' => 'Bắp rang caramel', 'price' => 45000, 'image_url' => '', 'is_active' => true],
            ['name' => 'Hotdog xúc xích', 'price' => 39000, 'image_url' => '', 'is_active' => true],
        ];

        foreach ($snacks as $snackData) {
            Snack::create($snackData);
        }

        // === Vouchers ===
        $vouchers = [
            [
                'code' => 'WELCOME50',
                'discount_value' => 50000,
                'discount_type' => 'fixed',
                'min_order_value' => 200000,
                'max_uses_per_user' => 1,
                'valid_from' => now(),
                'valid_to' => now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'code' => 'GIAM10PT',
                'discount_value' => 10,
                'discount_type' => 'percent',
                'min_order_value' => 150000,
                'max_uses_per_user' => 2,
                'valid_from' => now(),
                'valid_to' => now()->addMonths(1),
                'is_active' => true,
            ],
            [
                'code' => 'SINHVIEN30K',
                'discount_value' => 30000,
                'discount_type' => 'fixed',
                'min_order_value' => 100000,
                'max_uses_per_user' => 1,
                'valid_from' => now(),
                'valid_to' => now()->addMonths(6),
                'is_active' => true,
            ],
        ];

        foreach ($vouchers as $voucherData) {
            Voucher::create($voucherData);
        }
    }
}
