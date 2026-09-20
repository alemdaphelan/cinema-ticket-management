<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Movie;

$movies = [
    [
        'tmdb_id' => 101,
        'title' => 'Avengers: Endgame',
        'description' => 'Sau những sự kiện tàn khốc của Infinity War, vũ trụ đang chìm trong đống đổ nát. Với sự giúp đỡ của các đồng minh còn sót lại, Avengers tập hợp một lần nữa để đảo ngược hành động của Thanos.',
        'poster_url' => 'https://upload.wikimedia.org/wikipedia/en/0/0d/Avengers_Endgame_poster.jpg',
        'release_date' => '2019-04-24',
        'status' => 'showing',
        'genre' => 'Hành Động, Viễn Tưởng',
        'duration_minutes' => 181,
        'teaser_url' => 'https://www.youtube.com/embed/TcMBFSGVi1c'
    ],
    [
        'tmdb_id' => 102,
        'title' => 'Spider-Man: No Way Home',
        'description' => 'Lần đầu tiên trong lịch sử điện ảnh của Người Nhện, thân phận của anh hùng hàng xóm thân thiện của chúng ta bị tiết lộ.',
        'poster_url' => 'https://image.tmdb.org/t/p/w500/1g0dhYtq4irTY1R80vFA1REviy.jpg',
        'release_date' => '2021-12-15',
        'status' => 'showing',
        'genre' => 'Hành Động, Viễn Tưởng',
        'duration_minutes' => 148,
        'teaser_url' => 'https://www.youtube.com/embed/JfVOs4VSpmA'
    ],
    [
        'tmdb_id' => 103,
        'title' => 'The Batman',
        'description' => 'Trong năm thứ hai chiến đấu với tội phạm, Batman khám phá ra nạn tham nhũng ở thành phố Gotham.',
        'poster_url' => 'https://upload.wikimedia.org/wikipedia/en/f/ff/The_Batman_%28film%29_poster.jpg',
        'release_date' => '2022-03-01',
        'status' => 'showing',
        'genre' => 'Hành Động, Tội Phạm',
        'duration_minutes' => 176,
        'teaser_url' => 'https://www.youtube.com/embed/mqqft2x_Aa4'
    ],
    [
        'tmdb_id' => 104,
        'title' => 'Dune: Part Two',
        'description' => 'Paul Atreides đoàn kết với Chani và Fremen trong khi trên con đường trả thù những kẻ âm mưu đã tiêu diệt gia đình anh.',
        'poster_url' => 'https://upload.wikimedia.org/wikipedia/en/5/52/Dune_Part_Two_poster.jpeg',
        'release_date' => '2024-02-28',
        'status' => 'showing',
        'genre' => 'Hành Động, Viễn Tưởng',
        'duration_minutes' => 166,
        'teaser_url' => 'https://www.youtube.com/embed/Way9Dexny3w'
    ],
    [
        'tmdb_id' => 105,
        'title' => 'Joker',
        'description' => 'Một diễn viên hài thất bại và bị xã hội ruồng bỏ bắt đầu hành trình từ từ chìm vào sự điên loạn và trở thành một kẻ chủ mưu tội phạm.',
        'poster_url' => 'https://upload.wikimedia.org/wikipedia/en/e/e1/Joker_%282019_film%29_poster.jpg',
        'release_date' => '2019-10-02',
        'status' => 'showing',
        'genre' => 'Tâm Lý, Tội Phạm',
        'duration_minutes' => 122,
        'teaser_url' => 'https://www.youtube.com/embed/zAGVQLHvwOY'
    ],
    [
        'tmdb_id' => 106,
        'title' => 'Oppenheimer',
        'description' => 'Câu chuyện về nhà vật lý học người Mỹ J. Robert Oppenheimer và vai trò của ông trong việc phát triển bom nguyên tử.',
        'poster_url' => 'https://upload.wikimedia.org/wikipedia/en/4/4a/Oppenheimer_%28film%29.jpg',
        'release_date' => '2023-07-19',
        'status' => 'showing',
        'genre' => 'Tâm Lý, Lịch Sử',
        'duration_minutes' => 180,
        'teaser_url' => 'https://www.youtube.com/embed/uYPbbksJxIg'
    ],
    [
        'tmdb_id' => 107,
        'title' => 'Avatar: The Way of Water',
        'description' => 'Hơn mười năm sau các sự kiện của phần phim đầu tiên, Jake Sully sống cùng gia đình mới của mình tại hành tinh Pandora.',
        'poster_url' => 'https://upload.wikimedia.org/wikipedia/en/5/54/Avatar_The_Way_of_Water_poster.jpg',
        'release_date' => '2022-12-14',
        'status' => 'showing',
        'genre' => 'Hành Động, Viễn Tưởng',
        'duration_minutes' => 192,
        'teaser_url' => 'https://www.youtube.com/embed/d9MyW72ELq0'
    ],
    [
        'tmdb_id' => 108,
        'title' => 'John Wick: Chapter 4',
        'description' => 'John Wick khám phá ra một con đường để đánh bại High Table.',
        'poster_url' => 'https://upload.wikimedia.org/wikipedia/en/d/d0/John_Wick_-_Chapter_4_promotional_poster.jpg',
        'release_date' => '2023-03-22',
        'status' => 'showing',
        'genre' => 'Hành Động, Tội Phạm',
        'duration_minutes' => 169,
        'teaser_url' => 'https://www.youtube.com/embed/qEVUtrk8_B4'
    ],
    [
        'tmdb_id' => 109,
        'title' => 'Mission: Impossible - Dead Reckoning',
        'description' => 'Ethan Hunt và nhóm IMF của anh dấn thân vào nhiệm vụ nguy hiểm nhất từ trước đến nay.',
        'poster_url' => 'https://upload.wikimedia.org/wikipedia/en/e/ed/Mission-_Impossible_%E2%80%93_Dead_Reckoning_Part_One_poster.jpg',
        'release_date' => '2023-07-08',
        'status' => 'coming_soon',
        'genre' => 'Hành Động, Phiêu Lưu',
        'duration_minutes' => 163,
        'teaser_url' => 'https://www.youtube.com/embed/avz06PDqDbM'
    ],
    [
        'tmdb_id' => 110,
        'title' => 'Deadpool & Wolverine',
        'description' => 'Wolverine đang hồi phục khỏi vết thương và đối mặt với Deadpool để đánh bại một kẻ thù chung.',
        'poster_url' => 'https://upload.wikimedia.org/wikipedia/en/4/4c/Deadpool_%26_Wolverine_poster.jpg',
        'release_date' => '2024-07-24',
        'status' => 'coming_soon',
        'genre' => 'Hành Động, Hài Hước',
        'duration_minutes' => 127,
        'teaser_url' => 'https://www.youtube.com/embed/73_1biulkYk'
    ],
    [
        'tmdb_id' => 111,
        'title' => 'Inside Out 2',
        'description' => 'Bộ phim tiếp nối câu chuyện về Riley và những cảm xúc bên trong đầu cô bé.',
        'poster_url' => 'https://image.tmdb.org/t/p/w500/vpnVM9B6NMmQpWeZvzRxMgRU25I.jpg',
        'release_date' => '2024-06-12',
        'status' => 'coming_soon',
        'genre' => 'Hoạt Hình, Gia Đình',
        'duration_minutes' => 100,
        'teaser_url' => 'https://www.youtube.com/embed/LEjhY15eCx0'
    ],
    [
        'tmdb_id' => 112,
        'title' => 'Kung Fu Panda 4',
        'description' => 'Po chuẩn bị trở thành thủ lĩnh tinh thần của Thung lũng Hòa bình.',
        'poster_url' => 'https://image.tmdb.org/t/p/w500/xV26vVd51y6xYQ4pG0rM2xTz5Hh.jpg',
        'release_date' => '2024-03-02',
        'status' => 'coming_soon',
        'genre' => 'Hoạt Hình, Hành Động',
        'duration_minutes' => 94,
        'teaser_url' => 'https://www.youtube.com/embed/_inKs4eeHiI'
    ]
];

$count = 0;
foreach ($movies as $movie) {
    Movie::updateOrCreate(
        ['tmdb_id' => $movie['tmdb_id']],
        $movie
    );
    $count++;
}

echo "Da cap nhat thanh cong $count phim mau vao database voi day du URL hinh anh!";

