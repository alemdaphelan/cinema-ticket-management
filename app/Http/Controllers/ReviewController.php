<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ReviewController extends Controller
{
    /**
     * Danh sách đánh giá của phim kèm số liệu thống kê chuẩn phong cách MoMo.
     */
    public function index(Request $request, int $movieId)
    {
        $movie = Movie::findOrFail($movieId);

        // Lấy tất cả ratings để tính thống kê MoMo
        $allReviews = Review::where('movie_id', $movieId)->select('id', 'rating', 'comment')->get();
        $totalReviews = $allReviews->count();

        $avgRating = $totalReviews > 0 ? round($allReviews->avg('rating'), 1) : 0;

        // Tính phân bổ điểm số theo phong cách MoMo
        $count9to10 = $allReviews->whereBetween('rating', [9, 10])->count();
        $count7to8 = $allReviews->whereBetween('rating', [7, 8])->count();
        $count5to6 = $allReviews->whereBetween('rating', [5, 6])->count();
        $count3to4 = $allReviews->whereBetween('rating', [3, 4])->count();
        $count1to2 = $allReviews->whereBetween('rating', [1, 2])->count();

        $pct9to10 = $totalReviews > 0 ? round(($count9to10 / $totalReviews) * 100) : 0;
        $pct7to8 = $totalReviews > 0 ? round(($count7to8 / $totalReviews) * 100) : 0;
        $pct5to6 = $totalReviews > 0 ? round(($count5to6 / $totalReviews) * 100) : 0;
        $pct3to4 = $totalReviews > 0 ? round(($count3to4 / $totalReviews) * 100) : 0;
        $pct1to2 = $totalReviews > 0 ? round(($count1to2 / $totalReviews) * 100) : 0;

        $satisfactionRate = $totalReviews > 0 ? round((($count9to10 + $count7to8) / $totalReviews) * 100) : 100;

        // Điểm nhấn cảm xúc MoMo tags
        $momoTags = [
            ['tag' => 'Diễn xuất đỉnh cao', 'percentage' => min(98, max(85, $satisfactionRate + 2))],
            ['tag' => 'Kịch bản lôi cuốn', 'percentage' => min(96, max(82, $satisfactionRate - 1))],
            ['tag' => 'Kỹ xảo mãn nhãn', 'percentage' => min(99, max(88, $satisfactionRate + 3))],
            ['tag' => 'Âm thanh sống động', 'percentage' => min(97, max(80, $satisfactionRate))],
            ['tag' => 'Cái kết ấn tượng', 'percentage' => min(95, max(75, $satisfactionRate - 4))],
        ];

        // Lọc danh sách đánh giá
        $query = Review::where('movie_id', $movieId)
            ->with('user:id,name,avatar');

        $filter = $request->input('rating_filter', 'all');
        if ($filter === '9-10') {
            $query->whereBetween('rating', [9, 10]);
        } elseif ($filter === '7-8') {
            $query->whereBetween('rating', [7, 8]);
        } elseif ($filter === 'under_7') {
            $query->where('rating', '<', 7);
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'summary' => [
                'average_rating' => $avgRating,
                'total_reviews' => $totalReviews,
                'satisfaction_rate' => $satisfactionRate,
                'breakdown' => [
                    'stars_9_10' => ['count' => $count9to10, 'percentage' => $pct9to10],
                    'stars_7_8' => ['count' => $count7to8, 'percentage' => $pct7to8],
                    'stars_5_6' => ['count' => $count5to6, 'percentage' => $pct5to6],
                    'stars_3_4' => ['count' => $count3to4, 'percentage' => $pct3to4],
                    'stars_1_2' => ['count' => $count1to2, 'percentage' => $pct1to2],
                ],
                'top_tags' => $momoTags,
            ],
            'data' => $reviews->items(),
            'current_page' => $reviews->currentPage(),
            'last_page' => $reviews->lastPage(),
            'total' => $reviews->total(),
        ]);
    }

    /**
     * Thêm đánh giá cho phim.
     * Chỉ user đã đặt vé và suất chiếu đã qua mới được đánh giá.
     */
    public function store(Request $request, int $movieId)
    {
        $movie = Movie::findOrFail($movieId);
        $user = Auth::user() ?? auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'message' => 'Vui lòng đăng nhập để đánh giá phim.'
            ], 401);
        }

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
                'message' => 'Chỉ khán giả đã mua vé và xem phim xong tại rạp mới có quyền đánh giá phim này.'
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

        // Xóa cache phim để dữ liệu trung bình được cập nhật tức thì
        Cache::forget("movie_detail_{$movieId}");
        Cache::flush();

        return response()->json([
            'message' => 'Đánh giá thành công! Cảm ơn bạn đã chia sẻ cảm nhận.',
            'data' => $review,
        ], 201);
    }
}
