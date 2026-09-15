<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Movie;
use App\Models\Show;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * [Admin] Danh sách đơn hàng.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'show.movie', 'show.room']);

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        return response()->json(['data' => $orders]);
    }

    /**
     * [Admin] Thống kê tổng quan.
     */
    public function statistics()
    {
        $today = now()->toDateString();

        $todayRevenue = Order::whereIn('status', ['paid', 'completed'])
            ->whereDate('created_at', $today)
            ->sum('total_amount');

        $todayTickets = Order::whereIn('status', ['paid', 'completed'])
            ->whereDate('created_at', $today)
            ->count();

        $totalRevenue = Order::whereIn('status', ['paid', 'completed'])
            ->sum('total_amount');

        $totalTickets = Order::whereIn('status', ['paid', 'completed'])
            ->count();

        $moviesShowing = Movie::where('status', 'showing')->count();

        $upcomingShows = Show::where('start_time', '>=', now())
            ->whereDate('start_time', $today)
            ->count();

        return response()->json([
            'data' => [
                'today_revenue' => $todayRevenue,
                'today_tickets' => $todayTickets,
                'total_revenue' => $totalRevenue,
                'total_tickets' => $totalTickets,
                'movies_showing' => $moviesShowing,
                'upcoming_shows_today' => $upcomingShows,
            ],
        ]);
    }
}
