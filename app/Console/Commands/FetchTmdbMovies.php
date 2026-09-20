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
            $this->fetchMovies("https://api.themoviedb.org/3/movie/now_playing?api_key={$apiKey}&language=vi-VN&page={$page}", 'showing');
        }
        
        $this->info('Đang kéo phim Sắp chiếu (Upcoming)...');
        for ($page = 1; $page <= 2; $page++) {
            $this->info("Kéo trang $page...");
            $this->fetchMovies("https://api.themoviedb.org/3/movie/upcoming?api_key={$apiKey}&language=vi-VN&page={$page}", 'coming_soon');
        }
        
        $this->info('Kéo dữ liệu TMDB thành công!');
    }

    private function fetchMovies($url, $status)
    {
        $response = Http::withoutVerifying()->get($url);
        
        if ($response->successful()) {
            $movies = $response->json('results');
            
            foreach ($movies as $tmdbMovie) {
                // Bỏ qua nếu đã có trong DB
                if (Movie::where('tmdb_id', $tmdbMovie['id'])->exists()) {
                    continue;
                }
                
                // Kéo trailer thật từ TMDB
                $teaserUrl = 'https://www.youtube.com/embed/dQw4w9WgXcQ'; // Default
                $apiKey = env('TMDB_API_KEY', 'ebf1d04adcf30701e41125e0fa1ea5c6');
                $videoResponse = Http::withoutVerifying()->get("https://api.themoviedb.org/3/movie/{$tmdbMovie['id']}/videos?api_key={$apiKey}");
                
                if ($videoResponse->successful()) {
                    $videos = $videoResponse->json('results');
                    foreach ($videos as $video) {
                        if ($video['site'] === 'YouTube' && ($video['type'] === 'Trailer' || $video['type'] === 'Teaser')) {
                            $teaserUrl = 'https://www.youtube.com/embed/' . $video['key'];
                            break;
                        }
                    }
                }

                // Vì API list không trả về thời lượng và đạo diễn chi tiết, ta sẽ giả lập một chút cho đẹp
                Movie::create([
                    'tmdb_id' => $tmdbMovie['id'],
                    'title' => $tmdbMovie['title'],
                    'director' => 'Đạo diễn Hollywood', // Giả lập
                    'poster_url' => $tmdbMovie['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $tmdbMovie['poster_path'] : '/images/placeholder.png',
                    'teaser_url' => $teaserUrl,
                    'duration_minutes' => rand(90, 160),
                    'status' => $status,
                    'description' => $tmdbMovie['overview'] ?: 'Đang cập nhật...',
                    'genre' => 'Hành động, Phiêu lưu', // Giả lập
                ]);
            }
        } else {
            $this->error("Lỗi khi gọi API TMDB: " . $response->body());
        }
    }
}
