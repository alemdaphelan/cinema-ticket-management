<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3309;dbname=cinema_ticket_management', 'root', 'secret');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== 1. CẬP NHẬT THỂ LOẠI CHO TOÀN BỘ PHIM ===\n";

$genreMap = [
    1 => 'Hành Động, Tội Phạm, Giật Gân',
    2 => 'Kinh Dị, Hành Động, Viễn Tưởng',
    3 => 'Hoạt Hình, Hài Hước, Gia Đình',
    4 => 'Phiêu Lưu, Sử Thi, Thần Thoại',
    5 => 'Kinh Dị, Hành Động, Giật Gân',
    6 => 'Hành Động, Giật Gân',
    7 => 'Hoạt Hình, Phiêu Lưu, Âm Nhạc',
    8 => 'Hành Động, Tội Phạm, Hình Sự',
    9 => 'Tình Cảm, Lãng Mạn, Tâm Lý',
    10 => 'Hành Động, Phiêu Lưu',
    11 => 'Kinh Dị, Bí Ẩn, Giật Gân',
    12 => 'Kinh Dị, Giật Gân',
    13 => 'Hoạt Hình, Hài Hước, Phiêu Lưu',
    14 => 'Hài Hước, Gia Đình, Tâm Lý',
    15 => 'Hành Động, Gay Cấn, Thảm Họa',
    16 => 'Kinh Dị, Bí Ẩn, Siêu Nhiên',
    17 => 'Khoa Học Viễn Tưởng, Sinh Tồn',
    18 => 'Hoạt Hình, Phiêu Lưu, Gia Đình',
    19 => 'Hành Động, Giật Gân',
    20 => 'Kinh Dị, Giật Gân, Sinh Tồn',
    21 => 'Hài Hước, Gia Đình, Hành Động',
    22 => 'Tâm Lý, Chính Kịch',
    23 => 'Kinh Dị, Bí Ẩn, Giật Gân',
    24 => 'Khoa Học Viễn Tưởng, Hành Động',
    25 => 'Hành Động, Khoa Học Viễn Tưởng, Phiêu Lưu',
    26 => 'Tình Cảm, Lãng Mạn',
    27 => 'Hành Động, Võ Thuật, Cổ Trang',
    28 => 'Tình Cảm, Lãng Mạn, Tâm Lý',
    29 => 'Kinh Dị, Hài Đen, Giật Gân',
    30 => 'Tâm Lý, Tội Phạm, Kịch Tính',
    31 => 'Tài Liệu, Tâm Lý',
    32 => 'Kinh Dị, Bí Ẩn, Siêu Nhiên',
    33 => 'Tâm Lý, Hình Sự, Tội Phạm',
    34 => 'Hành Động, Sinh Tồn, Giật Gân',
    35 => 'Hài Hước, Tình Cảm',
    36 => 'Kinh Dị, Giật Gân',
    37 => 'Hoạt Hình, Phiêu Lưu, Giả Tưởng',
    38 => 'Tình Cảm, Hài Hước',
    39 => 'Tâm Lý, Giật Gân',
    40 => 'Hành Động, Phiêu Lưu, Lịch Sử',
    41 => 'Tâm Lý, Giật Gân',
    42 => 'Hài Hước, Tội Phạm',
    43 => 'Tâm Lý, Gia Đình',
    44 => 'Hành Động, Giật Gân',
    45 => 'Tâm Lý, Tình Cảm',
    46 => 'Tội Phạm, Giật Gân, Bí Ẩn',
    47 => 'Tâm Lý, Giật Gân',
    48 => 'Kinh Dị, Siêu Nhiên, Bí Ẩn',
    49 => 'Hài Hước, Phiêu Lưu',
    50 => 'Hoạt Hình, Hài Hước, Gia Đình',
    51 => 'Tình Cảm, Lãng Mạn, Hài Hước',
    52 => 'Kinh Dị, Giả Tưởng',
    53 => 'Tâm Lý, Chính Kịch',
    54 => 'Gia Đình, Tâm Lý',
    55 => 'Hoạt Hình, Giả Tưởng, Tâm Lý',
    56 => 'Bí Ẩn, Tâm Lý, Phiêu Lưu',
    57 => 'Tội Phạm, Hành Động',
    58 => 'Hoạt Hình, Phiêu Lưu, Giả Tưởng',
    59 => 'Tâm Lý, Gia Đình',
    60 => 'Tâm Lý, Nghệ Thuật',
    61 => 'Hành Động, Tội Phạm',
    62 => 'Tâm Lý, Giật Gân',
    63 => 'Hành Động, Võ Thuật, Cổ Trang',
    64 => 'Hành Động, Thể Thao',
    65 => 'Giả Tưởng, Huyền Bí, Tâm Lý',
    66 => 'Hành Động, Chiến Tranh',
    67 => 'Hoạt Hình, Hài Hước, Gia Đình'
];

$stmtUpdateGenre = $pdo->prepare("UPDATE movies SET genre = :genre WHERE id = :id");
foreach ($genreMap as $id => $genre) {
    $stmtUpdateGenre->execute([':genre' => $genre, ':id' => $id]);
}
echo "=> Đã cập nhật thể loại phong phú cho toàn bộ " . count($genreMap) . " phim thành công!\n";

echo "\n=== 2. KIỂM TRA & BỔ SUNG NGƯỜI DÙNG THẬT VÀO DB ===\n";
$usersToEnsure = [
    [
        'email' => 'nguyenvanan@gmail.com',
        'name' => 'Nguyễn Văn An',
        'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150',
    ],
    [
        'email' => 'lethimai@gmail.com',
        'name' => 'Lê Thị Mai',
        'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150',
    ],
    [
        'email' => 'hoangminhduc@gmail.com',
        'name' => 'Hoàng Minh Đức',
        'avatar' => 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?w=150',
    ],
    [
        'email' => 'phamthuytrang@gmail.com',
        'name' => 'Phạm Thùy Trang',
        'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=150',
    ],
    [
        'email' => 'dangquanghuy@gmail.com',
        'name' => 'Đặng Quang Huy',
        'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150',
    ],
    [
        'email' => 'vothihong@gmail.com',
        'name' => 'Võ Thị Hồng',
        'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150',
    ],
];

$passwordHash = password_hash('password123', PASSWORD_BCRYPT);
$userIds = [];

// Also get existing users
$existingUsers = $pdo->query("SELECT id, email, name FROM users")->fetchAll(PDO::FETCH_ASSOC);
foreach ($existingUsers as $eu) {
    $userIds[$eu['email']] = $eu['id'];
}

$stmtInsertUser = $pdo->prepare("INSERT INTO users (name, email, password, role, avatar, created_at, updated_at) 
                                 VALUES (:name, :email, :password, 'user', :avatar, NOW(), NOW())");

foreach ($usersToEnsure as $u) {
    if (!isset($userIds[$u['email']])) {
        $stmtInsertUser->execute([
            ':name' => $u['name'],
            ':email' => $u['email'],
            ':password' => $passwordHash,
            ':avatar' => $u['avatar']
        ]);
        $newId = $pdo->lastInsertId();
        $userIds[$u['email']] = $newId;
        echo "  + Đã tạo user: {$u['name']} (ID: $newId)\n";
    } else {
        // update avatar if null
        $pdo->prepare("UPDATE users SET avatar = :avatar WHERE id = :id AND (avatar IS NULL OR avatar = '')")->execute([
            ':avatar' => $u['avatar'],
            ':id' => $userIds[$u['email']]
        ]);
    }
}

echo "=> Danh sách User IDs sẵn sàng:\n";
print_r($userIds);

echo "\n=== 3. TẠO ĐƠN HÀNG XÁC THỰC ĐÃ XEM PHIM (COMPLETED / PAID + PAST SHOWS) ===\n";
// Tìm các suất chiếu trong quá khứ (< NOW()) cho từng phim
$stmtFindPastShow = $pdo->prepare("SELECT id, movie_id, start_time FROM shows WHERE movie_id = :movie_id AND start_time < NOW() ORDER BY start_time ASC LIMIT 1");

// Helper to ensure order exists
$stmtCheckOrder = $pdo->prepare("SELECT id FROM orders WHERE user_id = :user_id AND show_id = :show_id");
$stmtInsertOrder = $pdo->prepare("INSERT INTO orders (user_id, show_id, total_amount, status, payment_method, qr_code, created_at, updated_at) 
                                 VALUES (:user_id, :show_id, 90000, 'completed', 'momo', 'TICKET_QR_VERIFIED', NOW() - INTERVAL 2 DAY, NOW() - INTERVAL 2 DAY)");

// Phim mục tiêu để tạo đơn hàng & review:
// Phim 1: Ngày Tàn Của Phố Oak
// Phim 2: Vùng Đất Quỷ Dữ
// Phim 3: Coyote vs. Acme
// Phim 7: Hành Trình Của Moana
// Phim 13: Minions & Quái Vật
// Phim 25: Avengers: Hồi Kết
// Phim 30: Nhà Tù Shawshank
// Phim 36: Phim Kinh Dị
// Phim 40: Cái Chết của Robin Hood

$targetMovies = [1, 2, 3, 7, 13, 25, 30, 36, 40];

// All user IDs to participate in watching movies
$allUserIds = array_values($userIds);

foreach ($targetMovies as $mId) {
    $stmtFindPastShow->execute([':movie_id' => $mId]);
    $show = $stmtFindPastShow->fetch(PDO::FETCH_ASSOC);
    if (!$show) {
        // If no past show exists, create one for demonstration
        $pastTime = date('Y-m-d H:i:s', strtotime('-3 days'));
        $endTime = date('Y-m-d H:i:s', strtotime('-3 days +2 hours'));
        $pdo->prepare("INSERT INTO shows (movie_id, room_id, start_time, end_time, price, created_at, updated_at) VALUES (:m, 1, :st, :et, 90000, NOW(), NOW())")
            ->execute([':m' => $mId, ':st' => $pastTime, ':et' => $endTime]);
        $showId = $pdo->lastInsertId();
    } else {
        $showId = $show['id'];
    }

    // Cho 4-6 users xem mỗi phim này
    foreach ($allUserIds as $idx => $uid) {
        $stmtCheckOrder->execute([':user_id' => $uid, ':show_id' => $showId]);
        $order = $stmtCheckOrder->fetch(PDO::FETCH_ASSOC);
        if (!$order) {
            $stmtInsertOrder->execute([':user_id' => $uid, ':show_id' => $showId]);
        }
    }
}
echo "=> Đã tạo vé và suất chiếu đã xem thành công cho các users!\n";

echo "\n=== 4. THÊM DỮ LIỆU ĐÁNH GIÁ & BÌNH LUẬN THẬT PHONG CÁCH MOMO VÀO DB ===\n";

$sampleReviews = [
    // Phim 1: Ngày Tàn Của Phố Oak
    1 => [
        [
            'email' => 'nguyenvanan@gmail.com',
            'rating' => 10,
            'comment' => 'Phim siêu cuốn, kịch bản quá chặt chẽ và hồi hộp từ đầu đến cuối! Diễn xuất của dàn cast đỉnh chóp, không hề có chi tiết thừa. Rất đáng tiền vé rạp CineStar! #DiễnXuấtĐỉnh #KịchBảnHay',
            'days_ago' => 3
        ],
        [
            'email' => 'lethimai@gmail.com',
            'rating' => 9,
            'comment' => 'Âm thanh và kỹ xảo trong rạp nghe đã tai dã man. Góc quay nghệ thuật, không khí phim nghẹt thở. Điểm 9/10 cho một tác phẩm giật gân chất lượng!',
            'days_ago' => 2
        ],
        [
            'email' => 'hoangminhduc@gmail.com',
            'rating' => 9,
            'comment' => 'Cái kết làm mình bất ngờ ngơ ngác luôn. Lâu lắm rồi mới được xem một bộ phim hình sự điều tra gay cấn như thế này. 10 điểm cho kịch bản!',
            'days_ago' => 2
        ],
        [
            'email' => 'phamthuytrang@gmail.com',
            'rating' => 8,
            'comment' => 'Phim hay, nhịp điệu nhanh, hồi hộp. Tuy nhiên đoạn giữa hơi dông dài một chút, nhưng đoạn climax kéo lại tất cả.',
            'days_ago' => 1
        ],
    ],
    // Phim 2: Vùng Đất Quỷ Dữ
    2 => [
        [
            'email' => 'hoangminhduc@gmail.com',
            'rating' => 9,
            'comment' => 'Kinh dị rợn tóc gáy! Xem rạp phòng tối âm thanh vòm giật mình thon thót. Bạn nào yếu tim nhớ cân nhắc nhé, đáng xem cực kỳ!',
            'days_ago' => 4
        ],
        [
            'email' => 'dangquanghuy@gmail.com',
            'rating' => 10,
            'comment' => 'Quá xuất sắc! Kỹ xảo quái vật tạo hình chân thực đến rùng mình. Nhạc phim dồn dập làm tăng nhịp tim liên tục.',
            'days_ago' => 3
        ],
        [
            'email' => 'vothihong@gmail.com',
            'rating' => 8,
            'comment' => 'Phim kinh dị làm tới nơi tới chốn, jumpscare hợp lý không bị rẻ tiền. Dàn diễn viên thể hiện nỗi sợ rất đạt.',
            'days_ago' => 1
        ],
    ],
    // Phim 3: Coyote vs. Acme
    3 => [
        [
            'email' => 'lethimai@gmail.com',
            'rating' => 10,
            'comment' => 'Cười té ghế từ đầu đến cuối! Cả rạp cười vang luôn. Đồ họa kết hợp live-action mượt mà, hài hước thông minh cả người lớn lẫn trẻ em đều thích.',
            'days_ago' => 4
        ],
        [
            'email' => 'nguyenvanan@gmail.com',
            'rating' => 9,
            'comment' => 'Tuổi thơ ùa về trọn vẹn! Sự hài hước mang đúng tinh thần Looney Tunes huyền thoại nhưng hiện đại hơn nhiều.',
            'days_ago' => 3
        ],
        [
            'email' => 'phamthuytrang@gmail.com',
            'rating' => 9,
            'comment' => 'Phim giải trí đỉnh cao cuối tuần cho cả gia đình. Không khí xem phim tại rạp vui vẻ ấm cúng.',
            'days_ago' => 2
        ],
    ],
    // Phim 7: Hành Trình Của Moana
    7 => [
        [
            'email' => 'phamthuytrang@gmail.com',
            'rating' => 10,
            'comment' => 'Hình ảnh biển cả đẹp đến nao lòng! Bài hát nào cũng xuất sắc, nghe mà nổi da gà. Thông điệp về lòng dũng cảm chạm đến trái tim.',
            'days_ago' => 5
        ],
        [
            'email' => 'vothihong@gmail.com',
            'rating' => 10,
            'comment' => 'Một kiệt tác hoạt hình thực thụ. Màu sắc rực rỡ, âm nhạc đỉnh cao và kỹ xảo nước 3D đỉnh nhất từng thấy.',
            'days_ago' => 3
        ],
        [
            'email' => 'dangquanghuy@gmail.com',
            'rating' => 9,
            'comment' => 'Đưa cả gia đình đi xem, ai cũng khen nức nở. Rạp CineStar màn chiếu sắc nét xem hoạt hình quá đã!',
            'days_ago' => 2
        ],
    ],
    // Phim 25: Avengers: Hồi Kết
    25 => [
        [
            'email' => 'nguyenvanan@gmail.com',
            'rating' => 10,
            'comment' => 'Huyền thoại không cần bàn cãi! Đoạn Avengers Assemble xúc động rơi nước mắt. Trận chiến cuối cùng xứng đáng điểm 10 tuyệt đối!',
            'days_ago' => 6
        ],
        [
            'email' => 'hoangminhduc@gmail.com',
            'rating' => 10,
            'comment' => 'Đỉnh cao của điện ảnh siêu anh hùng! Kỹ xảo hoành tráng, cái kết mãn nguyện và trọn vẹn cho hành trình 10 năm.',
            'days_ago' => 5
        ],
        [
            'email' => 'dangquanghuy@gmail.com',
            'rating' => 10,
            'comment' => 'Xem lại lần thứ N ở rạp vẫn thấy bồi hồi. I love you 3000! Trải nghiệm tại rạp CineStar âm thanh rung cả ghế.',
            'days_ago' => 3
        ],
        [
            'email' => 'lethimai@gmail.com',
            'rating' => 9,
            'comment' => '3 tiếng trôi qua như một cái chớp mắt. Tuyệt phẩm điện ảnh đáng xem nhất mọi thời đại!',
            'days_ago' => 2
        ],
    ],
    // Phim 30: Nhà Tù Shawshank
    30 => [
        [
            'email' => 'hoangminhduc@gmail.com',
            'rating' => 10,
            'comment' => 'Bộ phim số 1 trong lịch sử điện ảnh. Triết lý về hy vọng và sự tự do chưa bao giờ lỗi thời. Tuyệt vời đến từng khung hình!',
            'days_ago' => 5
        ],
        [
            'email' => 'vothihong@gmail.com',
            'rating' => 10,
            'comment' => 'Xem xong đọng lại biết bao suy ngẫm. Diễn xuất của Morgan Freeman và Tim Robbins đạt đến đẳng cấp tượng đài.',
            'days_ago' => 4
        ],
    ],
    // Phim 36: Phim Kinh Dị
    36 => [
        [
            'email' => 'user@cinema.com', // User Trần Thị B
            'rating' => 8,
            'comment' => 'Phim hù dọa rất tốt, âm thanh trong rạp ghê rợn rùng mình. Trải nghiệm xem xong tối về không dám đi toilet một mình!',
            'days_ago' => 1
        ],
        [
            'email' => 'dangquanghuy@gmail.com',
            'rating' => 9,
            'comment' => 'Cốt truyện đen tối và khó lường. Rất hoan nghênh những tác phẩm thể loại này, xem cực kỳ đã.',
            'days_ago' => 2
        ],
    ],
];

$stmtInsertReview = $pdo->prepare("INSERT INTO reviews (user_id, movie_id, rating, comment, created_at, updated_at) 
                                  VALUES (:user_id, :movie_id, :rating, :comment, NOW() - INTERVAL :days_ago DAY, NOW() - INTERVAL :days_ago DAY)");

$stmtCheckReview = $pdo->prepare("SELECT id FROM reviews WHERE user_id = :user_id AND movie_id = :movie_id");

$reviewsAdded = 0;
foreach ($sampleReviews as $movieId => $revList) {
    foreach ($revList as $r) {
        $userEmail = $r['email'];
        if (!isset($userIds[$userEmail])) continue;
        $uid = $userIds[$userEmail];

        $stmtCheckReview->execute([':user_id' => $uid, ':movie_id' => $movieId]);
        $exists = $stmtCheckReview->fetch(PDO::FETCH_ASSOC);
        if (!$exists) {
            $stmtInsertReview->execute([
                ':user_id' => $uid,
                ':movie_id' => $movieId,
                ':rating' => $r['rating'],
                ':comment' => $r['comment'],
                ':days_ago' => $r['days_ago']
            ]);
            $reviewsAdded++;
        }
    }
}
echo "=> Đã thêm thành công $reviewsAdded đánh giá thật vào DB!\n";

// ĐẶC BIỆT: Để người dùng test tính năng bình luận ngay khi đăng nhập:
// Tạo completed order cho User 3 (user@cinema.com) và User 5 (lamto20050505@gmail.com) trên Phim 1 và Phim 25
// NHƯNG CHƯA ĐÁNH GIÁ Phim 1 và Phim 25!
// Như vậy khi User 3 hoặc User 5 vào Phim 1 hoặc Phim 25, họ SẼ CÓ QUYỀN ĐÁNH GIÁ (movieHasWatched = true, movieHasReviewed = false)!
// Còn nếu họ vào Phim 4 hoặc Phim 5, họ CHƯA XEM -> Sẽ hiện thông báo MoMo yêu cầu mua vé!
echo "\n=== 5. THIẾT LẬP TÀI KHOẢN TEST SẴN SÀNG ĐÁNH GIÁ ===\n";
$testUsers = ['user@cinema.com', 'lamto20050505@gmail.com'];
$moviesAllowedToReview = [1, 7]; // Phim 1 & Phim 7

foreach ($testUsers as $email) {
    if (isset($userIds[$email])) {
        $uid = $userIds[$email];
        foreach ($moviesAllowedToReview as $mId) {
            $stmtFindPastShow->execute([':movie_id' => $mId]);
            $show = $stmtFindPastShow->fetch(PDO::FETCH_ASSOC);
            if ($show) {
                $stmtCheckOrder->execute([':user_id' => $uid, ':show_id' => $show['id']]);
                if (!$stmtCheckOrder->fetch()) {
                    $stmtInsertOrder->execute([':user_id' => $uid, ':show_id' => $show['id']]);
                    echo "  + Đã cấp quyền đã xem Phim $mId cho user $email\n";
                }
            }
        }
    }
}

echo "\n=== HOÀN TẤT SEED DỮ LIỆU THẬT VÀO DATABASE ===\n";
