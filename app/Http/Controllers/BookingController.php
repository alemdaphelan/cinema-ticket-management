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

        try {
            $order = DB::transaction(function () use ($showId, $seatIds, $userId, $show) {
                // Lock trực tiếp trên bảng seats (Tránh double booking khi chưa có dữ liệu OrderSeat)
                $lockedPhysicalSeats = \App\Models\Seat::whereIn('id', $seatIds)->lockForUpdate()->get();

                if ($lockedPhysicalSeats->count() !== count($seatIds)) {
                    throw new Exception('Một hoặc nhiều ghế không tồn tại!');
                }

                $alreadyBooked = OrderSeat::whereIn('seat_id', $seatIds)
                    ->whereHas('order', function ($q) use ($showId) {
                        $q->where('show_id', $showId)
                          ->where(function ($query) {
                              $query->whereIn('status', ['paid', 'completed'])
                                    ->orWhere(function ($sub) {
                                        $sub->where('status', 'pending')
                                            ->where('hold_expires_at', '>', now());
                                    });
                          });
                    })
                    ->exists();

                if ($alreadyBooked) {
                    throw new Exception('Một trong các ghế bạn chọn đã có người đặt!');
                }

                // Tính giá vé cho từng ghế
                $basePrice = $show->price;

                $order = Order::create([
                    'user_id' => $userId,
                    'show_id' => $showId,
                    'status' => 'pending',
                    'hold_expires_at' => now()->addMinutes(10),
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

            ReleaseSeatJob::dispatch($order->id)->delay(now()->addMinutes(10));

            return response()->json([
                'message' => 'Giữ ghế thành công trong 10 phút.',
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
            'snacks' => 'required|array',
            'snacks.*.snack_id' => 'required|exists:snacks,id',
            'snacks.*.quantity' => 'required|integer|min:1',
        ]);

        // Xóa snacks cũ
        OrderSnack::where('order_id', $orderId)->delete();

        $snackTotal = 0;
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
