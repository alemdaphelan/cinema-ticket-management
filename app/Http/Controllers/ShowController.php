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
        $movieId = $request->input('movie_id', 'all');
        $date = $request->input('date', 'all');
        $cacheKey = "shows_index_{$movieId}_{$date}";

        $shows = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(5), function () use ($request) {
            $query = Show::with(['movie', 'room']);

            if ($request->has('movie_id')) {
                $query->where('movie_id', $request->input('movie_id'));
            }

            if ($request->has('date')) {
                $query->whereDate('start_time', $request->input('date'));
            } else {
                $query->where('start_time', '>=', now());
            }

            return $query->orderBy('start_time', 'asc')->get()->toArray();
        });

        return response()->json(['data' => $shows]);
    }

    /**
     * [Public] Lấy sơ đồ ghế và trạng thái cho một suất chiếu.
     */
    public function getSeats(int $id)
    {
        $show = Show::with(['room', 'movie'])->findOrFail($id);
        $seats = Seat::where('room_id', $show->room_id)->orderBy('row_label')->orderBy('seat_number')->get();
        $currentUserId = auth('sanctum')->id() ?? auth()->id();

        // Lấy danh sách ghế đang giữ của chính user này cho suất chiếu này
        $userHoldingSeatIds = [];
        if ($currentUserId) {
            $userHoldingSeatIds = OrderSeat::whereHas('order', function ($q) use ($id, $currentUserId) {
                $q->where('show_id', $id)
                  ->where('user_id', $currentUserId)
                  ->where('status', 'pending')
                  ->where('hold_expires_at', '>', now());
            })->pluck('seat_id')->toArray();
        }

        // Lấy danh sách ghế đã bị đặt hoặc người khác đang giữ
        $unavailableSeatIds = OrderSeat::whereHas('order', function ($q) use ($id, $currentUserId) {
            $q->where('show_id', $id)
              ->where(function ($query) use ($currentUserId) {
                  $query->whereIn('status', ['paid', 'completed'])
                        ->orWhere(function ($sub) use ($currentUserId) {
                            $sub->where('status', 'pending')
                                ->where('hold_expires_at', '>', now());
                            if ($currentUserId) {
                                $sub->where('user_id', '!=', $currentUserId);
                            }
                        });
              });
        })->pluck('seat_id')->toArray();

        $seatsData = $seats->map(function ($seat) use ($unavailableSeatIds, $userHoldingSeatIds) {
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
                'is_current_user_held' => in_array($seat->id, $userHoldingSeatIds),
            ];
        });

        return response()->json([
            'data' => [
                'show' => $show,
                'seats' => $seatsData,
                'user_held_seat_ids' => $userHoldingSeatIds,
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
     * [Admin] Tạo suất chiếu mới (check giãn cách tối thiểu 30 phút giữa các suất trong cùng phòng).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'room_id' => 'required|exists:rooms,id',
            'start_time' => 'required|date',
            'price' => 'required|numeric|min:0',
        ]);

        $movie = Movie::findOrFail($validated['movie_id']);
        $startTime = \Carbon\Carbon::parse($validated['start_time']);
        $duration = (int) ($movie->duration_minutes ?? 120);
        $endTime = $startTime->copy()->addMinutes($duration);
        
        // Thời gian phòng sẵn sàng sau suất mới (cần giãn cách 30 phút dọn rạp)
        $newOccupiedEnd = $endTime->copy()->addMinutes(30);

        // Check xung đột lịch chiếu trong cùng phòng (yêu cầu giãn cách tối thiểu 30 phút)
        $existingShows = Show::with(['movie', 'room'])
            ->where('room_id', $validated['room_id'])
            ->get();

        foreach ($existingShows as $existingShow) {
            $exStart = \Carbon\Carbon::parse($existingShow->start_time);
            $exDuration = (int) ($existingShow->movie?->duration_minutes ?? 120);
            $exEnd = $exStart->copy()->addMinutes($exDuration);
            // Suất cũ chiếm phòng đến khi hoàn tất dọn rạp (+30 phút)
            $exOccupiedEnd = $exEnd->copy()->addMinutes(30);

            // Hai khoảng chiếm dụng [startTime, newOccupiedEnd] và [exStart, exOccupiedEnd]
            if ($startTime->lt($exOccupiedEnd) && $newOccupiedEnd->gt($exStart)) {
                $cStartStr = $exStart->format('H:i');
                $cEndStr = $exEnd->format('H:i');
                $cNextAvailableStr = $exOccupiedEnd->format('H:i');
                $cDateStr = $exStart->format('d/m/Y');
                $roomName = $existingShow->room?->name ?? "Phòng #{$validated['room_id']}";
                $movieTitle = $existingShow->movie?->title ?? 'Suất chiếu khác';

                if ($startTime->gte($exStart) && $startTime->lt($exOccupiedEnd)) {
                    $msg = "Xung đột lịch chiếu! {$roomName} đã có suất chiếu phim '{$movieTitle}' ({$cStartStr} - {$cEndStr}) ngày {$cDateStr}. Quy định cần giãn cách 30 phút dọn rạp, suất tiếp theo chỉ được đặt từ {$cNextAvailableStr} trở đi.";
                } elseif ($newOccupiedEnd->gt($exStart) && $startTime->lt($exStart)) {
                    $earliestFinishNeeded = $exStart->copy()->subMinutes(30)->format('H:i');
                    $msg = "Xung đột lịch chiếu! {$roomName} đã có suất chiếu phim '{$movieTitle}' lúc {$cStartStr} ngày {$cDateStr}. Suất mới của bạn (kết thúc lúc {$endTime->format('H:i')}) cần 30 phút dọn rạp nên phải kết thúc trước {$earliestFinishNeeded}.";
                } else {
                    $msg = "Trùng lịch chiếu tại {$roomName}! Đã có suất chiếu phim '{$movieTitle}' ({$cStartStr} - {$cEndStr}) ngày {$cDateStr}. Các suất chiếu trong cùng phòng luôn phải giãn cách tối thiểu 30 phút.";
                }

                return response()->json(['message' => $msg], 422);
            }
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
     * [Admin] Cập nhật suất chiếu (check giãn cách 30 phút).
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

        $movieId = $validated['movie_id'] ?? $show->movie_id;
        $roomId = $validated['room_id'] ?? $show->room_id;
        $startTime = isset($validated['start_time']) ? \Carbon\Carbon::parse($validated['start_time']) : \Carbon\Carbon::parse($show->start_time);
        $movie = Movie::findOrFail($movieId);
        $duration = (int) ($movie->duration_minutes ?? 120);
        $endTime = $startTime->copy()->addMinutes($duration);
        $newOccupiedEnd = $endTime->copy()->addMinutes(30);

        // Check xung đột với các suất khác
        $existingShows = Show::with(['movie', 'room'])
            ->where('room_id', $roomId)
            ->where('id', '!=', $id)
            ->get();

        foreach ($existingShows as $existingShow) {
            $exStart = \Carbon\Carbon::parse($existingShow->start_time);
            $exDuration = (int) ($existingShow->movie?->duration_minutes ?? 120);
            $exEnd = $exStart->copy()->addMinutes($exDuration);
            $exOccupiedEnd = $exEnd->copy()->addMinutes(30);

            if ($startTime->lt($exOccupiedEnd) && $newOccupiedEnd->gt($exStart)) {
                $cStartStr = $exStart->format('H:i');
                $cEndStr = $exEnd->format('H:i');
                $cNextAvailableStr = $exOccupiedEnd->format('H:i');
                $cDateStr = $exStart->format('d/m/Y');
                $roomName = $existingShow->room?->name ?? "Phòng #{$roomId}";
                $movieTitle = $existingShow->movie?->title ?? 'Suất chiếu khác';

                return response()->json([
                    'message' => "Xung đột lịch chiếu! {$roomName} đã có suất chiếu phim '{$movieTitle}' ({$cStartStr} - {$cEndStr}) ngày {$cDateStr}. Suất tiếp theo phải cách tối thiểu 30 phút dọn rạp (từ {$cNextAvailableStr} trở đi).",
                ], 422);
            }
        }

        $validated['end_time'] = $endTime;
        $show->update($validated);

        return response()->json(['data' => $show->fresh(['movie', 'room']), 'message' => 'Cập nhật suất chiếu thành công!']);
    }

    /**
     * [Admin] Lấy danh sách suất chiếu của một phòng theo ngày (giúp kiểm tra lịch trống & giãn cách).
     */
    public function getRoomSchedule(Request $request)
    {
        $roomId = $request->input('room_id');
        $date = $request->input('date');

        if (!$roomId || !$date) {
            return response()->json(['data' => []]);
        }

        $shows = Show::with(['movie', 'room'])
            ->where('room_id', $roomId)
            ->whereDate('start_time', $date)
            ->orderBy('start_time', 'asc')
            ->get()
            ->map(function ($s) {
                $start = \Carbon\Carbon::parse($s->start_time);
                $duration = (int) ($s->movie?->duration_minutes ?? 120);
                $end = $start->copy()->addMinutes($duration);
                $cleanEnd = $end->copy()->addMinutes(30);

                return [
                    'id' => $s->id,
                    'movie_title' => $s->movie?->title ?? 'Phim',
                    'duration' => $duration,
                    'start_time' => $start->format('H:i'),
                    'end_time' => $end->format('H:i'),
                    'clean_end_time' => $cleanEnd->format('H:i'),
                    'raw_start' => $s->start_time,
                    'raw_end' => $s->end_time,
                ];
            });

        return response()->json(['data' => $shows]);
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
