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
        return view('admins.manageFoods.foodInvoice.index', compact('invoices'));
    }


    public function create()
    {
        $customers = Customer::all();
        $payments = payment_method::all();
        $foods = Food::all();

        return view('admins.manageFoods.foodInvoice.create', compact('customers', 'payments', 'foods'));
    }

    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'customerID' => 'required|exists:customers,customerID',
            'paymentID' => 'required|exists:payments,paymentID',
            'orderTime' => 'required|date',
            'foods' => 'required|array',
            'foods.*' => 'integer|min:0',
        ], [
            'customerID.required' => 'Vui lòng chọn khách hàng.',
            'customerID.exists' => 'Khách hàng không hợp lệ.',
            'paymentID.required' => 'Vui lòng chọn phương thức thanh toán.',
            'paymentID.exists' => 'Phương thức thanh toán không hợp lệ.',
            'orderTime.required' => 'Vui lòng chọn thời gian đặt.',
            'orderTime.date' => 'Thời gian đặt không hợp lệ.',
            'foods.required' => 'Vui lòng chọn ít nhất một món ăn.',
            'foods.*.integer' => 'Số lượng món ăn phải là số nguyên.',
            'foods.*.min' => 'Số lượng món ăn phải lớn hơn hoặc bằng 0.',
        ]);

        // Kiểm tra ít nhất 1 món được chọn
        $hasFood = false;
        foreach ($validated['foods'] as $quantity) {
            if ($quantity > 0) {
                $hasFood = true;
                break;
            }
        }
        if (!$hasFood) {
            return redirect()->back()->withInput()->withErrors(['foods' => 'Vui lòng chọn ít nhất một món ăn.']);
        }

        // Lưu hóa đơn
        $invoice = FoodInvoice::create([
            'customerID' => $validated['customerID'],
            'paymentID' => $validated['paymentID'],
            'orderTime' => $validated['orderTime'],
        ]);

        // Lưu chi tiết món ăn
        foreach ($validated['foods'] as $foodID => $quantity) {
            if ($quantity > 0) {
                $invoice->foods()->attach($foodID, ['quantity' => $quantity]);
            }
        }

        return redirect()->route('foodInvoice.index')->with('success', 'Tạo hóa đơn thành công!');
    }


    public function show($id)
    {
        $invoice = FoodInvoice::with(['customer', 'payment', 'details.food'])->findOrFail($id);
        return view('admins.manageFoods.foodInvoiceDetail.index', compact('invoice'));
    }

    public function edit($id)
    {
        $invoice = FoodInvoice::with('details')->findOrFail($id);
        $foods = Food::all();
        $customers = Customer::all();
        $payments = payment_method::all();

        return view('admins.manageFoods.foodInvoice.edit', compact('invoice', 'foods', 'customers', 'payments'));
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
