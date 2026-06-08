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
    /**
     * Màn hình chính Dashboard: Thống kê nhanh số lượng thực thể, doanh thu và danh sách chiếu phim trong ngày.
     */
    public function index()
    {
        $today = Carbon::today();

        // Khối nạp dữ liệu đếm tổng số lượng và doanh thu tích lũy trong ngày hôm nay
        $stats = [
            'totalMovies'        => movie::count(),
            'totalCustomers'     => customer::count(),
            'totalRooms'         => screeningRoom::count(),
            'totalAdmins'        => admin::count(),
            'todayShowTimes'     => showTime::whereDate('showDate', $today)->count(),
            'soldTickets'        => ticket::count(),
            // Tính tổng tiền vé dựa trên các suất chiếu có ngày chiếu là hôm nay
            'todayTicketRevenue' => (float) ticket::whereHas('showTime', function ($q) use ($today) {
                $q->whereDate('showDate', $today);
            })->sum('price'),
            // Tính tổng tiền hóa đơn dịch vụ đồ ăn phát sinh trong ngày hôm nay
            'todayFoodRevenue'   => (float) foodInvoice::whereDate('orderDate', $today)->sum('total'),
        ];

        // Lấy danh sách 6 suất chiếu sắp diễn ra từ thời điểm hiện tại trở đi
        $upcomingShowTimes = showTime::with(['movie', 'room'])
            ->whereDate('showDate', '>=', $today)
            ->orderBy('showDate')->orderBy('startTime')
            ->limit(6)->get();

        // Lấy danh sách 5 bộ phim mới cập nhật gần đây nhất dựa theo ngày phát hành
        $latestMovies = movie::with(['ageRating', 'studio'])
            ->orderByDesc('releaseDate')->limit(5)->get();

        return view('Admins.DashBoard.index', compact('stats', 'upcomingShowTimes', 'latestMovies'));
    }

    /**
     * Báo cáo tổng hợp: Doanh thu 12 tháng theo năm tùy chọn (Mặc định lấy năm hiện tại).
     */
    public function revenueByMonth(Request $request)
    {
        // Thu thập tham số năm lọc, giới hạn phạm vi dữ liệu an toàn để tránh lỗi tràn năm
        $year        = (int) $request->query('year', now()->year);
        $yearOptions = range(now()->year - 4, now()->year + 1);

        if ($year < 2000 || $year > now()->year + 5) {
            $year = now()->year;
        }

        // Nhóm và tính tổng doanh thu vé xem phim theo từng tháng (Pluck dạng: [Tháng => Số tiền])
        $ticketRevenue = invoice::selectRaw('MONTH(createDate) as period, SUM(totalAmount) as total')
            ->whereYear('createDate', $year)
            ->groupByRaw('MONTH(createDate)')
            ->pluck('total', 'period');

        // Nhóm và tính tổng doanh thu đồ ăn theo từng tháng
        $foodRevenue = foodInvoice::selectRaw('MONTH(orderDate) as period, SUM(total) as total')
            ->whereYear('orderDate', $year)
            ->groupByRaw('MONTH(orderDate)')
            ->pluck('total', 'period');

        // Khởi tạo mảng cố định từ tháng 1 đến tháng 12 để map khớp nối dữ liệu, tránh thiếu tháng nếu không có doanh thu
        $rows = collect(range(1, 12))->map(function ($month) use ($ticketRevenue, $foodRevenue, $year) {
            $ticket = (float) ($ticketRevenue[$month] ?? 0);
            $food   = (float) ($foodRevenue[$month]   ?? 0);
            return [
                'label'         => 'Tháng ' . $month,
                'period'        => $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT), // Định dạng chuỗi chuẩn: YYYY-MM
                'ticketRevenue' => $ticket,
                'foodRevenue'   => $food,
                'totalRevenue'  => $ticket + $food,
            ];
        });

        // Điểm tin Phim/Đồ ăn bán chạy nhất và chậm nhất trong năm mục tiêu
        [$topMovie, $bottomMovie, $movieList] = $this->getMovieHighlights(
            Carbon::createFromFormat('Y', $year)->startOfYear(),
            Carbon::createFromFormat('Y', $year)->endOfYear()
        );

        [$topFood, $bottomFood, $foodList] = $this->getFoodHighlights(
            Carbon::createFromFormat('Y', $year)->startOfYear(),
            Carbon::createFromFormat('Y', $year)->endOfYear()
        );

        // Tính toán tổng dòng tiền tích lũy của cả năm
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

    /**
     * Báo cáo tổng hợp: Chi tiết doanh thu theo từng ngày của một tháng (Định dạng nhận vào: YYYY-MM).
     */
    public function revenueByDay(Request $request)
    {
        $monthInput = $request->query('month', now()->format('Y-m'));

        // Kiểm tra Regex định dạng chuỗi tháng nhận từ client
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

        // Nhóm và tính doanh thu vé xem phim theo ngày trong tháng mục tiêu
        $ticketRevenue = invoice::selectRaw('DAY(createDate) as period, SUM(totalAmount) as total')
            ->whereBetween('createDate', [$startOfMonth, $endOfMonth])
            ->groupByRaw('DAY(createDate)')
            ->pluck('total', 'period');

        // Nhóm và tính doanh thu đồ ăn theo ngày trong tháng mục tiêu
        $foodRevenue = foodInvoice::selectRaw('DAY(orderDate) as period, SUM(total) as total')
            ->whereBetween('orderDate', [$startOfMonth, $endOfMonth])
            ->groupByRaw('DAY(orderDate)')
            ->pluck('total', 'period');

        // Tự động tính số ngày tối đa của tháng (ví dụ: 28, 29, 30, 31) để sinh mảng lặp dữ liệu không sót ngày nào
        $rows = collect(range(1, $selectedMonth->daysInMonth))->map(
            function ($day) use ($ticketRevenue, $foodRevenue, $selectedMonth) {
                $ticket = (float) ($ticketRevenue[$day] ?? 0);
                $food   = (float) ($foodRevenue[$day]   ?? 0);
                $date   = $selectedMonth->copy()->day($day);
                return [
                    'label'         => $date->format('d/m/Y'),
                    'period'        => $date->format('Y-m-d'), // Dùng cấu trúc ISO giúp hàm AJAX invoicesByPeriod bóc tách chuẩn xác hơn
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
            'filterOptions'     => [], // Để trống vì giao diện sử dụng thẻ chọn <input type="month"> thay vì select dropdown
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

    /**
     * API Endpoint (AJAX): Truy xuất danh sách hóa đơn chi tiết phát sinh trong một kỳ (Ngày/Tháng/Năm) và lọc theo phân loại.
     */
    public function invoicesByPeriod(Request $request)
    {
        $period = trim((string) $request->query('period'));
        $type   = $request->query('type', 'all'); // Phân loại lọc: all (tất cả) | ticket (chỉ vé) | food (chỉ đồ ăn)

        if (! in_array($type, ['all', 'ticket', 'food'])) {
            $type = 'all';
        }

        // Biên dịch chuỗi thời gian gửi lên thành mốc mảng [Bắt đầu, Kết thúc] dạng Carbon
        [$start, $end] = $this->parsePeriod($period);
        if (! $start) {
            return response()->json(['error' => 'Định dạng kỳ không hợp lệ. Vui lòng thử lại.'], 422);
        }

        try {
            // ── Lấy danh sách hóa đơn Vé phim nếu thuộc bộ lọc ─────────────────
            $invoiceList = collect();
            if (in_array($type, ['all', 'ticket'])) {
                $invoiceList = invoice::with(['customer', 'paymentMethod'])
                    ->whereBetween('createDate', [$start, $end])
                    ->orderByDesc('createDate')
                    ->get()
                    ->map(fn ($inv) => [
                        'invoiceID'     => $inv->invoiceID,
                        'customer'      => $inv->customer->fullName ?? 'Khách vãng lai',
                        'paymentMethod' => $inv->paymentMethod->name ?? '---',
                        'createDate'    => Carbon::parse($inv->createDate)->format('d/m/Y'),
                        'totalAmount'   => (float) $inv->totalAmount,
                        'type'          => 'ticket',
                    ]);
            }

            // ── Lấy danh sách hóa đơn Đồ ăn nếu thuộc bộ lọc ───────────────────
            $foodInvoiceList = collect();
            if (in_array($type, ['all', 'food'])) {
                $foodInvoiceList = foodInvoice::with(['customer', 'paymentMethod'])
                    ->whereBetween('orderDate', [$start, $end])
                    ->orderByDesc('orderDate')
                    ->get()
                    ->map(fn ($fi) => [
                        'invoiceID'     => $fi->foodInvoiceID,
                        'customer'      => $fi->customer->fullName ?? 'Khách vãng lai',
                        'paymentMethod' => $fi->paymentMethod->name ?? '---',
                        'createDate'    => Carbon::parse($fi->orderDate)->format('d/m/Y'),
                        'totalAmount'   => (float) $fi->total,
                        'type'          => 'food',
                    ]);
            }

            $ticketRevenue = $invoiceList->sum('totalAmount');
            $foodRevenue   = $foodInvoiceList->sum('totalAmount');

            // Gộp 2 luồng danh sách hóa đơn lại làm một và sắp xếp theo mốc thời gian tạo giảm dần
            $allInvoices = $invoiceList->concat($foodInvoiceList)
                ->sortByDesc(fn ($i) => Carbon::createFromFormat('d/m/Y', $i['createDate'])->timestamp)
                ->values();

            $topMovie = $bottomMovie = $topFood = $bottomFood = null;
            $movieList = $foodList = [];

            // Tải thông tin thống kê tiêu biểu (Top/Bottom) dựa theo phân loại yêu cầu
            if (in_array($type, ['all', 'ticket'])) {
                [$topMovie, $bottomMovie, $movieList] = $this->getMovieHighlights($start, $end);
            }
            if (in_array($type, ['all', 'food'])) {
                [$topFood, $bottomFood, $foodList] = $this->getFoodHighlights($start, $end);
            }

            // Trả về JSON phản hồi cho Client vẽ biểu đồ/bảng dữ liệu
            return response()->json([
                'ticketRevenue' => $ticketRevenue,
                'foodRevenue'   => $foodRevenue,
                'totalRevenue'  => $ticketRevenue + $foodRevenue,
                'invoices'      => $allInvoices,
                'type'          => $type,
                'period'        => $period,
                'topMovie'      => $topMovie,
                'bottomMovie'   => $bottomMovie,
                'movieList'     => $movieList,
                'topFood'       => $topFood,
                'bottomFood'    => $bottomFood,
                'foodList'      => $foodList,
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
    // Bộ hàm bổ trợ (Helpers) nội bộ
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Phân tích chuỗi đa định dạng thời gian và quy đổi sang mốc [Bắt đầu ngày/năm, Kết thúc ngày/năm] cụ thể.
     */
    private function parsePeriod(string $period): array
    {
        try {
            // Định dạng Năm: "2026"
            if (preg_match('/^\d{4}$/', $period)) {
                $start = Carbon::createFromFormat('Y', $period)->startOfYear();
                return [$start, $start->copy()->endOfYear()];
            }
            // Định dạng Tháng: "2026-05"
            if (preg_match('/^\d{4}-\d{2}$/', $period)) {
                $start = Carbon::createFromFormat('Y-m', $period)->startOfMonth();
                return [$start, $start->copy()->endOfMonth()];
            }
            // Định dạng Ngày tiêu chuẩn hệ thống (ISO): "2026-05-25"
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $period)) {
                $start = Carbon::createFromFormat('Y-m-d', $period)->startOfDay();
                return [$start, $start->copy()->endOfDay()];
            }
            // Định dạng Ngày Việt Nam: "25/05/2026" (Giữ lại để đảm bảo tương thích ngược)
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
     * Thu thập, tính toán xếp hạng doanh thu phim (Top đầu/Bottom cuối) trong phạm vi mốc thời gian lựa chọn.
     * Giải pháp sửa đổi logic: Khớp nối dựa theo ngày xuất hóa đơn vé (invoice.createDate) thay vì dựa vào ngày chiếu (showDate).
     */
    private function getMovieHighlights(Carbon $start, Carbon $end): array
    {
        $stats = ticket::with('showTime.movie')
            // Ràng buộc điều kiện: Hóa đơn chứa vé phim phải được lập trong khoảng thời gian biểu quyết này
            ->whereHas('invoice', fn ($q) => $q->whereBetween('createDate', [$start, $end]))
            ->get()
            // Gom nhóm Collection theo mã ID bộ phim
            ->groupBy(fn ($t) => optional(optional($t->showTime)->movie)->movieID)
            ->filter(fn ($g, $id) => $id !== null) // Loại bỏ các nhóm có phim không tồn tại trong hệ thống
            ->map(function ($tickets) {
                $movie = optional($tickets->first()->showTime)->movie;
                return [
                    'movieName' => $movie->movieTitle ?? 'Không rõ',
                    'revenue'   => (float) $tickets->sum('price'), // Tổng doanh thu thu về từ bộ phim trong kỳ
                    'count'     => $tickets->count(),             // Tổng lượng vé bán ra của phim
                ];
            })
            ->filter(fn ($item) => $item['revenue'] > 0) // Chỉ xét những phim có phát sinh doanh thu thực tế
            ->sortByDesc('revenue')
            ->values();

        $top    = $stats->first();
        $bottom = $stats->count() >= 2 ? $stats->last() : null; // Chỉ lấy phần tử bét bảng nếu danh sách có từ 2 phim trở lên
        $list   = $stats->map(fn ($item, $idx) => array_merge($item, ['rank' => $idx + 1]))->values()->toArray();

        return [$top, $bottom, $list];
    }

    /**
     * Thu thập, tính toán xếp hạng doanh thu các mặt hàng đồ ăn (Top đầu/Bottom cuối) trong phạm vi mốc thời gian lựa chọn.
     */
    private function getFoodHighlights(Carbon $start, Carbon $end): array
    {
        $stats = foodInvoiceDetail::with('food')
            // Lọc các chi tiết hóa đơn dựa vào thời gian đặt hàng (foodInvoice.orderDate)
            ->whereHas('foodInvoice', fn ($q) => $q->whereBetween('orderDate', [$start, $end]))
            ->get()
            ->groupBy('foodID')
            ->map(function ($details) {
                $food = $details->first()->food;
                return [
                    'foodName' => optional($food)->foodName ?? 'Không rõ',
                    // Tính toán doanh thu động: Số lượng bán * Đơn giá sản phẩm
                    'revenue'  => $details->sum(fn ($d) => $d->quantity * (float) optional($d->food)->price),
                    'quantity' => (int) $details->sum('quantity'), // Tổng số suất đồ ăn tiêu thụ
                ];
            })
            ->filter(fn ($item) => $item['revenue'] > 0)
            ->sortByDesc('revenue')
            ->values();

        $top    = $stats->first();
        $bottom = $stats->count() >= 2 ? $stats->last() : null;
        $list   = $stats->map(fn ($item, $idx) => array_merge($item, ['rank' => $idx + 1]))->values()->toArray();

        return [$top, $bottom, $list];
    }
}