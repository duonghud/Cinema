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

class DashBoardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $stats = [
            'totalMovies'       => movie::count(),
            'totalCustomers'    => customer::count(),
            'totalRooms'        => screeningRoom::count(),
            'totalAdmins'       => admin::count(),
            'todayShowTimes'    => showTime::whereDate('showDate', $today)->count(),
            'soldTickets'       => ticket::count(),
            'todayTicketRevenue'=> (float) ticket::whereHas('showTime', function ($query) use ($today) {
                $query->whereDate('showDate', $today);
            })->sum('price'),
            'todayFoodRevenue'  => (float) foodInvoice::whereDate('orderDate', $today)->sum('total'),
        ];

        $upcomingShowTimes = showTime::with(['movie', 'room'])
            ->whereDate('showDate', '>=', $today)
            ->orderBy('showDate')
            ->orderBy('startTime')
            ->limit(6)
            ->get();

        $latestMovies = movie::with(['ageRating', 'studio'])
            ->orderByDesc('releaseDate')
            ->limit(5)
            ->get();

        return view('Admins.DashBoard.index', compact('stats', 'upcomingShowTimes', 'latestMovies'));
    }

    public function revenueByMonth(Request $request)
    {
        $year        = (int) $request->query('year', now()->year);
        $yearOptions = range(now()->year - 4, now()->year + 1);

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
            $food   = (float) ($foodRevenue[$month] ?? 0);

            return [
                'label'         => 'Tháng ' . $month,
                'period'        => $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT),
                'ticketRevenue' => $ticket,
                'foodRevenue'   => $food,
                'totalRevenue'  => $ticket + $food,
            ];
        });

        $summary = [
            'ticketRevenue' => $rows->sum('ticketRevenue'),
            'foodRevenue'   => $rows->sum('foodRevenue'),
            'totalRevenue'  => $rows->sum('totalRevenue'),
        ];

        return view('Admins.DashBoard.revenue-report', [
            'reportTitle'       => 'Thống kê doanh thu theo tháng',
            'reportDescription' => 'Tổng hợp doanh thu vé và đồ ăn theo từng tháng trong năm ' . $year . '.',
            'filterLabel'       => 'Năm',
            'filterValue'       => $year,
            'filterOptions'     => $yearOptions,
            'filterKey'         => 'year',
            'rows'              => $rows,
            'summary'           => $summary,
        ]);
    }

    public function revenueByDay(Request $request)
    {
        $monthInput     = $request->query('month', now()->format('Y-m'));
        $selectedMonth  = Carbon::createFromFormat('Y-m', $monthInput);
        $startOfMonth   = $selectedMonth->copy()->startOfMonth();
        $endOfMonth     = $selectedMonth->copy()->endOfMonth();

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
                $food   = (float) ($foodRevenue[$day] ?? 0);
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

        $summary = [
            'ticketRevenue' => $rows->sum('ticketRevenue'),
            'foodRevenue'   => $rows->sum('foodRevenue'),
            'totalRevenue'  => $rows->sum('totalRevenue'),
        ];

        return view('Admins.DashBoard.revenue-report', [
            'reportTitle'       => 'Thống kê doanh thu theo ngày',
            'reportDescription' => 'Tổng hợp doanh thu vé và đồ ăn theo từng ngày trong tháng ' . $selectedMonth->format('m/Y') . '.',
            'filterLabel'       => 'Tháng',
            'filterValue'       => $selectedMonth->format('Y-m'),
            'filterOptions'     => [],
            'filterKey'         => 'month',
            'rows'              => $rows,
            'summary'           => $summary,
        ]);
    }

    /**
     * AJAX – trả về hóa đơn + top/bottom phim & đồ ăn trong một kỳ
     *
     * period formats accepted:
     *   - '2025'            → cả năm
     *   - '2025-06'         → tháng (từ revenueByMonth)
     *   - '15/06/2025'      → ngày cụ thể (từ revenueByDay)
     */
    public function invoicesByPeriod(Request $request)
    {
        $period = trim((string) $request->query('period'));

        if (preg_match('/^\d{4}$/', $period)) {
            // năm: '2025'
            $start = Carbon::createFromFormat('Y', $period)->startOfYear();
            $end   = $start->copy()->endOfYear();
        } elseif (preg_match('/^\d{4}-\d{2}$/', $period)) {
            // tháng: '2025-06'
            $start = Carbon::createFromFormat('Y-m', $period)->startOfMonth();
            $end   = $start->copy()->endOfMonth();
        } elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $period)) {
            // ngày: '15/06/2025'
            $start = Carbon::createFromFormat('d/m/Y', $period)->startOfDay();
            $end   = $start->copy()->endOfDay();
        } else {
            return response()->json(['error' => 'Invalid period format'], 422);
        }

        // ── Ticket invoices ───────────────────────────────────────────
        $invoiceList = invoice::with(['customer', 'paymentMethod'])
            ->whereBetween('createDate', [$start, $end])
            ->orderByDesc('createDate')
            ->get()
            ->map(fn($inv) => [
                'invoiceID'     => $inv->invoiceID,
                'customer'      => $inv->customer->fullName ?? 'Khách vãng lai',
                'paymentMethod' => $inv->paymentMethod->name ?? '---',
                'createDate'    => Carbon::parse($inv->createDate)->format('d/m/Y'),
                'totalAmount'   => (float) $inv->totalAmount,
                'type'          => 'ticket',
            ]);

        // ── Food invoices ─────────────────────────────────────────────
        $foodList = foodInvoice::with(['customer', 'payment'])
            ->whereBetween('orderDate', [$start, $end])
            ->orderByDesc('orderDate')
            ->get()
            ->map(fn($fi) => [
                'invoiceID'     => 'F-' . $fi->foodInvoiceID,
                'customer'      => $fi->customer->fullName ?? 'Khách vãng lai',
                'paymentMethod' => $fi->payment->name ?? '---',
                'createDate'    => Carbon::parse($fi->orderDate)->format('d/m/Y'),
                'totalAmount'   => (float) $fi->total,
                'type'          => 'food',
            ]);

        $ticketRevenue = $invoiceList->sum('totalAmount');
        $foodRevenue   = $foodList->sum('totalAmount');

        $allInvoices = $invoiceList->concat($foodList)
            ->sortByDesc(fn($i) => Carbon::createFromFormat('d/m/Y', $i['createDate'])->timestamp)
            ->values();

        // ── Top / bottom phim (theo doanh thu vé trong kỳ) ───────────
        $movieStats = ticket::with('showTime.movie')
            ->whereHas('showTime', function ($q) use ($start, $end) {
                $q->whereBetween('showDate', [
                    $start->toDateString(),
                    $end->toDateString(),
                ]);
            })
            ->get()
            ->groupBy(fn($t) => optional(optional($t->showTime)->movie)->movieID)
            ->filter(fn($g, $id) => $id !== null)
            ->map(function ($tickets) {
                $movie = optional($tickets->first()->showTime)->movie;
                return [
                    'movieName' => $movie->movieName ?? 'Không rõ',
                    'revenue'   => (float) $tickets->sum('price'),
                    'count'     => $tickets->count(),
                ];
            })
            ->sortByDesc('revenue')
            ->values();

        $topMovie    = $movieStats->first();
        $bottomMovie = $movieStats->count() > 1 ? $movieStats->last() : null;

        // ── Top / bottom đồ ăn (theo doanh thu trong kỳ) ─────────────
        $foodStats = foodInvoiceDetail::with('food')
            ->whereHas('foodInvoice', function ($q) use ($start, $end) {
                $q->whereBetween('orderDate', [$start, $end]);
            })
            ->get()
            ->groupBy('foodID')
            ->map(function ($details) {
                $food    = $details->first()->food;
                $revenue = $details->sum(fn($d) => $d->quantity * (float) optional($d->food)->price);
                return [
                    'foodName' => optional($food)->foodName ?? 'Không rõ',
                    'revenue'  => $revenue,
                    'quantity' => (int) $details->sum('quantity'),
                ];
            })
            ->sortByDesc('revenue')
            ->values();

        $topFood    = $foodStats->first();
        $bottomFood = $foodStats->count() > 1 ? $foodStats->last() : null;

        return response()->json([
            'ticketRevenue' => $ticketRevenue,
            'foodRevenue'   => $foodRevenue,
            'totalRevenue'  => $ticketRevenue + $foodRevenue,
            'invoices'      => $allInvoices,
            'topMovie'      => $topMovie,
            'bottomMovie'   => $bottomMovie,
            'topFood'       => $topFood,
            'bottomFood'    => $bottomFood,
        ]);
    }
}