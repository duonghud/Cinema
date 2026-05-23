<?php

namespace App\Http\Controllers;

use App\Models\Admin\Invoice;

class BookingHistoryController extends Controller
{
    public function index()
    {
        $customer = session('customer');

        if (!$customer) {
            return redirect()->route('auth.customerLogin')
                ->with('error', 'Vui lòng đăng nhập để xem lịch sử đặt vé.');
        }

        $invoices = Invoice::with([
            'paymentMethod',
            'tickets.showTime.movie',
            'tickets.showTime.room',
            'tickets.seat',
        ])
            ->where('customerID', $customer->customerID)
            ->orderByDesc('createDate')
            ->orderByDesc('invoiceID')
            ->paginate(10);

        return view('system.history', compact('invoices', 'customer'));
    }
}
