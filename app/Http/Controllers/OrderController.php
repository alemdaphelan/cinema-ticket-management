<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Movie;
use App\Models\Show;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Build base query with filters for orders.
     */
    protected function buildFilteredOrderQuery(Request $request)
    {
        $query = Order::with(['user', 'show.movie', 'show.room', 'seats.seat']);

        // 1. Lọc theo trạng thái
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        // 2. Lọc theo tên phim
        if ($request->filled('movie_title')) {
            $movieTitle = trim($request->input('movie_title'));
            $query->whereHas('show.movie', function ($q) use ($movieTitle) {
                $q->where('title', 'like', "%{$movieTitle}%");
            });
        }

        // 3. Lọc theo người dùng (Tên, Email hoặc User ID)
        if ($request->filled('user_search')) {
            $userSearch = trim($request->input('user_search'));
            $query->where(function ($q) use ($userSearch) {
                $q->where('user_id', $userSearch)
                  ->orWhereHas('user', function ($uq) use ($userSearch) {
                      $uq->where('name', 'like', "%{$userSearch}%")
                         ->orWhere('email', 'like', "%{$userSearch}%");
                  });
            });
        } elseif ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // 4. Lọc theo thời gian (ngày / tháng / năm / khoảng ngày)
        $periodType = $request->input('period_type', 'all');
        if ($periodType === 'day' || $request->filled('date')) {
            $date = $request->input('date', now()->toDateString());
            $query->whereDate('created_at', $date);
        } elseif ($periodType === 'month' || $request->filled('month')) {
            $month = $request->input('month', now()->format('Y-m'));
            if (preg_match('/^(\d{4})-(\d{2})$/', $month, $matches)) {
                $query->whereYear('created_at', $matches[1])
                      ->whereMonth('created_at', $matches[2]);
            }
        } elseif ($periodType === 'year' || $request->filled('year')) {
            $year = $request->input('year', now()->format('Y'));
            $query->whereYear('created_at', $year);
        } elseif ($periodType === 'custom' || ($request->filled('from_date') && $request->filled('to_date'))) {
            $from = $request->input('from_date');
            $to = $request->input('to_date');
            if ($from && $to) {
                $query->whereDate('created_at', '>=', $from)
                      ->whereDate('created_at', '<=', $to);
            }
        }

        return $query;
    }

    /**
     * [Admin] Danh sách đơn hàng hỗ trợ phân trang & lọc đa tiêu chí.
     */
    public function index(Request $request)
    {
        $query = $this->buildFilteredOrderQuery($request);

        // Hỗ trợ phân trang, mặc định 10 đơn / trang
        $perPage = (int) $request->input('per_page', 10);
        if ($perPage <= 0) {
            $perPage = 10;
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($orders);
    }

    /**
     * [Admin] Xuất danh sách đơn hàng sang file Excel (CSV UTF-8 with BOM mở trực tiếp trên Excel).
     */
    public function exportExcel(Request $request)
    {
        $query = $this->buildFilteredOrderQuery($request);
        $orders = $query->orderBy('created_at', 'desc')->get();

        $filename = 'danh-sach-don-hang-' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $statusMap = [
            'pending' => 'Chờ thanh toán',
            'paid' => 'Đã thanh toán',
            'completed' => 'Hoàn tất',
            'cancelled' => 'Đã hủy',
        ];

        $callback = function () use ($orders, $statusMap) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Microsoft Excel can display Vietnamese characters correctly
            fputs($handle, "\xEF\xBB\xBF");

            // CSV Header Row
            fputcsv($handle, [
                'Mã đơn hàng',
                'Khách hàng',
                'Email',
                'Tên phim',
                'Phòng chiếu',
                'Suất chiếu',
                'Ghế đã chọn',
                'Phương thức thanh toán',
                'Tổng tiền (VNĐ)',
                'Trạng thái',
                'Ngày tạo đơn',
            ]);

            foreach ($orders as $order) {
                $seats = $order->seats->map(function ($orderSeat) {
                    $seat = $orderSeat->seat;
                    if (! $seat) {
                        return '';
                    }
                    $row = $seat->row_label ?? $seat->row ?? '';
                    $number = $seat->seat_number ?? $seat->number ?? '';
                    return $row . $number;
                })->filter()->implode(', ');

                $paymentMethod = $order->payment_method ? strtoupper($order->payment_method) : 'Chưa chọn';
                if (strtolower($order->payment_method) === 'cash') {
                    $paymentMethod = 'Tiền mặt';
                }

                $statusText = $statusMap[$order->status] ?? $order->status;
                $startTime = $order->show?->start_time ? date('H:i d/m/Y', strtotime($order->show->start_time)) : '—';
                $createdAt = $order->created_at ? $order->created_at->format('H:i d/m/Y') : '—';

                fputcsv($handle, [
                    '#' . $order->id,
                    $order->user?->name ?? 'Khách vãng lai',
                    $order->user?->email ?? '—',
                    $order->show?->movie?->title ?? '—',
                    $order->show?->room?->name ?? '—',
                    $startTime,
                    $seats ?: '—',
                    $paymentMethod,
                    $order->total_amount,
                    $statusText,
                    $createdAt,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * [Admin] Thống kê tổng quan & nâng cao (lọc theo ngày/tháng/năm, nhóm theo ngày/tuần/tháng/năm, chi tiết chuyển khoản/tiền mặt).
     */
    public function statistics(Request $request)
    {
        $today = now()->toDateString();

        // 1. Chỉ số tổng quan nhanh
        $todayOrders = Order::whereIn('status', ['paid', 'completed'])
            ->whereDate('created_at', $today);
        $todayRevenue = (float) $todayOrders->sum('total_amount');
        $todayTickets = $todayOrders->count();

        $allTimePaidOrders = Order::whereIn('status', ['paid', 'completed']);
        $totalRevenue = (float) $allTimePaidOrders->sum('total_amount');
        $totalTickets = $allTimePaidOrders->count();

        $moviesShowing = Movie::where('status', 'showing')->count();
        $upcomingShows = Show::where('start_time', '>=', now())
            ->whereDate('start_time', $today)
            ->count();

        // 2. Lọc thống kê chi tiết theo tham số
        // period_type: 'day' (theo ngày cụ thể), 'week' (theo tuần), 'month' (theo tháng), 'year' (theo năm), 'all'
        // date: 'YYYY-MM-DD'
        // month: 'YYYY-MM'
        // year: 'YYYY'
        $groupBy = $request->input('group_by', 'day'); // 'day', 'week', 'month', 'year'
        $periodType = $request->input('period_type', 'all');

        $statsQuery = Order::whereIn('status', ['paid', 'completed']);

        if ($periodType === 'day' && $request->filled('date')) {
            $statsQuery->whereDate('created_at', $request->input('date'));
        } elseif ($periodType === 'month' && $request->filled('month')) {
            $monthParts = explode('-', $request->input('month'));
            if (count($monthParts) === 2) {
                $statsQuery->whereYear('created_at', $monthParts[0])
                           ->whereMonth('created_at', $monthParts[1]);
            }
        } elseif ($periodType === 'year' && $request->filled('year')) {
            $statsQuery->whereYear('created_at', $request->input('year'));
        } elseif ($request->filled('from_date') && $request->filled('to_date')) {
            $statsQuery->whereBetween('created_at', [
                $request->input('from_date') . ' 00:00:00',
                $request->input('to_date') . ' 23:59:59'
            ]);
        }

        // Clone query để tính tổng cho kỳ được lọc
        $filteredRevenue = (float) (clone $statsQuery)->sum('total_amount');
        $filteredTickets = (clone $statsQuery)->count();

        // Thống kê chi tiết theo phương thức thanh toán trong kỳ được lọc
        $paymentMethodsData = (clone $statsQuery)
            ->selectRaw('payment_method, count(*) as count, sum(total_amount) as total')
            ->groupBy('payment_method')
            ->get();

        $cashRevenue = 0;
        $cashCount = 0;
        $transferRevenue = 0; // Chuyển khoản (VNPay, MoMo, ngân hàng...)
        $transferCount = 0;

        foreach ($paymentMethodsData as $pm) {
            $method = strtolower($pm->payment_method ?? 'cash');
            $sum = (float) $pm->total;
            $cnt = (int) $pm->count;

            if ($method === 'cash' || $method === 'tiền mặt' || $method === 'tien_mat') {
                $cashRevenue += $sum;
                $cashCount += $cnt;
            } else {
                $transferRevenue += $sum;
                $transferCount += $cnt;
            }
        }

        // 3. Chuẩn bị dữ liệu cho Chart theo group_by (day, week, month, year)
        $chartOrders = (clone $statsQuery)
            ->orderBy('created_at', 'asc')
            ->get(['created_at', 'total_amount', 'payment_method']);

        $groupedChartData = [];

        foreach ($chartOrders as $order) {
            $createdAt = \Carbon\Carbon::parse($order->created_at);
            $key = '';
            $label = '';

            if ($groupBy === 'week') {
                $key = $createdAt->format('o-\WW');
                $label = 'Tuần ' . $createdAt->format('W/Y');
            } elseif ($groupBy === 'month') {
                $key = $createdAt->format('Y-m');
                $label = 'Tháng ' . $createdAt->format('m/Y');
            } elseif ($groupBy === 'year') {
                $key = $createdAt->format('Y');
                $label = 'Năm ' . $createdAt->format('Y');
            } else {
                // Default: day
                $key = $createdAt->format('Y-m-d');
                $label = $createdAt->format('d/m/Y');
            }

            if (!isset($groupedChartData[$key])) {
                $groupedChartData[$key] = [
                    'label' => $label,
                    'total_revenue' => 0,
                    'cash_revenue' => 0,
                    'transfer_revenue' => 0,
                    'tickets_count' => 0,
                ];
            }

            $amount = (float) $order->total_amount;
            $pm = strtolower($order->payment_method ?? 'cash');

            $groupedChartData[$key]['total_revenue'] += $amount;
            $groupedChartData[$key]['tickets_count'] += 1;

            if ($pm === 'cash' || $pm === 'tiền mặt' || $pm === 'tien_mat') {
                $groupedChartData[$key]['cash_revenue'] += $amount;
            } else {
                $groupedChartData[$key]['transfer_revenue'] += $amount;
            }
        }

        return response()->json([
            'data' => [
                'today_revenue' => $todayRevenue,
                'today_tickets' => $todayTickets,
                'total_revenue' => $totalRevenue,
                'total_tickets' => $totalTickets,
                'movies_showing' => $moviesShowing,
                'upcoming_shows_today' => $upcomingShows,
                'filtered' => [
                    'total_revenue' => $filteredRevenue,
                    'total_tickets' => $filteredTickets,
                    'cash_revenue' => $cashRevenue,
                    'cash_tickets' => $cashCount,
                    'transfer_revenue' => $transferRevenue,
                    'transfer_tickets' => $transferCount,
                ],
                'chart' => array_values($groupedChartData),
            ],
        ]);
    }
}
