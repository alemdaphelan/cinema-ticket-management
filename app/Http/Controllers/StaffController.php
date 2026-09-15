<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    /**
     * Quét QR Code & Soát vé.
     */
    public function scanTicket(Request $request)
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
        ]);

        $qrData = json_decode($validated['qr_code'], true);

        if (! $qrData || ! isset($qrData['order_id'])) {
            return response()->json(['message' => 'Mã QR không hợp lệ.'], 422);
        }

        $order = Order::with(['show.movie', 'show.room', 'seats.seat', 'snacks.snack', 'user'])
            ->find($qrData['order_id']);

        if (! $order) {
            return response()->json(['message' => 'Không tìm thấy đơn hàng.'], 404);
        }

        if ($order->status === 'completed') {
            return response()->json([
                'message' => 'Vé này đã được sử dụng trước đó.',
                'data' => $order,
            ], 409);
        }

        if ($order->status !== 'paid') {
            return response()->json([
                'message' => 'Vé chưa được thanh toán hoặc đã bị hủy.',
                'data' => $order,
            ], 422);
        }

        // Cập nhật trạng thái
        $order->update(['status' => 'completed']);

        return response()->json([
            'message' => 'Soát vé thành công! Vé đã được đánh dấu "Đã sử dụng".',
            'data' => $order->fresh(['show.movie', 'show.room', 'seats.seat', 'snacks.snack']),
        ]);
    }
}
