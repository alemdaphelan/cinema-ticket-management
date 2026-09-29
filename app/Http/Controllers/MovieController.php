<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * [Public] Danh sách phim đang chiếu / sắp chiếu.
     */
    public function publicIndex(Request $request)
    {
        $status = $request->input('status', 'showing_coming_soon');
        $search = $request->input('search', '');
        $genre = $request->input('genre', '');
        $page = $request->input('page', 1);

        $cacheKey = "movies_index_{$status}_{$search}_{$genre}_{$page}";

        $movies = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request) {
            $query = Movie::query();

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            } else {
                $query->whereIn('status', ['showing', 'coming_soon']);
            }

            if ($request->filled('search')) {
                $query->where('title', 'like', '%' . $request->input('search') . '%');
            }

            if ($request->filled('genre')) {
                $query->where('genre', 'like', '%' . $request->input('genre') . '%');
            }

            return $query->orderBy('created_at', 'desc')->paginate(12)->toArray();
        });

        return response()->json($movies);
    }

    /**
     * [Public] Chi tiết phim (bao gồm URL teaser).
     */
    public function publicShow(int $id)
    {
        $movieData = \Illuminate\Support\Facades\Cache::remember("movie_detail_{$id}", now()->addMinutes(10), function () use ($id) {
            $movie = Movie::withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->findOrFail($id);

            return [
                'data' => $movie->toArray(),
                'review_stats' => [
                    'total_reviews' => $movie->reviews_count,
                    'average_rating' => round($movie->reviews_avg_rating, 1) ?: 0,
                ]
            ];
        });

        $movie = $movieData['data'];
        $reviewStats = $movieData['review_stats'];

        // Kiểm tra user đã xem phim chưa
        $hasWatched = false;
        $hasReviewed = false;
        if (auth('sanctum')->check()) {
            $userId = auth('sanctum')->id();
            $hasWatched = \App\Models\Order::where('user_id', $userId)
                ->whereIn('status', ['paid', 'completed'])
                ->whereHas('show', function ($query) use ($id) {
                    $query->where('movie_id', $id)
                          ->where('start_time', '<', now());
                })
                ->exists();
            $hasReviewed = \App\Models\Review::where('user_id', $userId)
                ->where('movie_id', $id)
                ->exists();
        }

        return response()->json([
            'data' => $movie,
            'review_stats' => $reviewStats,
            'has_watched' => $hasWatched,
            'has_reviewed' => $hasReviewed,
        ]);
    }

    /**
     * [Admin] Danh sách phim (bao gồm cả stopped).
     */
    public function fetchTmdb(Request $request)
    {
        $request->validate([
            'tmdb_id' => 'required|string'
        ]);

        $tmdbId = $request->tmdb_id;
        $apiKey = env('TMDB_API_KEY');

        if (!$apiKey) {
            return response()->json(['message' => 'TMDB API key is not configured.'], 500);
        }

        $response = \Illuminate\Support\Facades\Http::withoutVerifying()->get("https://api.themoviedb.org/3/movie/{$tmdbId}", [
            'api_key' => $apiKey,
            'language' => 'vi'
        ]);

        if ($response->failed()) {
            return response()->json(['message' => 'Failed to fetch data from TMDB', 'error' => $response->json()], $response->status());
        }

        $data = $response->json();

        // Get Credits for Director/Cast
        $creditsResponse = \Illuminate\Support\Facades\Http::withoutVerifying()->get("https://api.themoviedb.org/3/movie/{$tmdbId}/credits", [
            'api_key' => $apiKey
        ]);

        $director = 'Unknown';
        if ($creditsResponse->successful()) {
            $crew = $creditsResponse->json()['crew'] ?? [];
            foreach ($crew as $member) {
                if ($member['job'] === 'Director') {
                    $director = $member['name'];
                    break;
                }
            }
        }

        $mappedData = [
            'tmdb_id' => $tmdbId,
            'title' => $data['title'] ?? $data['original_title'],
            'director' => $director,
            'poster_url' => isset($data['poster_path']) ? "https://image.tmdb.org/t/p/w500" . $data['poster_path'] : null,
            'teaser_url' => null, // Typically TMDB videos endpoint is needed for trailers
            'duration_minutes' => $data['runtime'] ?? 0,
            'status' => 'coming_soon',
            'raw_data' => $data // optional for review
        ];

        return response()->json([
            'message' => 'TMDB data fetched successfully.',
            'data' => $mappedData
        ]);
    }

    public function index()
    {
        $movies = Movie::orderBy('created_at', 'desc')->get();

        return response()->json(['data' => $movies]);
    }

    /**
     * [Admin] Thêm phim mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'director' => 'nullable|string|max:255',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'poster_url' => 'nullable|string',
            'teaser_url' => 'nullable|string',
            'teaser_file' => 'nullable|file|mimes:mp4|max:102400',
            'duration_minutes' => 'required|integer|min:1',
            'status' => 'required|in:coming_soon,showing,stopped',
            'tmdb_id' => 'nullable|string',
            'description' => 'nullable|string',
            'genre' => 'nullable|string',
            'age_rating' => 'nullable|string|max:10',
            'release_date' => 'nullable|date',
        ]);

        if ($request->hasFile('poster')) {
            $path = $request->file('poster')->store('movies', 'public');
            $validated['poster_url'] = '/storage/' . $path;
        }

        if ($request->hasFile('teaser_file')) {
            $path = $request->file('teaser_file')->store('teasers', 'public');
            $validated['teaser_url'] = '/storage/' . $path;
        }

        unset($validated['teaser_file']);

        $movie = Movie::create($validated);

        return response()->json(['data' => $movie, 'message' => 'Thêm phim thành công!'], 201);
    }

    /**
     * [Admin] Cập nhật phim.
     */
    public function update(Request $request, int $id)
    {
        $movie = Movie::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'director' => 'nullable|string|max:255',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'poster_url' => 'nullable|string',
            'teaser_url' => 'nullable|string',
            'teaser_file' => 'nullable|file|mimes:mp4|max:102400',
            'duration_minutes' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:coming_soon,showing,stopped',
            'tmdb_id' => 'nullable|string',
            'description' => 'nullable|string',
            'genre' => 'nullable|string',
            'age_rating' => 'nullable|string|max:10',
            'release_date' => 'nullable|date',
        ]);

        if ($request->hasFile('poster')) {
            if ($movie->poster_url && str_starts_with($movie->poster_url, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $movie->poster_url);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('poster')->store('movies', 'public');
            $validated['poster_url'] = '/storage/' . $path;
        }

        if ($request->hasFile('teaser_file')) {
            if ($movie->teaser_url && str_starts_with($movie->teaser_url, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $movie->teaser_url);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('teaser_file')->store('teasers', 'public');
            $validated['teaser_url'] = '/storage/' . $path;
        }

        unset($validated['teaser_file']);

        $movie->update($validated);

        return response()->json(['data' => $movie, 'message' => 'Cập nhật phim thành công!']);
    }

    /**
     * [Admin] Xóa / Ẩn phim.
     */
    public function destroy(int $id)
    {
        $movie = Movie::findOrFail($id);
        $movie->delete();

        return response()->json(null, 204);
    }

    /**
     * [Admin] Kéo phim từ TMDB (hàng loạt).
     */
    public function fetchTmdbList()
    {
        \Illuminate\Support\Facades\Artisan::call('movies:fetch-tmdb');
        return response()->json(['message' => 'Đã kéo thành công dữ liệu từ TMDB!']);
    }

}
