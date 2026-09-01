<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderSeat;
use App\Jobs\ReleaseSeatJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class BookingController extends Controller
{
    public function holdSeats(Request $request)
    {
        $showId = $request->input('show_id');
        $seatIds = $request->input('seat_ids');
        $userId = $request->user()?->id;

        try {
            $order = DB::transaction(function () use ($showId, $seatIds, $userId) {
                $lockedSeats = OrderSeat::whereIn('seat_id', $seatIds)
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
                    ->lockForUpdate()
                    ->get();
                
                if ($lockedSeats->isNotEmpty()) {
                    throw new Exception('Mot trong cac ghe ban chon da co nguoi dat!');
                }

                $order = Order::create([
                    'user_id' => $userId,
                    'show_id' => $showId,
                    'status' => 'pending',
                    'hold_expires_at' => now()->addMinutes(10),
                    'total_amount' => 0,
                ]);

                foreach ($seatIds as $seatId) {
                    OrderSeat::create([
                        'order_id' => $order->id,
                        'seat_id' => $seatId,
                        'price' => 0 
                    ]);
                }

                return $order;
            });

            ReleaseSeatJob::dispatch($order->id)->delay(now()->addMinutes(10));

            return response()->json([
                'message' => 'Giu ghe thanh cong trong 10 phut.',
                'order_id' => $order->id,
                'hold_expires_at' => $order->hold_expires_at,
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 409);
        }
    }
}
