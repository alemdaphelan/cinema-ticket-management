<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Danh sách đánh giá của phim.
     */
    public function index(int $movieId)
    {
        $movie = Movie::findOrFail($movieId);

        $reviews = Review::where('movie_id', $movieId)
            ->with('user:id,name,avatar')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($reviews);
    }

    /**
     * Thêm đánh giá cho phim.
     * Chỉ user đã đặt vé và suất chiếu đã qua mới được đánh giá.
     */
    public function store(Request $request, int $movieId)
    {
        $movie = Movie::findOrFail($movieId);
        $user = Auth::user();

        // Kiểm tra user đã đánh giá phim này chưa
        $existingReview = Review::where('user_id', $user->id)
            ->where('movie_id', $movieId)
            ->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'Bạn đã đánh giá phim này rồi.'
            ], 422);
        }

        // Kiểm tra user đã đặt vé cho phim này và suất chiếu đã qua
        $hasWatched = Order::where('user_id', $user->id)
            ->whereIn('status', ['paid', 'completed'])
            ->whereHas('show', function ($query) use ($movieId) {
                $query->where('movie_id', $movieId)
                      ->where('start_time', '<', now());
            })
            ->exists();

        if (!$hasWatched) {
            return response()->json([
                'message' => 'Bạn cần đặt vé và xem phim này trước khi đánh giá.'
            ], 403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:10',
            'comment' => 'nullable|string|max:1000',
        ], [
            'rating.required' => 'Vui lòng chọn điểm đánh giá',
            'rating.min' => 'Điểm đánh giá tối thiểu là 1',
            'rating.max' => 'Điểm đánh giá tối đa là 10',
            'comment.max' => 'Bình luận không được vượt quá 1000 ký tự',
        ]);

        $review = Review::create([
            'user_id' => $user->id,
            'movie_id' => $movieId,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        $review->load('user:id,name,avatar');

        return response()->json([
            'message' => 'Đánh giá thành công!',
            'data' => $review,
        ], 201);
    }
}
