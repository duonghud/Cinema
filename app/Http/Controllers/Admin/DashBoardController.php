<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\admin;
use App\Models\Admin\customer;
use App\Models\Admin\foodInvoice;
use App\Models\Admin\foodInvoiceDetail;
use App\Models\Admin\movie;
use App\Models\Admin\screeningRoom;
use App\Models\Admin\showTime;
use App\Models\Admin\ticket;
use Illuminate\Support\Carbon;
use App\Models\Admin\invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashBoardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $stats = [
            'totalMovies'        => movie::count(),
            'totalCustomers'     => customer::count(),
            'totalRooms'         => screeningRoom::count(),
            'totalAdmins'        => admin::count(),
            'todayShowTimes'     => showTime::whereDate('showDate', $today)->count(),
            'soldTickets'        => ticket::count(),
            'todayTicketRevenue' => (float) ticket::whereHas('showTime', function ($q) use ($today) {
                $q->whereDate('showDate', $today);
            })->sum('price'),
            'todayFoodRevenue'   => (float) foodInvoice::whereDate('orderDate', $today)->sum('total'),
        ];

        $upcomingShowTimes = showTime::with(['movie', 'room'])
            ->whereDate('showDate', '>=', $today)
            ->orderBy('showDate')->orderBy('startTime')
            ->limit(6)->get();

        $latestMovies = movie::with(['ageRating', 'studio'])
            ->orderByDesc('releaseDate')->limit(5)->get();

        return view('Admins.DashBoard.index', compact('stats', 'upcomingShowTimes', 'latestMovies'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Doanh thu theo tháng (trong một năm)
    // ─────────────────────────────────────────────────────────────────────────
    public function revenueByMonth(Request $request)
    {
        $year        = (int) $request->query('year', now()->year);
        $yearOptions = range(now()->year - 4, now()->year + 1);

        if ($year < 2000 || $year > now()->year + 5) {
            $year = now()->year;
        }

        $ticketRevenue = invoice::selectRaw('MONTH(createDate) as period, SUM(totalAmount) as total')
            ->whereYear('createDate', $year)
            ->groupByRaw('MONTH(createDate)')
            ->pluck('total', 'period');

        $foodRevenue = foodInvoice::selectRaw('MONTH(orderDate) as period, SUM(total) as total')
            ->whereYear('orderDate', $year)
            ->groupByRaw('MONTH(orderDate)')
            ->pluck('total', 'period');

        $rows = collect(range(1, 12))->map(function ($month) use ($ticketRevenue, $foodRevenue, $year) {
            $ticket = (float) ($ticketRevenue[$month] ?? 0);
            $food   = (float) ($foodRevenue[$month]   ?? 0);
            return [
                'label'         => 'Tháng ' . $month,
                'period'        => $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT),
                'ticketRevenue' => $ticket,
                'foodRevenue'   => $food,
                'totalRevenue'  => $ticket + $food,
            ];
        });

        [$topMovie, $bottomMovie, $movieList] = $this->getMovieHighlights(
            Carbon::createFromFormat('Y', $year)->startOfYear(),
            Carbon::createFromFormat('Y', $year)->endOfYear()
        );

        [$topFood, $bottomFood, $foodList] = $this->getFoodHighlights(
            Carbon::createFromFormat('Y', $year)->startOfYear(),
            Carbon::createFromFormat('Y', $year)->endOfYear()
        );

        $summary = [
            'ticketRevenue' => $rows->sum('ticketRevenue'),
            'foodRevenue'   => $rows->sum('foodRevenue'),
            'totalRevenue'  => $rows->sum('totalRevenue'),
        ];

        return view('Admins.DashBoard.revenue.index', [
            'reportTitle'       => 'Thống kê doanh thu theo tháng',
            'reportDescription' => 'Tổng hợp doanh thu vé và đồ ăn theo từng tháng trong năm ' . $year . '.',
            'filterLabel'       => 'Năm',
            'filterValue'       => $year,
            'filterOptions'     => $yearOptions,
            'filterKey'         => 'year',
            'rows'              => $rows,
            'summary'           => $summary,
            'topMovie'          => $topMovie,
            'bottomMovie'       => $bottomMovie,
            'movieList'         => $movieList,
            'topFood'           => $topFood,
            'bottomFood'        => $bottomFood,
            'foodList'          => $foodList,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Doanh thu theo ngày (trong một tháng)
    // ─────────────────────────────────────────────────────────────────────────
    public function revenueByDay(Request $request)
    {
        $monthInput = $request->query('month', now()->format('Y-m'));

        if (! preg_match('/^\d{4}-\d{2}$/', $monthInput)) {
            $monthInput = now()->format('Y-m');
        }

        try {
            $selectedMonth = Carbon::createFromFormat('Y-m', $monthInput);
        } catch (\Exception $e) {
            Log::warning('revenueByDay: invalid month input', ['input' => $monthInput]);
            $selectedMonth = Carbon::now();
        }

        $startOfMonth = $selectedMonth->copy()->startOfMonth();
        $endOfMonth   = $selectedMonth->copy()->endOfMonth();

        $ticketRevenue = invoice::selectRaw('DAY(createDate) as period, SUM(totalAmount) as total')
            ->whereBetween('createDate', [$startOfMonth, $endOfMonth])
            ->groupByRaw('DAY(createDate)')
            ->pluck('total', 'period');

        $foodRevenue = foodInvoice::selectRaw('DAY(orderDate) as period, SUM(total) as total')
            ->whereBetween('orderDate', [$startOfMonth, $endOfMonth])
            ->groupByRaw('DAY(orderDate)')
            ->pluck('total', 'period');

        $rows = collect(range(1, $selectedMonth->daysInMonth))->map(
            function ($day) use ($ticketRevenue, $foodRevenue, $selectedMonth) {
                $ticket = (float) ($ticketRevenue[$day] ?? 0);
                $food   = (float) ($foodRevenue[$day]   ?? 0);
                $date   = $selectedMonth->copy()->day($day);
                return [
                    'label'         => $date->format('d/m/Y'),
                    'period'        => $date->format('d/m/Y'),
                    'ticketRevenue' => $ticket,
                    'foodRevenue'   => $food,
                    'totalRevenue'  => $ticket + $food,
                ];
            }
        );

        [$topMovie, $bottomMovie, $movieList] = $this->getMovieHighlights($startOfMonth, $endOfMonth);
        [$topFood,  $bottomFood,  $foodList]  = $this->getFoodHighlights($startOfMonth, $endOfMonth);

        $summary = [
            'ticketRevenue' => $rows->sum('ticketRevenue'),
            'foodRevenue'   => $rows->sum('foodRevenue'),
            'totalRevenue'  => $rows->sum('totalRevenue'),
        ];

        return view('Admins.DashBoard.revenue.index', [
            'reportTitle'       => 'Thống kê doanh thu theo ngày',
            'reportDescription' => 'Tổng hợp doanh thu vé và đồ ăn theo từng ngày trong tháng ' . $selectedMonth->format('m/Y') . '.',
            'filterLabel'       => 'Tháng',
            'filterValue'       => $selectedMonth->format('Y-m'),
            'filterOptions'     => [],
            'filterKey'         => 'month',
            'rows'              => $rows,
            'summary'           => $summary,
            'topMovie'          => $topMovie,
            'bottomMovie'       => $bottomMovie,
            'movieList'         => $movieList,
            'topFood'           => $topFood,
            'bottomFood'        => $bottomFood,
            'foodList'          => $foodList,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // AJAX – hóa đơn theo kỳ, hỗ trợ lọc type: all | ticket | food
    // ─────────────────────────────────────────────────────────────────────────
    public function invoicesByPeriod(Request $request)
    {
        $period = trim((string) $request->query('period'));
        $type   = $request->query('type', 'all');

        if (! in_array($type, ['all', 'ticket', 'food'])) {
            $type = 'all';
        }

        [$start, $end] = $this->parsePeriod($period);
        if (! $start) {
            return response()->json(['error' => 'Định dạng kỳ không hợp lệ. Vui lòng thử lại.'], 422);
        }

        try {
            // ── Ticket invoices ───────────────────────────────────────────
            $invoiceList = collect();
            if (in_array($type, ['all', 'ticket'])) {
                $invoiceList = invoice::with(['customer', 'paymentMethod'])
                    ->whereBetween('createDate', [$start, $end])
                    ->orderByDesc('createDate')
                    ->get()
                    ->map(fn ($inv) => [
                        'invoiceID'     => $inv->invoiceID,
                        'customer'      => $inv->customer->fullName ?? 'Khách vãng lai',
                        // FIX TASK 1: dùng đúng tên relation paymentMethod() thay vì payment()
                        'paymentMethod' => $inv->paymentMethod->name ?? '---',
                        'createDate'    => Carbon::parse($inv->createDate)->format('d/m/Y'),
                        'totalAmount'   => (float) $inv->totalAmount,
                        'type'          => 'ticket',
                    ]);
            }

            // ── Food invoices ─────────────────────────────────────────────
            $foodList = collect();
            if (in_array($type, ['all', 'food'])) {
                $foodList = foodInvoice::with(['customer', 'paymentMethod'])
                    ->whereBetween('orderDate', [$start, $end])
                    ->orderByDesc('orderDate')
                    ->get()
                    ->map(fn ($fi) => [
                        'invoiceID'     => $fi->foodInvoiceID,
                        'customer'      => $fi->customer->fullName ?? 'Khách vãng lai',
                        // FIX TASK 1: foodInvoice model có paymentMethod(), không phải payment()
                        'paymentMethod' => $fi->paymentMethod->name ?? '---',
                        'createDate'    => Carbon::parse($fi->orderDate)->format('d/m/Y'),
                        'totalAmount'   => (float) $fi->total,
                        'type'          => 'food',
                    ]);
            }

            $ticketRevenue = $invoiceList->sum('totalAmount');
            $foodRevenue   = $foodList->sum('totalAmount');

            $allInvoices = $invoiceList->concat($foodList)
                ->sortByDesc(fn ($i) => Carbon::createFromFormat('d/m/Y', $i['createDate'])->timestamp)
                ->values();

            $topMovie = $bottomMovie = $topFood = $bottomFood = null;
            $movieList = $foodList2 = [];

            if (in_array($type, ['all', 'ticket'])) {
                [$topMovie, $bottomMovie, $movieList] = $this->getMovieHighlights($start, $end);
            }
            if (in_array($type, ['all', 'food'])) {
                [$topFood, $bottomFood, $foodList2] = $this->getFoodHighlights($start, $end);
            }

            return response()->json([
                'ticketRevenue' => $ticketRevenue,
                'foodRevenue'   => $foodRevenue,
                'totalRevenue'  => $ticketRevenue + $foodRevenue,
                'invoices'      => $allInvoices,
                'type'          => $type,
                'topMovie'      => $topMovie,
                'bottomMovie'   => $bottomMovie,
                'movieList'     => $movieList,   // TASK 2: danh sách phim đầy đủ
                'topFood'       => $topFood,
                'bottomFood'    => $bottomFood,
                'foodList'      => $foodList2,   // TASK 2: danh sách món ăn đầy đủ
            ]);

        } catch (\Throwable $e) {
            Log::error('invoicesByPeriod error: ' . $e->getMessage(), [
                'period' => $period,
                'type'   => $type,
            ]);
            return response()->json(['error' => 'Đã xảy ra lỗi khi tải dữ liệu hóa đơn.'], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Parse period string → [Carbon $start, Carbon $end] hoặc [null, null]
     */
    private function parsePeriod(string $period): array
    {
        try {
            if (preg_match('/^\d{4}$/', $period)) {
                $start = Carbon::createFromFormat('Y', $period)->startOfYear();
                return [$start, $start->copy()->endOfYear()];
            }
            if (preg_match('/^\d{4}-\d{2}$/', $period)) {
                $start = Carbon::createFromFormat('Y-m', $period)->startOfMonth();
                return [$start, $start->copy()->endOfMonth()];
            }
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $period)) {
                $start = Carbon::createFromFormat('d/m/Y', $period)->startOfDay();
                return [$start, $start->copy()->endOfDay()];
            }
        } catch (\Exception $e) {
            Log::warning('parsePeriod failed', ['period' => $period, 'error' => $e->getMessage()]);
        }

        return [null, null];
    }

    /**
     * FIX TASK 2: Top/bottom phim + toàn bộ danh sách theo doanh thu vé
     * - Trả về array 3 phần tử: [topMovie, bottomMovie, movieList]
     * - FIX lỗi min/max: chỉ tính phim có doanh thu > 0 (loại phim chưa bán vé)
     *   để tránh hiển thị "Không rõ" hoặc phim trùng top/bottom
     */
    private function getMovieHighlights(Carbon $start, Carbon $end): array
    {
        $stats = ticket::with('showTime.movie')
            ->whereHas('showTime', fn ($q) => $q->whereBetween(
                'showDate', [$start->toDateString(), $end->toDateString()]
            ))
            ->get()
            ->groupBy(fn ($t) => optional(optional($t->showTime)->movie)->movieID)
            ->filter(fn ($g, $id) => $id !== null) // bỏ ticket không có movie
            ->map(function ($tickets) {
                $movie = optional($tickets->first()->showTime)->movie;
                return [
                    'movieName' => $movie->movieTitle ?? 'Không rõ',
                    'revenue'   => (float) $tickets->sum('price'),
                    'count'     => $tickets->count(),
                ];
            })
            // FIX: chỉ giữ phim có revenue > 0 trước khi sort → tránh bottom = 0đ
            ->filter(fn ($item) => $item['revenue'] > 0)
            ->sortByDesc('revenue')
            ->values();

        $top    = $stats->first();  // phim doanh thu cao nhất
        // FIX: bottomMovie chỉ hiển thị khi có ít nhất 2 phim khác nhau
        $bottom = $stats->count() >= 2 ? $stats->last() : null;

        // Toàn bộ danh sách để hiển thị trong modal
        $list = $stats->map(fn ($item, $idx) => array_merge($item, ['rank' => $idx + 1]))->values()->toArray();

        return [$top, $bottom, $list];
    }

    /**
     * FIX TASK 2: Top/bottom món ăn + toàn bộ danh sách theo doanh thu
     * - Trả về array 3 phần tử: [topFood, bottomFood, foodList]
     * - FIX: chỉ tính món có revenue > 0
     */
    private function getFoodHighlights(Carbon $start, Carbon $end): array
    {
        $stats = foodInvoiceDetail::with('food')
            ->whereHas('foodInvoice', fn ($q) => $q->whereBetween('orderDate', [$start, $end]))
            ->get()
            ->groupBy('foodID')
            ->map(function ($details) {
                $food = $details->first()->food;
                return [
                    'foodName' => optional($food)->foodName ?? 'Không rõ',
                    'revenue'  => $details->sum(fn ($d) => $d->quantity * (float) optional($d->food)->price),
                    'quantity' => (int) $details->sum('quantity'),
                ];
            })
            // FIX: chỉ giữ món có revenue > 0
            ->filter(fn ($item) => $item['revenue'] > 0)
            ->sortByDesc('revenue')
            ->values();

        $top    = $stats->first();
        $bottom = $stats->count() >= 2 ? $stats->last() : null;

        $list = $stats->map(fn ($item, $idx) => array_merge($item, ['rank' => $idx + 1]))->values()->toArray();

        return [$top, $bottom, $list];
    }
}