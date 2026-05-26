<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\admin;
use App\Models\Admin\customer;
use App\Models\Admin\foodInvoice;
use App\Models\Admin\movie;
use App\Models\Admin\screeningRoom;
use App\Models\Admin\showTime;
use App\Models\Admin\ticket;
use Illuminate\Support\Carbon;
use App\Models\Admin\invoice;
use Illuminate\Http\Request;

class DashBoardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $today = Carbon::today();

        $stats = [
            'totalMovies' => movie::count(),
            'totalCustomers' => customer::count(),
            'totalRooms' => screeningRoom::count(),
            'totalAdmins' => admin::count(),
            'todayShowTimes' => showTime::whereDate('showDate', $today)->count(),
            'soldTickets' => ticket::count(),
            'todayTicketRevenue' => (float) ticket::whereHas('showTime', function ($query) use ($today) {
                $query->whereDate('showDate', $today);
            })->sum('price'),
            'todayFoodRevenue' => (float) foodInvoice::whereDate('orderDate', $today)->sum('total'),
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
        $year = (int) $request->query('year', now()->year);
        $yearOptions = range(now()->year - 4, now()->year + 1);

        $ticketRevenue = invoice::selectRaw('MONTH(createDate) as period, SUM(totalAmount) as total')
            ->whereYear('createDate', $year)
            ->groupByRaw('MONTH(createDate)')
            ->pluck('total', 'period');

        $foodRevenue = foodInvoice::selectRaw('MONTH(orderDate) as period, SUM(total) as total')
            ->whereYear('orderDate', $year)
            ->groupByRaw('MONTH(orderDate)')
            ->pluck('total', 'period');

        $rows = collect(range(1, 12))->map(function ($month) use ($ticketRevenue, $foodRevenue) {
            $ticket = (float) ($ticketRevenue[$month] ?? 0);
            $food = (float) ($foodRevenue[$month] ?? 0);

            return [
                'label' => 'Tháng ' . $month,
                'ticketRevenue' => $ticket,
                'foodRevenue' => $food,
                'totalRevenue' => $ticket + $food,
            ];
        });

        $summary = [
            'ticketRevenue' => $rows->sum('ticketRevenue'),
            'foodRevenue' => $rows->sum('foodRevenue'),
            'totalRevenue' => $rows->sum('totalRevenue'),
        ];

        return view('Admins.DashBoard.revenue-report', [
            'reportTitle' => 'Thống kê doanh thu theo tháng',
            'reportDescription' => 'Tổng hợp doanh thu vé và đồ ăn theo từng tháng trong năm ' . $year . '.',
            'filterLabel' => 'Năm',
            'filterValue' => $year,
            'filterOptions' => $yearOptions,
            'filterKey' => 'year',
            'rows' => $rows,
            'summary' => $summary,
        ]);
    }

    public function revenueByDay(Request $request)
    {
        $monthInput = $request->query('month', now()->format('Y-m'));
        $selectedMonth = Carbon::createFromFormat('Y-m', $monthInput);
        $startOfMonth = $selectedMonth->copy()->startOfMonth();
        $endOfMonth = $selectedMonth->copy()->endOfMonth();

        $ticketRevenue = invoice::selectRaw('DAY(createDate) as period, SUM(totalAmount) as total')
            ->whereBetween('createDate', [$startOfMonth, $endOfMonth])
            ->groupByRaw('DAY(createDate)')
            ->pluck('total', 'period');

        $foodRevenue = foodInvoice::selectRaw('DAY(orderDate) as period, SUM(total) as total')
            ->whereBetween('orderDate', [$startOfMonth, $endOfMonth])
            ->groupByRaw('DAY(orderDate)')
            ->pluck('total', 'period');

        $rows = collect(range(1, $selectedMonth->daysInMonth))->map(function ($day) use ($ticketRevenue, $foodRevenue, $selectedMonth) {
            $ticket = (float) ($ticketRevenue[$day] ?? 0);
            $food = (float) ($foodRevenue[$day] ?? 0);

            return [
                'label' => $selectedMonth->copy()->day($day)->format('d/m/Y'),
                'ticketRevenue' => $ticket,
                'foodRevenue' => $food,
                'totalRevenue' => $ticket + $food,
            ];
        });

        $summary = [
            'ticketRevenue' => $rows->sum('ticketRevenue'),
            'foodRevenue' => $rows->sum('foodRevenue'),
            'totalRevenue' => $rows->sum('totalRevenue'),
        ];

        return view('Admins.DashBoard.revenue-report', [
            'reportTitle' => 'Thống kê doanh thu theo ngày',
            'reportDescription' => 'Tổng hợp doanh thu vé và đồ ăn theo từng ngày trong tháng ' . $selectedMonth->format('m/Y') . '.',
            'filterLabel' => 'Tháng',
            'filterValue' => $selectedMonth->format('Y-m'),
            'filterOptions' => [],
            'filterKey' => 'month',
            'rows' => $rows,
            'summary' => $summary,
        ]);
    }

    public function invoicesByPeriod(Request $request)
    {
        $period = $request->query('period');

        if (preg_match('/^\d{4}$/', $period)) {
            // Year view: '2025'
            $start = Carbon::createFromFormat('Y', $period)->startOfYear();
            $end   = $start->copy()->endOfYear();
            $labelType = 'year';
        } elseif (preg_match('/^\d{4}-\d{2}$/', $period)) {
            $start = Carbon::createFromFormat('Y-m', $period)->startOfMonth();
            $end   = $start->copy()->endOfMonth();
            $labelType = 'month';
        } elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $period)) {
            $start = Carbon::createFromFormat('d/m/Y', $period)->startOfDay();
            $end   = $start->copy()->endOfDay();
            $labelType = 'day';
        } else {
            return response()->json(['error' => 'Invalid period'], 422);
        }

        // ── Invoice (vé) ──────────────────────────────────────────────
        $invoiceList = invoice::with('customer')
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

        // ── Food invoice (đồ ăn) ──────────────────────────────────────
        $foodList = foodInvoice::with('customer')
            ->whereBetween('orderDate', [$start, $end])
            ->orderByDesc('orderDate')
            ->get()
            ->map(fn($fi) => [
                'invoiceID'     => 'F-' . $fi->id,   // tuỳ field PK của foodInvoice
                'customer'      => $fi->customer->fullName ?? 'Khách vãng lai',
                'paymentMethod' => '---',
                'createDate'    => Carbon::parse($fi->orderDate)->format('d/m/Y'),
                'totalAmount'   => (float) $fi->total,
                'type'          => 'food',
            ]);

        $ticketRevenue = $invoiceList->sum('totalAmount');
        $foodRevenue   = $foodList->sum('totalAmount');

        // Gộp và sắp xếp theo ngày mới nhất
        $allInvoices = $invoiceList->concat($foodList)
            ->sortByDesc(fn($i) => Carbon::createFromFormat('d/m/Y', $i['createDate']))
            ->values();

        return response()->json([
            'ticketRevenue' => $ticketRevenue,
            'foodRevenue'   => $foodRevenue,
            'totalRevenue'  => $ticketRevenue + $foodRevenue,
            'invoices'      => $allInvoices,
        ]);
    }
}
