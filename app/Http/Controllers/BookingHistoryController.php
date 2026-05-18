<?php

namespace App\Http\Controllers;

use App\Models\Admin\Invoice;

class BookingHistoryController extends Controller
{
    public function index()
    {
        // Lấy khách hàng đang đăng nhập từ session
        $customer = session('customer');

        if (!$customer) {
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập để xem lịch sử đặt vé.');
        }

        /*
        |--------------------------------------------------------------------------
        | Nếu bảng invoices không có cột created_at
        | Hãy sắp xếp theo invoiceID giảm dần (hóa đơn mới nhất trước)
        |--------------------------------------------------------------------------
        */
        $invoices = Invoice::where('customerID', $customer->customerID)
            ->orderBy('invoiceID', 'desc')
            ->paginate(10);

        return view('system.history', compact('invoices', 'customer'));
    }
}