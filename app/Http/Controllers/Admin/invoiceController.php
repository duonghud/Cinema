<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Invoice;
use App\Models\Admin\Customer;
use App\Models\Admin\Admin;
use App\Models\Admin\payment_method;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['customer', 'admin', 'payment'])->paginate(10);
        return view('admins.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers = Customer::all();
        $admins = Admin::all();
        $payments = payment_method::all();

        return view('admins.invoices.create', compact('customers', 'admins', 'payments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customerID' => 'required',
            'adminID' => 'required',
            'paymentID' => 'required',
            'totalAmount' => 'required|numeric'
        ]);

        Invoice::create([
            'createDate' => now(),
            'totalAmount' => $request->totalAmount,
            'customerID' => $request->customerID,
            'adminID' => $request->adminID,
            'paymentID' => $request->paymentID,
        ]);

        return redirect()->route('invoices.index')->with('success', 'Tạo hóa đơn thành công');
    }

    public function edit($id)
    {
        $invoice = Invoice::findOrFail($id);
        $customers = Customer::all();
        $admins = Admin::all();
        $payments = payment_method::all();

        return view('admins.invoices.edit', compact('invoice', 'customers', 'admins', 'payments'));
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $invoice->update($request->all());

        return redirect()->route('invoices.index')->with('success', 'Cập nhật thành công');
    }

    public function destroy($id)
    {
        Invoice::destroy($id);
        return redirect()->route('invoices.index')->with('success', 'Xóa thành công');
    }
}