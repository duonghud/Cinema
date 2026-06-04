<?php

namespace App\Http\Controllers;

use App\Services\BookingService;
use Illuminate\Http\Request;
use RuntimeException;

class MomoController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    public function createPayment($amount)
    {
        $endpoint    = trim(env('MOMO_ENDPOINT'));
        $partnerCode = trim(env('MOMO_PARTNER_CODE'));
        $accessKey   = trim(env('MOMO_ACCESS_KEY'));
        $secretKey   = trim(env('MOMO_SECRET_KEY'));
        $returnUrl   = route('momo.return');
        $notifyUrl   = route('momo.notify');

        $orderId   = $partnerCode . '_' . date('YmdHis') . rand(1000, 9999);
        $requestId = $orderId;
        $orderInfo = 'Thanh toan ve xem phim';
        $extraData = '';
        $amount    = (int) $amount;

        $rawHash = "accessKey={$accessKey}"
            . "&amount={$amount}"
            . "&extraData={$extraData}"
            . "&ipnUrl={$notifyUrl}"
            . "&orderId={$orderId}"
            . "&orderInfo={$orderInfo}"
            . "&partnerCode={$partnerCode}"
            . "&redirectUrl={$returnUrl}"
            . "&requestId={$requestId}"
            . "&requestType=payWithMethod";

        $signature = hash_hmac('sha256', $rawHash, $secretKey);

        $payload = [
            'partnerCode' => $partnerCode,
            'accessKey'   => $accessKey,
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $returnUrl,
            'ipnUrl'      => $notifyUrl,
            'extraData'   => $extraData,
            'requestType' => 'payWithMethod',
            'signature'   => $signature,
            'lang'        => 'vi',
        ];

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 10,
        ]);
        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result, true);

        if (($response['resultCode'] ?? -1) !== 0) {
            return redirect()->route('payment')
                ->with('error', 'Thanh toán MoMo thất bại!');
        }

        // Lưu mã giao dịch MoMo
        session([
            'momo_order_id' => $orderId,
        ]);

        // BẮT BUỘC save session trước khi redirect MoMo
        session()->save();

        return redirect()->away($response['payUrl']);
    }

    public function momoReturn(Request $request)
    {
        $secretKey   = trim(env('MOMO_SECRET_KEY'));
        $accessKey   = trim(env('MOMO_ACCESS_KEY'));

        $partnerCode  = $request->partnerCode;
        $orderId      = $request->orderId;
        $requestId    = $request->requestId;
        $amount       = $request->amount;
        $orderInfo    = $request->orderInfo;
        $orderType    = $request->orderType;
        $transId      = $request->transId;
        $resultCode   = $request->resultCode;
        $message      = $request->message;
        $payType      = $request->payType;
        $responseTime = $request->responseTime;
        $extraData    = $request->extraData;

        $rawHash = "accessKey={$accessKey}"
            . "&amount={$amount}"
            . "&extraData={$extraData}"
            . "&message={$message}"
            . "&orderId={$orderId}"
            . "&orderInfo={$orderInfo}"
            . "&orderType={$orderType}"
            . "&partnerCode={$partnerCode}"
            . "&payType={$payType}"
            . "&requestId={$requestId}"
            . "&responseTime={$responseTime}"
            . "&resultCode={$resultCode}"
            . "&transId={$transId}";

        $signature = hash_hmac('sha256', $rawHash, $secretKey);

        // Check checksum
        if ($signature !== $request->signature) {
            return redirect()->route('payment')
                ->with('error', 'Sai chữ ký MoMo!');
        }

        // Check mã giao dịch
        if (
            session('momo_order_id') &&
            session('momo_order_id') != $orderId
        ) {
            return redirect()->route('payment')
                ->with('error', 'Mã giao dịch không hợp lệ!');
        }

        // Thanh toán thất bại
        if ((int) $resultCode !== 0) {
            return redirect()->route('payment')
                ->with('error', 'Thanh toán MoMo thất bại!');
        }

        // Lấy dữ liệu session
        $invoice   = session('invoice');
        $paymentId = session('selected_payment_method');
        $customer  = session('customer');

        if (!$invoice || !$paymentId || !$customer) {
            return redirect()->route('show')
                ->with('error', 'Không có dữ liệu hóa đơn!');
        }

        try {

            // Tạo hóa đơn
            $savedInvoice = $this->bookingService->finalizeFromSession(
                $invoice,
                (int) $paymentId,
                (int) $customer->customerID
            );

        } catch (RuntimeException $e) {

            return redirect()->route('payment')
                ->with('error', $e->getMessage());
        }

        // Xóa session
        session()->forget([
            'invoice',
            'selected_payment_method',
            'momo_order_id',
        ]);

        // Redirect success
        return redirect()->route('system.success', [
            'invoiceID' => $savedInvoice->invoiceID,
        ]);
    }

    public function momoNotify(Request $request)
    {
        return response()->json(['resultCode' => 0, 'message' => 'OK'], 200);
    }
}