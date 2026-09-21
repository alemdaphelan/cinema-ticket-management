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
        if (Movie::count() < 10) {
            try {
                \Illuminate\Support\Facades\Artisan::call('movies:fetch-tmdb');
            } catch (\Exception $e) {}
        }
        
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

        $movies = $query->orderBy('created_at', 'desc')->paginate(12);

        return response()->json($movies);
    }

    /**
     * [Public] Chi tiết phim (bao gồm URL teaser).
     */
    public function publicShow(int $id)
    {
        $movie = Movie::with('shows.room')->findOrFail($id);

        return response()->json(['data' => $movie]);
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
            'duration_minutes' => 'required|integer|min:1',
            'status' => 'required|in:coming_soon,showing,stopped',
            'tmdb_id' => 'nullable|string',
            'description' => 'nullable|string',
            'genre' => 'nullable|string',
            'release_date' => 'nullable|date',
        ]);

        if ($request->hasFile('poster')) {
            $path = $request->file('poster')->store('movies', 'public');
            $validated['poster_url'] = '/storage/' . $path;
        }

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
            'duration_minutes' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:coming_soon,showing,stopped',
            'tmdb_id' => 'nullable|string',
            'description' => 'nullable|string',
            'genre' => 'nullable|string',
            'release_date' => 'nullable|date',
        ]);

        if ($request->hasFile('poster')) {
            // Delete old poster if exists and is a local file
            if ($movie->poster_url && str_starts_with($movie->poster_url, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $movie->poster_url);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('poster')->store('movies', 'public');
            $validated['poster_url'] = '/storage/' . $path;
        }

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
