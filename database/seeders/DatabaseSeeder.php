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
        $rooms = [
            ['name' => 'Phòng 1 - Standard'],
            ['name' => 'Phòng 2 - Premium'],
            ['name' => 'Phòng 3 - VIP'],
        ];

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

        // === Movies (Mock Data) ===
        $movies = [
            [
                'title' => 'Lật Mặt 8: Vòng Tay Nắng',
                'director' => 'Lý Hải',
                'poster_url' => '/images/posters/movie_1.png',
                'teaser_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'duration_minutes' => 130,
                'status' => 'showing',
                'description' => 'Phần tiếp theo của loạt phim ăn khách Lật Mặt. Câu chuyện về tình cảm gia đình, những mâu thuẫn và sự hy sinh.',
                'genre' => 'Tâm lý, Gia đình',
            ],
            [
                'title' => 'Mai',
                'director' => 'Trấn Thành',
                'poster_url' => '/images/posters/movie_2.png',
                'teaser_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'duration_minutes' => 131,
                'status' => 'showing',
                'description' => 'Câu chuyện về Mai - một cô gái massage xinh đẹp với quá khứ đau thương, tìm kiếm tình yêu đích thực giữa Sài Gòn hoa lệ.',
                'genre' => 'Tâm lý, Tình cảm',
            ],
            [
                'title' => 'Avengers: Doomsday',
                'director' => 'Joe Russo, Anthony Russo',
                'poster_url' => '/images/posters/movie_3.png',
                'teaser_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'duration_minutes' => 155,
                'status' => 'showing',
                'description' => 'Các siêu anh hùng Avengers phải đối đầu với mối đe dọa lớn nhất từ trước tới nay khi Doctor Doom xuất hiện.',
                'genre' => 'Hành động, Siêu anh hùng',
            ],
            [
                'title' => 'Inside Out 3',
                'director' => 'Kelsey Mann',
                'poster_url' => '/images/posters/movie_4.png',
                'teaser_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'duration_minutes' => 100,
                'status' => 'showing',
                'description' => 'Riley bước vào tuổi trưởng thành với những cảm xúc mới lạ và phức tạp hơn bao giờ hết.',
                'genre' => 'Hoạt hình, Gia đình',
            ],
            [
                'title' => 'Dune: Part Three',
                'director' => 'Denis Villeneuve',
                'poster_url' => '/images/posters/movie_5.png',
                'teaser_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'duration_minutes' => 166,
                'status' => 'showing',
                'description' => 'Paul Atreides tiếp tục hành trình chinh phục vũ trụ và đối mặt với những thử thách cam go nhất.',
                'genre' => 'Khoa học viễn tưởng, Phiêu lưu',
            ],
            [
                'title' => 'Người Vợ Cuối Cùng',
                'director' => 'Victor Vũ',
                'poster_url' => '/images/posters/movie_6.png',
                'teaser_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'duration_minutes' => 115,
                'status' => 'showing',
                'description' => 'Câu chuyện tình yêu bi thương thời phong kiến Việt Nam, nơi người phụ nữ phải đấu tranh cho hạnh phúc của mình.',
                'genre' => 'Tâm lý, Lịch sử',
            ],
            [
                'title' => 'The Batman 2',
                'director' => 'Matt Reeves',
                'poster_url' => '/images/posters/movie_7.png',
                'teaser_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'duration_minutes' => 150,
                'status' => 'coming_soon',
                'description' => 'Bruce Wayne tiếp tục hành trình bảo vệ Gotham khỏi những tội phạm nguy hiểm nhất.',
                'genre' => 'Hành động, Tội phạm',
            ],
            [
                'title' => 'Tết Ở Làng Địa Ngục 2',
                'director' => 'Trần Hữu Tấn',
                'poster_url' => '/images/posters/movie_8.png',
                'teaser_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'duration_minutes' => 110,
                'status' => 'coming_soon',
                'description' => 'Phần tiếp theo của bộ phim kinh dị Việt Nam ăn khách. Những bí ẩn đáng sợ tại ngôi làng hẻo lánh.',
                'genre' => 'Kinh dị, Tâm lý',
            ],
            [
                'title' => 'Spider-Man: Brand New Day',
                'director' => 'Destin Daniel Cretton',
                'poster_url' => '/images/posters/movie_9.png',
                'teaser_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'duration_minutes' => 140,
                'status' => 'coming_soon',
                'description' => 'Peter Parker bắt đầu một chương mới trong cuộc đời siêu anh hùng với những thử thách chưa từng có.',
                'genre' => 'Hành động, Siêu anh hùng',
            ],
            [
                'title' => 'Đào, Phở và Piano 2',
                'director' => 'Phi Tiến Sơn',
                'poster_url' => '/images/posters/movie_10.png',
                'teaser_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'duration_minutes' => 105,
                'status' => 'coming_soon',
                'description' => 'Tiếp nối câu chuyện lãng mạn và hào hùng về Hà Nội những ngày đầu kháng chiến.',
                'genre' => 'Tâm lý, Lịch sử, Tình cảm',
            ],
        ];

        foreach ($movies as $movieData) {
            Movie::create($movieData);
        }

        // === Shows (3 ngày tới) ===
        $showingMovies = Movie::where('status', 'showing')->get();
        $allRooms = Room::all();
        $times = ['08:00', '10:30', '13:00', '15:30', '18:00', '20:30'];
        $prices = [75000, 85000, 95000];

        for ($day = 0; $day < 3; $day++) {
            $date = now()->addDays($day)->format('Y-m-d');

            foreach ($allRooms as $roomIndex => $room) {
                foreach (array_slice($times, 0, 3) as $timeIndex => $time) {
                    $movieIndex = ($roomIndex * 3 + $timeIndex + $day) % $showingMovies->count();
                    $movie = $showingMovies[$movieIndex];

                    $startTime = \Carbon\Carbon::parse("{$date} {$time}");
                    $endTime = $startTime->copy()->addMinutes($movie->duration_minutes + 20);

                    Show::create([
                        'movie_id' => $movie->id,
                        'room_id' => $room->id,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'price' => $prices[$roomIndex] ?? 75000,
                    ]);
                }
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
