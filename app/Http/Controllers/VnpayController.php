<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin\Seat;
use App\Models\Admin\ticket;

class VnpayController extends Controller
{
    public function createPayment($amount)
    {
        // URL thanh toán của VNPAY
        $vnp_Url = trim(env('VNP_URL'));
        $vnp_ReturnUrl = route('vnpay.return');

        // Thông tin cấu hình
        $vnp_TmnCode = trim(env('VNP_TMN_CODE'));
        $vnp_HashSecret = trim(env('VNP_HASH_SECRET'));

        // Mã giao dịch duy nhất
        $vnp_TxnRef = date('YmdHis') . rand(1000, 9999);

        // Thiết lập múi giờ
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        // Thông tin đơn hàng
        $vnp_OrderInfo = 'Thanh toan ve xem phim';
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = (int) $amount * 100; // VNPAY yêu cầu nhân 100
        $vnp_Locale = 'vn';
        $vnp_BankCode = '';

        // Địa chỉ IP người dùng
        $vnp_IpAddr = request()->ip();

        // Thời gian tạo và hết hạn
        $createDate = date('YmdHis');
        $expireDate = date('YmdHis', strtotime('+15 minutes'));

        // Dữ liệu gửi sang VNPAY
        $inputData = [
            'vnp_Version'    => '2.1.0',
            'vnp_TmnCode'    => $vnp_TmnCode,
            'vnp_Amount'     => $vnp_Amount,
            'vnp_Command'    => 'pay',
            'vnp_CreateDate' => $createDate,
            'vnp_CurrCode'   => 'VND',
            'vnp_IpAddr'     => $vnp_IpAddr,
            'vnp_Locale'     => $vnp_Locale,
            'vnp_OrderInfo'  => $vnp_OrderInfo,
            'vnp_OrderType'  => $vnp_OrderType,
            'vnp_ReturnUrl'  => $vnp_ReturnUrl,
            'vnp_TxnRef'     => $vnp_TxnRef,
            'vnp_ExpireDate' => $expireDate,
        ];

        if (!empty($vnp_BankCode)) {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);

        $query = '';
        $hashData = '';
        $i = 0;

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashData .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }

            $query .= urlencode($key) . '=' . urlencode($value) . '&';
        }

        $vnp_SecureHash = hash_hmac(
            'sha512',
            $hashData,
            $vnp_HashSecret
        );

        $paymentUrl = $vnp_Url
            . '?'
            . $query
            . 'vnp_SecureHash='
            . $vnp_SecureHash;
        session([
            'vnp_TxnRef' => $vnp_TxnRef,
        ]);

        return redirect()->away($paymentUrl);
    }


    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = trim(env('VNP_HASH_SECRET'));

        $inputData = $request->except([
            'vnp_SecureHash',
            'vnp_SecureHashType'
        ]);

        ksort($inputData);

        $hashData = '';
        $i = 0;

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashData .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac(
            'sha512',
            $hashData,
            $vnp_HashSecret
        );

        if ($secureHash !== $request->vnp_SecureHash) {
            return redirect()->route('payment')
                ->with('error', 'Sai chữ ký VNPAY!');
        }

        if (
            session('vnp_TxnRef') &&
            session('vnp_TxnRef') != $request->vnp_TxnRef
        ) {
            return redirect()->route('payment')
                ->with('error', 'Mã giao dịch không hợp lệ!');
        }

        if ($request->vnp_ResponseCode === '00') {

            // Lấy invoice từ session
            $invoice = session('invoice');

            // Nếu có dữ liệu invoice thì cập nhật trạng thái vé đã đặt
            if ($invoice && !empty($invoice['seats']) && !empty($invoice['showTimeID'])) {

                // Lấy danh sách seatID theo tên ghế (A1, A2...)
                $seatIds = Seat::query()
                    ->where('screeningRoomID', $invoice['roomID'] ?? null) // nếu có roomID
                    ->get()
                    ->filter(function ($seat) use ($invoice) {
                        $seatCode = $seat->rowSeat . $seat->colSeat;
                        return in_array($seatCode, $invoice['seats']);
                    })
                    ->pluck('seatID');

                // Cập nhật trạng thái ticket thành booked
                ticket::where('showTimeID', $invoice['showTimeID'])
                    ->whereIn('seatID', $seatIds)
                    ->update([
                        'status' => 'booked'
                    ]);
            }

            // Lấy mã giao dịch
            $transactionCode = $request->vnp_TransactionNo ?? $request->vnp_TxnRef;

            // Xóa session sau khi đã cập nhật ticket
            session()->forget([
                'invoice',
                'selected_payment_method',
                'vnp_TxnRef'
            ]);

            // Chuyển đến trang thành công
            return redirect()->route('system.success', [
                'transaction_code' => $transactionCode
            ]);
        }
    }
}
