<?php

namespace App\Http\Controllers;

use App\Models\Admin\payment_method;
use App\Models\Admin\Seat;
use App\Models\Admin\showTime;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use RuntimeException;

class SystemPaymentController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {
    }

    public function confirm(Request $request)
    {
        if (!$request->seats) {
            return back()->with('error', 'Vui lòng chọn ghế!');
        }

        $request->validate([
            'showtime_id' => 'required|exists:show_times,showTimeID',
            'seats' => 'required|string',
        ]);

        $seatCodes = array_values(array_filter(explode(',', $request->seats)));

        $showtime = showTime::with(['movie', 'room'])
            ->findOrFail($request->showtime_id);

        $seats = Seat::with('seatType')
            ->where('roomID', $showtime->roomID)
            ->get()
            ->filter(function ($seat) use ($seatCodes) {
                return in_array($seat->rowSeat . $seat->colSeat, $seatCodes, true);
            })
            ->values();

        if ($seats->count() !== count($seatCodes)) {
            return back()->with('error', 'Một hoặc nhiều ghế không hợp lệ.');
        }

        $total = $seats->sum(function ($seat) {
            return $seat->seatType->price ?? 0;
        });

        session([
            'invoice' => [
                'movie' => $showtime->movie->movieTitle ?? 'N/A',
                'seats' => $seatCodes,
                'seat_ids' => $seats->pluck('seatID')->values()->all(),
                'time' => substr($showtime->startTime, 0, 5),
                'date' => Carbon::parse($showtime->showDate)->format('d/m/Y'),
                'room' => $showtime->room->roomName ?? 'N/A',
                'room_id' => $showtime->roomID,
                'format' => $showtime->format ?? '2D',
                'total' => $total,
                'showtime_id' => $showtime->showTimeID,
            ],
        ]);

        return redirect()->route('payment');
    }

    public function index()
    {
        $invoice = session('invoice');

        if (!$invoice) {
            return redirect()->route('show')
                ->with('error', 'Không có dữ liệu hóa đơn!');
        }

        $paymentMethods = payment_method::all();

        return view('system.payment', compact('invoice', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|exists:payment_methods,paymentID',
        ]);

        $invoice = session('invoice');
        $customer = session('customer');

        if (!$invoice || !$customer) {
            return redirect()->route('show')
                ->with('error', 'Hết phiên thanh toán!');
        }

        $paymentMethod = payment_method::findOrFail($request->payment_method);

        session([
            'selected_payment_method' => $paymentMethod->paymentID,
        ]);

        if (stripos($paymentMethod->name, 'VNPAY') !== false) {
            return app(VnpayController::class)->createPayment($invoice['total']);
        }

        try {
            $savedInvoice = $this->bookingService->finalizeFromSession(
                $invoice,
                (int) $paymentMethod->paymentID,
                (int) $customer->customerID
            );
        } catch (RuntimeException $e) {
            return redirect()->route('payment')
                ->with('error', $e->getMessage());
        }

        session()->forget([
            'invoice',
            'selected_payment_method',
        ]);
        return redirect()->route('system.success', [
            'transaction_code' => 'INV-' . $savedInvoice->invoiceID,
        ])->with('success', 'Thanh toán thành công');
    }

    public function vnpaySuccess()
    {
        $invoice = session('invoice');

        if (!$invoice) {
            return redirect()->route('show')
                ->with('error', 'Không tìm thấy hóa đơn!');
        }
        session()->forget([
            'invoice',
            'selected_payment_method',
        ]);

        return redirect()->route('show')
            ->with('success', 'Thanh toán VNPay thành công');
    }

    public function vnpayFail()
    {
        return redirect()->route('payment')
            ->with('error', 'Thanh toán VNPay thất bại!');
    }

    public function success(Request $request)
    {
        // Lấy mã giao dịch từ URL, ví dụ: /payment/success?transaction_code=TXN123456
        $transactionCode = $request->get('transaction_code', 'N/A');

        return view('system.success', compact('transactionCode'));
    }
}
