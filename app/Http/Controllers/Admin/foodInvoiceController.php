<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\FoodInvoice;
use App\Models\Admin\FoodInvoiceDetail;
use App\Models\Admin\Food;
use App\Models\Admin\Customer;
use App\Models\Admin\payment_method;
use Illuminate\Support\Facades\Auth;

class FoodInvoiceController extends Controller
{

    public function index()
    {
        $invoices = FoodInvoice::with([
            'customer',
            'payment',
            'details.food'
        ])->get();

        return view(
            'admins.manageFoods.foodInvoice.index',
            compact('invoices')
        );
    }

    public function create()
    {
        $customers = Customer::all();
        $payments = payment_method::all();
        $foods = Food::all();

        return view(
            'admins.manageFoods.foodInvoice.create',
            compact('customers', 'payments', 'foods')
        );
    }

    public function store(Request $request)
    {
        // validation
        $validated = $request->validate([
            'customerID' => 'required|exists:customers,customerID',
            'paymentID' => 'required|exists:payment_methods,paymentID',
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
            'foods.required' => 'Vui lòng chọn món ăn.',
        ]);


        $hasFood = false;

        foreach ($validated['foods'] as $quantity) {
            if ($quantity > 0) {
                $hasFood = true;
                break;
            }
        }

        if (!$hasFood) {
            return back()
                ->withInput()
                ->withErrors([
                    'foods' => 'Vui lòng chọn ít nhất một món ăn.'
                ]);
        }

        $total = 0;

        foreach ($validated['foods'] as $foodID => $quantity) {

            if ($quantity > 0) {

                $food = Food::find($foodID);

                if ($food) {
                    $total += $food->price * $quantity;
                }
            }
        }

        $invoice = FoodInvoice::create([
            'customerID' => $validated['customerID'],
            'paymentID' => $validated['paymentID'],
            'adminID' => 1,
            'orderDate' => $validated['orderTime'],
            'total' => $total
        ]);

        foreach ($validated['foods'] as $foodID => $quantity) {

            if ($quantity > 0) {

                FoodInvoiceDetail::create([
                    'foodInvoiceID' => $invoice->invoiceID,
                    'foodID' => $foodID,
                    'quantity' => $quantity
                ]);
            }
        }


        return redirect()
            ->route('foodInvoice.index')
            ->with('success', 'Tạo hóa đơn thành công!');
    }

    public function show($id)
    {
        $invoice = FoodInvoice::with([
            'customer',
            'payment',
            'details.food'
        ])->findOrFail($id);

        return view(
            'admins.manageFoods.foodInvoiceDetail.index',
            compact('invoice')
        );
    }

    public function edit($id)
    {
        $invoice = FoodInvoice::with('details')->findOrFail($id);

        $foods = Food::all();
        $customers = Customer::all();
        $payments = payment_method::all();

        return view(
            'admins.manageFoods.foodInvoice.edit',
            compact(
                'invoice',
                'foods',
                'customers',
                'payments'
            )
        );
    }

    public function update(Request $request, $id)
    {
        $invoice = FoodInvoice::findOrFail($id);

        $invoice->update([
            'customerID' => $request->customerID,
            'paymentID' => $request->paymentID,
            'orderDate' => $request->orderTime
        ]);


        // xóa chi tiết cũ
        FoodInvoiceDetail::where(
            'foodInvoiceID',
            $id
        )->delete();


        // tính total mới
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

        $invoice->update([
            'total' => $total
        ]);


        return redirect()
            ->route('foodInvoice.index')
            ->with('success', 'Cập nhật thành công');
    }

    public function destroy($id)
    {
        FoodInvoice::findOrFail($id)->delete();

        return redirect()
            ->route('foodInvoice.index')
            ->with('success', 'Xóa thành công');
    }
}
