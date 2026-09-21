<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Movie;

class FetchTmdbMovies extends Command
{
    protected $signature = 'movies:fetch-tmdb';
    protected $description = 'Kéo danh sách phim từ TMDB về database';

    public function handle()
    {
        $apiKey = env('TMDB_API_KEY', 'ebf1d04adcf30701e41125e0fa1ea5c6');
        
        $this->info('Đang kéo phim Đang chiếu (Now Playing)...');
        for ($page = 1; $page <= 2; $page++) {
            $this->info("Kéo trang $page...");
            $this->fetchMovies("https://api.themoviedb.org/3/movie/now_playing?api_key={$apiKey}&language=vi-VN&page={$page}", 'showing', "tmdb_showing_{$page}.json");
        }
        
        $this->info('Đang kéo phim Sắp chiếu (Upcoming)...');
        for ($page = 1; $page <= 2; $page++) {
            $this->info("Kéo trang $page...");
            $this->fetchMovies("https://api.themoviedb.org/3/movie/upcoming?api_key={$apiKey}&language=vi-VN&page={$page}", 'coming_soon', "tmdb_coming_{$page}.json");
        }
        
        $this->info('Kéo dữ liệu TMDB thành công!');
    }

    private function fetchMovies($url, $status, $localFallbackFile)
    {
        $movies = null;
        try {
            $response = Http::timeout(5)->withoutVerifying()->get($url);
            if ($response->successful()) {
                $movies = $response->json('results');
                // Cập nhật lại file local dự phòng nếu kéo thành công
                file_put_contents(storage_path('app/' . $localFallbackFile), $response->body());
            }
        } catch (\Exception $e) {
            $this->error("Lỗi mạng khi gọi API TMDB: " . $e->getMessage());
        }

        // Nếu không lấy được từ API, đọc từ file local dự phòng
        if (!$movies) {
            $localPath = storage_path('app/' . $localFallbackFile);
            if (file_exists($localPath)) {
                $this->info("Đang sử dụng dữ liệu dự phòng từ file: " . $localFallbackFile);
                $jsonData = json_decode(file_get_contents($localPath), true);
                $movies = $jsonData['results'] ?? [];
            }
        }

        if ($movies) {
            foreach ($movies as $tmdbMovie) {
                // Bỏ qua nếu đã có trong DB
                if (Movie::where('tmdb_id', $tmdbMovie['id'])->exists()) {
                    continue;
                }
                
                // Kéo trailer thật từ TMDB (có thể bỏ qua nếu mạng lỗi)
                $teaserUrl = null; // Tránh dùng Rickroll làm trailer mặc định
                $apiKey = env('TMDB_API_KEY', 'ebf1d04adcf30701e41125e0fa1ea5c6');
                try {
                    $videoResponse = Http::timeout(3)->withoutVerifying()->get("https://api.themoviedb.org/3/movie/{$tmdbMovie['id']}/videos?api_key={$apiKey}");
                    if ($videoResponse->successful()) {
                        $videos = $videoResponse->json('results');
                        foreach ($videos as $video) {
                            if ($video['site'] === 'YouTube' && ($video['type'] === 'Trailer' || $video['type'] === 'Teaser')) {
                                $teaserUrl = 'https://www.youtube.com/embed/' . $video['key'];
                                break;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Nếu lỗi mạng TMDB, tự động tìm trailer thật trên Youtube
                    try {
                        $searchQuery = urlencode($tmdbMovie['title'] . ' trailer');
                        // Thêm user-agent để cào youtube tốt hơn
                        $opts = ['http' => ['header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n"]];
                        $html = file_get_contents("https://www.youtube.com/results?search_query=" . $searchQuery, false, stream_context_create($opts));
                        if ($html && preg_match('/"videoId":"([^"]{11})"/', $html, $matches)) {
                            $teaserUrl = 'https://www.youtube.com/embed/' . $matches[1];
                        }
                    } catch (\Exception $ex) {
                        // Bỏ qua nếu vẫn lỗi
                    }
                }

                // Dùng Image Proxy để tránh bị block ảnh
                $imageProxy = 'https://wsrv.nl/?url=image.tmdb.org/t/p/w500';
                $posterUrl = $tmdbMovie['poster_path'] ? $imageProxy . $tmdbMovie['poster_path'] : '/images/placeholder.png';

                // Vì API list không trả về thời lượng và đạo diễn chi tiết, ta sẽ giả lập một chút cho đẹp
                Movie::create([
                    'tmdb_id' => $tmdbMovie['id'],
                    'title' => $tmdbMovie['title'],
                    'director' => 'Đạo diễn Hollywood', // Giả lập
                    'poster_url' => $posterUrl,
                    'teaser_url' => $teaserUrl,
                    'duration_minutes' => rand(90, 160),
                    'status' => $status,
                    'description' => $tmdbMovie['overview'] ?: 'Đang cập nhật...',
                    'genre' => 'Hành động, Phiêu lưu', // Giả lập
                ]);
            }
        } else {
            $this->error("Không có dữ liệu từ API và cũng không có file dự phòng!");
        }
    }
}
