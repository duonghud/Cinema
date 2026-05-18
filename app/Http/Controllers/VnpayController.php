<?php

namespace App\Http\Controllers;

use App\Services\BookingService;
use Illuminate\Http\Request;
use RuntimeException;

class VnpayController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {
    }

    public function createPayment($amount)
    {
        $vnp_Url = trim(env('VNP_URL'));
        $vnp_ReturnUrl = route('vnpay.return');

        $vnp_TmnCode = trim(env('VNP_TMN_CODE'));
        $vnp_HashSecret = trim(env('VNP_HASH_SECRET'));
        $vnp_TxnRef = date('YmdHis') . rand(1000, 9999);

        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $vnp_TmnCode,
            'vnp_Amount' => (int) $amount * 100,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => request()->ip(),
            'vnp_Locale' => 'vn',
            'vnp_OrderInfo' => 'Thanh toan ve xem phim',
            'vnp_OrderType' => 'billpayment',
            'vnp_ReturnUrl' => $vnp_ReturnUrl,
            'vnp_TxnRef' => $vnp_TxnRef,
            'vnp_ExpireDate' => date('YmdHis', strtotime('+15 minutes')),
        ];

        ksort($inputData);

        $query = '';
        $hashData = '';
        $i = 0;

        foreach ($inputData as $key => $value) {
            if ($i === 1) {
                $hashData .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashData .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }

            $query .= urlencode($key) . '=' . urlencode($value) . '&';
        }

        $paymentUrl = $vnp_Url
            . '?'
            . $query
            . 'vnp_SecureHash='
            . hash_hmac('sha512', $hashData, $vnp_HashSecret);

        session(['vnp_TxnRef' => $vnp_TxnRef]);

        return redirect()->away($paymentUrl);
    }

    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = trim(env('VNP_HASH_SECRET'));
        $inputData = $request->except([
            'vnp_SecureHash',
            'vnp_SecureHashType',
        ]);

        ksort($inputData);

        $hashData = '';
        $i = 0;

        foreach ($inputData as $key => $value) {
            if ($i === 1) {
                $hashData .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashData .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash !== $request->vnp_SecureHash) {
            return redirect()->route('payment')
                ->with('error', 'Sai chữ ký VNPAY!');
        }

        if (session('vnp_TxnRef') && session('vnp_TxnRef') != $request->vnp_TxnRef) {
            return redirect()->route('payment')
                ->with('error', 'Mã giao dịch không hợp lệ!');
        }

        if ($request->vnp_ResponseCode !== '00') {
            return redirect()->route('payment')
                ->with('error', 'Thanh toán VNPay thất bại!');
        }

        $invoice = session('invoice');
        $paymentId = session('selected_payment_method');
        $customer = session('customer');

        if (!$invoice || !$paymentId || !$customer) {
            return redirect()->route('payment')
                ->with('error', 'Không tìm thấy dữ liệu thanh toán.');
        }

        try {
            $this->bookingService->finalizeFromSession(
                $invoice,
                (int) $paymentId,
                (int) $customer->customerID
            );
        } catch (RuntimeException $e) {
            return redirect()->route('payment')
                ->with('error', $e->getMessage());
        }

        $transactionCode = $request->vnp_TransactionNo ?? $request->vnp_TxnRef;

        session()->forget([
            'invoice',
            'selected_payment_method',
            'vnp_TxnRef',
        ]);

        return redirect()->route('system.success', [
            'transaction_code' => $transactionCode,
        ]);
    }
}
