<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin\payment_method;
use App\Models\Admin\showTime;
use App\Models\Admin\Seat;
use Carbon\Carbon;

class SystemPaymentController extends Controller
{
    /**
     * Xác nhận hóa đơn trước khi sang trang thanh toán
     */
    public function confirm(Request $request)
    {
        if (!$request->seats) {
            return back()->with('error', 'Vui lòng chọn ghế!');
        }

        $request->validate([
            'showtime_id' => 'required|exists:show_times,showTimeID',
            'seats'       => 'required|string',
        ]);

        // Danh sách ghế đã chọn
        $seatCodes = array_filter(explode(',', $request->seats));

        // Lấy thông tin suất chiếu
        $showtime = showTime::with(['movie', 'room'])
            ->findOrFail($request->showtime_id);

        // Lấy ghế thuộc đúng phòng
        $seats = Seat::with('seatType')
            ->where('roomID', $showtime->roomID)
            ->get()
            ->filter(function ($seat) use ($seatCodes) {
                return in_array($seat->rowSeat . $seat->colSeat, $seatCodes);
            });

        // Tính tổng tiền
        $total = $seats->sum(function ($seat) {
            return $seat->seatType->price ?? 0;
        });

        // Tạo dữ liệu hóa đơn
        $invoice = [
            'movie'       => $showtime->movie->movieTitle ?? 'N/A',
            'seats'       => $seatCodes,
            'time'        => substr($showtime->startTime, 0, 5),
            'date'        => Carbon::parse($showtime->showDate)->format('d/m/Y'),
            'room'        => $showtime->room->roomName ?? 'N/A',
            'format'      => $showtime->format ?? '2D',
            'total'       => $total,
            'showtime_id' => $showtime->showTimeID,
        ];

        // Lưu hóa đơn vào session
        session(['invoice' => $invoice]);

        return redirect()->route('payment');
    }

    /**
     * Hiển thị trang thanh toán
     */
    public function index()
    {
        $invoice = session('invoice');

        if (!$invoice) {
            return redirect()->route('booking')
                ->with('error', 'Không có dữ liệu hóa đơn!');
        }

        // Lấy tất cả phương thức thanh toán từ admin
        $paymentMethods = payment_method::all();

        return view('system.payment', compact(
            'invoice',
            'paymentMethods'
        ));
    }

    /**
     * Xử lý khi người dùng nhấn nút "Thanh toán"
     */
    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|exists:payment_methods,paymentID',
        ]);

        $invoice = session('invoice');

        if (!$invoice) {
            return redirect()->route('booking')
                ->with('error', 'Hết phiên thanh toán!');
        }

        // Lấy phương thức thanh toán được chọn
        $paymentMethod = payment_method::findOrFail(
            $request->payment_method
        );

        // Lưu lại phương thức đã chọn để giữ trạng thái radio
        session([
            'selected_payment_method' => $paymentMethod->paymentID
        ]);

        /**
         * Nếu chọn VNPAY
         * -> chuyển sang trang giả lập fake-vnpay.blade.php
         */
        if (stripos($paymentMethod->name, 'VNPAY') !== false) {
            $vnpayController = new VnpayController();
            return $vnpayController->createPayment($invoice['total']);
        }

        // Xóa session
        session()->forget([
            'invoice',
            'selected_payment_method'
        ]);

        return redirect()->route('booking')
            ->with('success', 'Thanh toán thành công ');
    }
    public function vnpaySuccess()
    {
        $invoice = session('invoice');

        if (!$invoice) {
            return redirect()->route('booking')
                ->with('error', 'Không tìm thấy hóa đơn!');
        }

        session()->forget([
            'invoice',
            'selected_payment_method'
        ]);

        return redirect()->route('booking')
            ->with('success', 'Thanh toán VNPay thành công');
    }

    /**
     * Callback khi thanh toán VNPAY thất bại
     */
    public function vnpayFail()
    {
        return redirect()->route('payment')
            ->with('error', 'Thanh toán VNPay thất bại!');
    }

    public function success(Request $request)
    {
        $transactionCode = $request->get('transaction_code', 'N/A');

        return view('system.success', compact('transactionCode'));
    }
}
