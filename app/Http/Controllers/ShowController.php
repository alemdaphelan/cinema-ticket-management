<?php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\Movie;
use App\Models\Seat;
use App\Models\OrderSeat;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    /**
     * [Public] Lấy danh sách suất chiếu theo phim và ngày.
     */
    public function publicIndex(Request $request)
    {
        $query = Show::with(['movie', 'room']);

        if ($request->has('movie_id')) {
            $query->where('movie_id', $request->input('movie_id'));
        }

        if ($request->has('date')) {
            $query->whereDate('start_time', $request->input('date'));
        } else {
            $query->where('start_time', '>=', now());
        }

        $shows = $query->orderBy('start_time', 'asc')->get();

        return response()->json(['data' => $shows]);
    }

    /**
     * [Public] Lấy sơ đồ ghế và trạng thái cho một suất chiếu.
     */
    public function getSeats(int $id)
    {
        $show = Show::with('room')->findOrFail($id);
        $seats = Seat::where('room_id', $show->room_id)->orderBy('row_label')->orderBy('seat_number')->get();

        // Lấy danh sách ghế đã bị đặt hoặc đang giữ
        $unavailableSeatIds = OrderSeat::whereHas('order', function ($q) use ($id) {
            $q->where('show_id', $id)
              ->where(function ($query) {
                  $query->whereIn('status', ['paid', 'completed'])
                        ->orWhere(function ($sub) {
                            $sub->where('status', 'pending')
                                ->where('hold_expires_at', '>', now());
                        });
              });
        })->pluck('seat_id')->toArray();

        $seatsData = $seats->map(function ($seat) use ($unavailableSeatIds) {
            $status = 'available';
            if (in_array($seat->id, $unavailableSeatIds)) {
                $status = 'booked';
            }

            return [
                'id' => $seat->id,
                'row_label' => $seat->row_label,
                'seat_number' => $seat->seat_number,
                'type' => $seat->type,
                'status' => $status,
            ];
        });

        return response()->json([
            'data' => [
                'show' => $show,
                'seats' => $seatsData,
            ],
        ]);
    }

    /**
     * [Admin] Danh sách tất cả suất chiếu.
     */
    public function index()
    {
        $shows = Show::with(['movie', 'room'])
            ->orderBy('start_time', 'desc')
            ->get();

        return response()->json(['data' => $shows]);
    }

    /**
     * [Admin] Tạo suất chiếu mới (có check trùng lịch).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'room_id' => 'required|exists:rooms,id',
            'start_time' => 'required|date|after:now',
            'price' => 'required|numeric|min:0',
        ]);

        $movie = Movie::findOrFail($validated['movie_id']);

        // Tính end_time = start_time + duration + 20 phút dọn rạp
        $startTime = \Carbon\Carbon::parse($validated['start_time']);
        $endTime = $startTime->copy()->addMinutes($movie->duration_minutes + 20);

        // Check trùng lịch trong cùng phòng
        $isConflict = Show::where('room_id', $validated['room_id'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                      ->orWhereBetween('end_time', [$startTime, $endTime])
                      ->orWhere(function ($q) use ($startTime, $endTime) {
                          $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                      });
            })->exists();

        if ($isConflict) {
            return response()->json([
                'message' => 'Trùng lịch chiếu! Phòng này đã có suất chiếu trong khoảng thời gian này (bao gồm 20 phút dọn rạp).',
            ], 422);
        }

        $show = Show::create([
            'movie_id' => $validated['movie_id'],
            'room_id' => $validated['room_id'],
            'start_time' => $startTime,
            'end_time' => $endTime,
            'price' => $validated['price'],
        ]);

        $show->load(['movie', 'room']);

        return response()->json(['data' => $show, 'message' => 'Tạo suất chiếu thành công!'], 201);
    }

    /**
     * [Admin] Cập nhật suất chiếu.
     */
    public function update(Request $request, int $id)
    {
        $show = Show::findOrFail($id);

        $validated = $request->validate([
            'movie_id' => 'sometimes|exists:movies,id',
            'room_id' => 'sometimes|exists:rooms,id',
            'start_time' => 'sometimes|date',
            'price' => 'sometimes|numeric|min:0',
        ]);

        if (isset($validated['start_time'])) {
            $movieId = $validated['movie_id'] ?? $show->movie_id;
            $movie = Movie::findOrFail($movieId);
            $startTime = \Carbon\Carbon::parse($validated['start_time']);
            $validated['end_time'] = $startTime->copy()->addMinutes($movie->duration_minutes + 20);
        }

        $show->update($validated);

        return response()->json(['data' => $show->fresh(['movie', 'room']), 'message' => 'Cập nhật suất chiếu thành công!']);
    }

    /**
     * [Admin] Xóa suất chiếu.
     */
    public function destroy(int $id)
    {
        $show = Show::findOrFail($id);
        $show->delete();

        return response()->json(null, 204);
    }
}
