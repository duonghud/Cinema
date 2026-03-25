<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\FoodInvoice;
use App\Models\Admin\FoodInvoiceDetail;
use App\Models\Admin\Food;
use App\Models\Admin\Customer;
use App\Models\Admin\payment_method;

class foodInvoiceController extends Controller
{
    
    public function index()
    {
        $invoices = FoodInvoice::with(['customer', 'payment', 'details.food'])->get();
        return view('admins.foodInvoice.index', compact('invoices'));
    }

    
    public function create()
    {
        $foods = Food::all();
        $customers = Customer::all();
        $payments = payment_method::all();

        return view('admins.foodInvoice.create', compact('foods', 'customers', 'payments'));
    }

    
    public function store(Request $request)
    {
        $invoice = FoodInvoice::create([
            'orderDate' => now(),
            'total' => 0,
            'customerID' => $request->customerID,
            'adminID' => 2,
            'paymentID' => $request->paymentID
        ]);

        $total = 0;

        foreach ($request->foods as $foodID => $qty) {
            if ($qty > 0) {
                $food = Food::find($foodID);

                FoodInvoiceDetail::create([
                    'foodInvoiceID' => $invoice->foodInvoiceID,
                    'foodID' => $foodID,
                    'quantity' => $qty
                ]);

                $total += $food->price * $qty;
            }
        }

        $invoice->update(['total' => $total]);

        return redirect()->route('foodInvoice.index')->with('success', 'Tạo hóa đơn thành công');
    }

    
    public function show($id)
    {
        $invoice = FoodInvoice::with(['customer', 'payment', 'details.food'])->findOrFail($id);
        return view('admins.foodInvoiceDetail.index', compact('invoice'));
    }

    public function edit($id)
    {
        $invoice = FoodInvoice::with('details')->findOrFail($id);
        $foods = Food::all();
        $customers = Customer::all();
        $payments = payment_method::all();

        return view('admins.foodInvoice.edit', compact('invoice', 'foods', 'customers', 'payments'));
    }

    public function update(Request $request, $id)
    {
        $invoice = FoodInvoice::findOrFail($id);

        $invoice->update([
            'customerID' => $request->customerID,
            'paymentID' => $request->paymentID
        ]);

        FoodInvoiceDetail::where('foodInvoiceID', $id)->delete();

        $total = 0;

        foreach ($request->foods as $foodID => $qty) {
            if ($qty > 0) {
                $food = Food::find($foodID);

                FoodInvoiceDetail::create([
                    'foodInvoiceID' => $id,
                    'foodID' => $foodID,
                    'quantity' => $qty
                ]);

                $total += $food->price * $qty;
            }
        }

        $invoice->update(['total' => $total]);

        return redirect()->route('foodInvoice.index')->with('success', 'Cập nhật thành công');
    }

    public function destroy($id)
    {
        FoodInvoice::findOrFail($id)->delete();
        return redirect()->route('foodInvoice.index')->with('success', 'Xóa thành công');
    }
}
