<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Invoice;
use App\Models\Admin\Customer;
use App\Models\Admin\Admin;
use App\Models\Admin\payment_method;
use App\Models\Admin\Ticket;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $invoices = Invoice::with([
            'customer',
            'admin',
            'paymentMethod'
        ])
        ->when($search, function ($query) use ($search) {
            $query->where('invoiceID', 'like', "%{$search}%")
                ->orWhere('totalAmount', 'like', "%{$search}%")
                ->orWhere('createDate', 'like', "%{$search}%")

                ->orWhereHas('customer', function ($q) use ($search) {
                    $q->where('fullName', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })

                ->orWhereHas('admin', function ($q) use ($search) {
                    $q->where('fullName', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })

                ->orWhereHas('paymentMethod', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        })
        ->paginate(5)
        ->withQueryString();

        return view('admins.invoices.index', compact('invoices'));
    }

    public function create()
    {
        return view('admins.invoices.create', [
            'customers' => Customer::all(),
            'admins'    => Admin::all(),
            'payments'  => payment_method::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customerID'  => 'required|exists:customers,customerID',
            'adminID'     => 'required|exists:admins,adminID',
            'paymentID'   => 'required|exists:payment_methods,paymentID',
            'totalAmount' => 'required|numeric|min:0',
        ]);

        Invoice::create([
            'createDate'  => now(),
            'totalAmount' => $request->totalAmount,
            'customerID'  => $request->customerID,
            'adminID'     => $request->adminID,
            'paymentID'   => $request->paymentID,
        ]);

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Tạo hóa đơn thành công');
    }

    public function edit($id)
    {
        return view('admins.invoices.edit', [
            'invoice'   => Invoice::findOrFail($id),
            'customers' => Customer::all(),
            'admins'    => Admin::all(),
            'payments'  => payment_method::all(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $request->validate([
            'customerID'  => 'required|exists:customers,customerID',
            'adminID'     => 'required|exists:admins,adminID',
            'paymentID'   => 'required|exists:payment_methods,paymentID',
            'totalAmount' => 'required|numeric|min:0',
        ]);

        $invoice->update([
            'totalAmount' => $request->totalAmount,
            'customerID'  => $request->customerID,
            'adminID'     => $request->adminID,
            'paymentID'   => $request->paymentID,
        ]);

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Cập nhật thành công');
    }

    public function show($id)
    {
        $invoice = Invoice::with([
            'customer',
            'admin',
            'paymentMethod',
            'tickets.seat',
            'tickets.showTime.movie'
        ])->findOrFail($id);

        return view('admins.invoices.show', compact('invoice'));
    }

    public function destroy($id)
    {
        Invoice::destroy($id);

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Xóa thành công');
    }
}