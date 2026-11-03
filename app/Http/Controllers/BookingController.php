<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderSeat;
use App\Models\OrderSnack;
use App\Models\Show;
use App\Models\Snack;
use App\Models\Voucher;
use App\Jobs\ReleaseSeatJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class BookingController extends Controller
{
    /**
     * Bước 1: Chọn ghế & Giữ ghế (10 phút).
     */
    public function holdSeats(Request $request)
    {
        $validated = $request->validate([
            'show_id' => 'required|exists:shows,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'required|integer|exists:seats,id',
        ]);

        $showId = $validated['show_id'];
        $seatIds = $validated['seat_ids'];
        $userId = $request->user()?->id;

        $show = Show::findOrFail($showId);

        // Chống spam giữ ghế: Nếu tài khoản hủy/để hết hạn >= 3 đơn trong 15 phút gần nhất, tạm khóa đặt vé
        // Không phạt các đơn bị hủy do người dùng đổi ghế trong luồng đặt vé
        if ($userId) {
            $recentCancelledCount = Order::where('user_id', $userId)
                ->where('status', 'cancelled')
                ->where(function ($q) {
                    $q->whereNull('qr_code')
                      ->orWhere('qr_code', 'not like', '%reason:change_seats%');
                })
                ->where('updated_at', '>=', now()->subMinutes(15))
                ->count();

            if ($recentCancelledCount >= 3) {
                // Lấy đơn hủy gần nhất để tính thời gian còn lại của cooldown 15 phút
                $lastCancelled = Order::where('user_id', $userId)
                    ->where('status', 'cancelled')
                    ->where(function ($q) {
                        $q->whereNull('qr_code')
                          ->orWhere('qr_code', 'not like', '%reason:change_seats%');
                    })
                    ->latest('updated_at')
                    ->first();

                $cooldownEnds = $lastCancelled ? $lastCancelled->updated_at->addMinutes(15) : now();
                $minutesRemaining = max(1, (int) now()->diffInMinutes($cooldownEnds, false));

                return response()->json([
                    'message' => "Bạn đã hủy hoặc để hết hạn đơn giữ ghế quá nhiều lần. Để đảm bảo công bằng cho khán giả khác, tài khoản của bạn tạm thời bị giới hạn đặt vé trong {$minutesRemaining} phút nữa."
                ], 429);
            }
        }

        try {
            $order = DB::transaction(function () use ($showId, $seatIds, $userId, $show) {
                // Kiểm tra xem user có đơn pending hiện tại cho suất chiếu này không để kế thừa thời gian giữ chỗ
                $existingOrder = null;
                $previousHoldExpiresAt = null;

                if ($userId) {
                    $existingOrder = Order::where('user_id', $userId)
                        ->where('show_id', $showId)
                        ->where('status', 'pending')
                        ->where('hold_expires_at', '>', now())
                        ->latest()
                        ->first();

                    if ($existingOrder) {
                        $previousHoldExpiresAt = $existingOrder->hold_expires_at;
                    }
                }

                // Lock trực tiếp trên bảng seats (Tránh double booking khi chưa có dữ liệu OrderSeat)
                $lockedPhysicalSeats = \App\Models\Seat::whereIn('id', $seatIds)->lockForUpdate()->get();

                if ($lockedPhysicalSeats->count() !== count($seatIds)) {
                    throw new Exception('Một hoặc nhiều ghế không tồn tại!');
                }

                // Kiểm tra xem trong các ghế user chọn, có ghế nào đã có người khác đặt hoặc đang giữ không
                $alreadyBooked = OrderSeat::whereIn('seat_id', $seatIds)
                    ->whereHas('order', function ($q) use ($showId, $userId) {
                        $q->where('show_id', $showId)
                          ->where(function ($query) use ($userId) {
                              $query->whereIn('status', ['paid', 'completed'])
                                    ->orWhere(function ($sub) use ($userId) {
                                        $sub->where('status', 'pending')
                                            ->where('hold_expires_at', '>', now());
                                        if ($userId) {
                                            $sub->where('user_id', '!=', $userId);
                                        }
                                    });
                          });
                    })
                    ->exists();

                if ($alreadyBooked) {
                    throw new Exception('Một hoặc nhiều ghế bạn chọn vừa có người khác giữ chỗ hoặc đặt mua. Vui lòng chọn ghế khác!');
                }

                if ($userId) {
                    // Tự động hủy các đơn pending cũ của chính user này (đánh dấu là đổi ghế để không phạt spam)
                    Order::where('user_id', $userId)
                        ->where('status', 'pending')
                        ->update([
                            'status' => 'cancelled',
                            'qr_code' => 'reason:change_seats',
                        ]);
                }

                // Tính thời gian hết hạn: Nếu user đổi ghế khi đang trong phiên giữ ghế, giữ nguyên mốc đếm ngược cũ
                $holdExpiresAt = ($previousHoldExpiresAt && \Carbon\Carbon::parse($previousHoldExpiresAt)->isFuture())
                    ? $previousHoldExpiresAt
                    : now()->addMinutes(10);

                // Tính giá vé cho từng ghế
                $basePrice = $show->price;

                $order = Order::create([
                    'user_id' => $userId,
                    'show_id' => $showId,
                    'status' => 'pending',
                    'hold_expires_at' => $holdExpiresAt,
                    'total_amount' => 0,
                ]);

                $totalAmount = 0;
                foreach ($lockedPhysicalSeats as $seat) {
                    $seatPrice = $seat->type === 'vip' ? $basePrice * 1.5 : $basePrice;
                    $totalAmount += $seatPrice;

                    OrderSeat::create([
                        'order_id' => $order->id,
                        'seat_id' => $seat->id,
                        'price' => $seatPrice,
                    ]);
                }

                $order->update(['total_amount' => $totalAmount]);

                return $order;
            });

            $remainingSeconds = max(1, (int) now()->diffInSeconds($order->hold_expires_at, false));
            ReleaseSeatJob::dispatch($order->id)->delay(now()->addSeconds($remainingSeconds));

            return response()->json([
                'message' => 'Giữ ghế thành công.',
                'data' => [
                    'order_id' => $order->id,
                    'total_amount' => $order->total_amount,
                    'hold_expires_at' => $order->hold_expires_at,
                ],
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 409);
        }
    }

    /**
     * Bước 2: Chọn dịch vụ bắp nước.
     */
    public function addSnacks(Request $request, int $orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('status', 'pending')
            ->where('hold_expires_at', '>', now())
            ->firstOrFail();

        $validated = $request->validate([
            'snacks' => 'nullable|array',
            'snacks.*.snack_id' => 'required|exists:snacks,id',
            'snacks.*.quantity' => 'required|integer|min:1',
        ]);

        // Xóa snacks cũ
        OrderSnack::where('order_id', $orderId)->delete();

        $snackTotal = 0;
        if (!empty($validated['snacks'])) {
            foreach ($validated['snacks'] as $item) {
                $snack = Snack::findOrFail($item['snack_id']);
                $price = $snack->price * $item['quantity'];
                $snackTotal += $price;

                OrderSnack::create([
                    'order_id' => $orderId,
                    'snack_id' => $item['snack_id'],
                    'quantity' => $item['quantity'],
                    'price' => $price,
                ]);
            }
        }

        // Tính lại tổng = ghế + snacks
        $seatTotal = $order->seats()->sum('price');
        $order->update(['total_amount' => $seatTotal + $snackTotal]);

        return response()->json([
            'message' => 'Thêm bắp nước thành công!',
            'data' => [
                'total_amount' => $order->fresh()->total_amount,
                'seat_total' => $seatTotal,
                'snack_total' => $snackTotal,
            ],
        ]);
    }

    /**
     * Bước 3: Áp dụng Voucher.
     */
    public function applyVoucher(Request $request, int $orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('status', 'pending')
            ->where('hold_expires_at', '>', now())
            ->firstOrFail();

        $validated = $request->validate([
            'voucher_code' => 'required|string',
        ]);

        $voucher = Voucher::where('code', $validated['voucher_code'])
            ->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_to', '>=', now())
            ->first();

        if (! $voucher) {
            return response()->json(['message' => 'Mã voucher không hợp lệ hoặc đã hết hạn.'], 422);
        }

        // Check số lần sử dụng
        $usedCount = Order::where('user_id', $order->user_id)
            ->where('voucher_id', $voucher->id)
            ->whereIn('status', ['paid', 'completed'])
            ->count();

        if ($usedCount >= $voucher->max_uses_per_user) {
            return response()->json(['message' => 'Bạn đã sử dụng hết số lần cho phép của voucher này.'], 422);
        }

        // Tính tổng trước giảm
        $seatTotal = $order->seats()->sum('price');
        $snackTotal = $order->snacks()->sum('price');
        $subtotal = $seatTotal + $snackTotal;

        if ($subtotal < $voucher->min_order_value) {
            return response()->json([
                'message' => 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($voucher->min_order_value) . 'đ để áp dụng voucher.',
            ], 422);
        }

        // Tính giảm giá
        $discount = 0;
        if ($voucher->discount_type === 'percent') {
            $discount = $subtotal * ($voucher->discount_value / 100);
        } else {
            $discount = $voucher->discount_value;
        }

        $finalTotal = max(0, $subtotal - $discount);

        $order->update([
            'voucher_id' => $voucher->id,
            'total_amount' => $finalTotal,
        ]);

        return response()->json([
            'message' => 'Áp dụng voucher thành công!',
            'data' => [
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total_amount' => $finalTotal,
                'voucher_code' => $voucher->code,
            ],
        ]);
    }

    /**
     * Bước 4: Thanh toán (Mock - giả lập).
     */
    public function pay(Request $request, int $orderId)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:vnpay,momo,cash',
        ]);

        try {
            return DB::transaction(function () use ($orderId, $validated) {
                // Lock row order để tranh chấp với ReleaseSeatJob
                $order = Order::where('id', $orderId)->lockForUpdate()->firstOrFail();

                if ($order->status !== 'pending' || $order->hold_expires_at <= now()) {
                    return response()->json(['message' => 'Đơn hàng đã hết hạn hoặc không ở trạng thái chờ thanh toán.'], 400);
                }

                // Mock payment: Thanh toán luôn thành công
                $qrContent = json_encode([
                    'order_id' => $order->id,
                    'code' => 'CINEMA-' . strtoupper(Str::random(8)),
                ]);

                $order->update([
                    'status' => 'paid',
                    'payment_method' => $validated['payment_method'],
                    'qr_code' => $qrContent,
                ]);

                return response()->json([
                    'message' => 'Thanh toán thành công!',
                    'data' => [
                        'order_id' => $order->id,
                        'status' => 'paid',
                        'qr_code' => $qrContent,
                        'total_amount' => $order->total_amount,
                    ],
                ]);
            });
        } catch (Exception $e) {
            return response()->json(['message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Hủy đơn hàng ngay lập tức (khi người dùng thoát trang)
     */
    public function cancelOrder(Request $request, int $orderId)
    {
        try {
            $showId = null;
            DB::transaction(function () use ($orderId, $request, &$showId) {
                $order = Order::where('id', $orderId)
                    ->where('user_id', $request->user()->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $showId = $order->show_id;

                if ($order->status === 'pending') {
                    $reason = $request->input('reason');
                    $qrCodeValue = ($reason === 'change_seats') ? 'reason:change_seats' : null;

                    $order->update([
                        'status' => 'cancelled',
                        'qr_code' => $qrCodeValue,
                    ]);
                }
            });

            return response()->json([
                'message' => 'Đã hủy đơn hàng',
                'data' => [
                    'order_id' => $orderId,
                    'show_id' => $showId,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Lịch sử đặt vé của user.
     */
    public function orderHistory(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['show.movie', 'show.room', 'seats.seat', 'snacks.snack'])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($orders as $order) {
            if ($order->status === 'pending' && $order->hold_expires_at && now()->greaterThan($order->hold_expires_at)) {
                $order->update(['status' => 'cancelled']);
            }
        }

        return response()->json(['data' => $orders]);
    }

    /**
     * Chi tiết đơn hàng.
     */
    public function orderDetail(int $orderId)
    {
        $order = Order::with(['show.movie', 'show.room', 'seats.seat', 'snacks.snack', 'voucher'])
            ->findOrFail($orderId);

        return response()->json(['data' => $order]);
    }
}
