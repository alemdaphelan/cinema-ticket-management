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
        $query = Movie::query();

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        } else {
            $query->whereIn('status', ['showing', 'coming_soon']);
        }

        $movies = $query->orderBy('created_at', 'desc')->get();

        return response()->json(['data' => $movies]);
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
            'poster_url' => 'nullable|string',
            'teaser_url' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'status' => 'required|in:coming_soon,showing,stopped',
            'tmdb_id' => 'nullable|string',
            'description' => 'nullable|string',
            'genre' => 'nullable|string',
            'release_date' => 'nullable|date',
        ]);

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
            'poster_url' => 'nullable|string',
            'teaser_url' => 'nullable|string',
            'duration_minutes' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:coming_soon,showing,stopped',
            'tmdb_id' => 'nullable|string',
            'description' => 'nullable|string',
            'genre' => 'nullable|string',
            'release_date' => 'nullable|date',
        ]);

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
     * [Admin] Lấy thông tin phim từ TMDB (placeholder).
     */
    public function fetchFromTmdb(Request $request)
    {
        $validated = $request->validate([
            'tmdb_id' => 'required|string',
        ]);

        // Placeholder: Trả về mock data
        // TODO: Tích hợp TMDB API thật khi có API key
        return response()->json([
            'data' => [
                'tmdb_id' => $validated['tmdb_id'],
                'title' => 'Phim từ TMDB #' . $validated['tmdb_id'],
                'director' => 'Đạo diễn TMDB',
                'poster_url' => 'https://via.placeholder.com/300x450',
                'teaser_url' => '',
                'duration_minutes' => 120,
                'description' => 'Mô tả phim từ TMDB',
            ],
            'message' => 'Đây là mock data. Tích hợp TMDB API key để lấy data thật.',
        ]);
    }
}
