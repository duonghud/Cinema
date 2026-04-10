<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\FoodInvoice;
use App\Models\Admin\FoodInvoiceDetail;
use App\Models\Admin\Food;
use App\Models\Admin\Customer;
use App\Models\Admin\payment_method;
use Illuminate\Support\Facades\DB;

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

        if ($foods->isEmpty()) {
            return redirect()
                ->route('food.index')
                ->with('error', 'Vui lòng tạo món ăn trước khi lập hóa đơn đồ ăn.');
        }

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

        $selectedFoods = collect($validated['foods'])

            ->filter(fn($quantity) => (int) $quantity > 0)
            ->mapWithKeys(fn($quantity, $foodID) => [(int) $foodID => (int) $quantity]);

        $foods = Food::whereIn('foodID', $selectedFoods->keys()->all())->get()->keyBy('foodID');
        $total = 0;

        foreach ($selectedFoods as $foodID => $quantity) {
            $food = $foods->get($foodID);

            if ($food) {
                $total += $food->price * $quantity;
            }
        }

        DB::transaction(function () use ($validated, $selectedFoods, $total, $request) {
            $invoice = FoodInvoice::create([
                'customerID' => $validated['customerID'],
                'paymentID' => $validated['paymentID'],
                'adminID' => $request->session()->get('admin_auth.adminID', 1),
                'orderDate' => $validated['orderTime'],
                'total' => $total
            ]);

            foreach ($selectedFoods as $foodID => $quantity) {
                FoodInvoiceDetail::create([
                    'foodInvoiceID' => $invoice->foodInvoiceID,
                    'foodID' => $foodID,
                    'quantity' => $quantity
                ]);
            }
        });


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

        $detailRows = $invoice->details->map(function ($detail) {
            $food = $detail->food;
            $unitPrice = (float) ($food->price ?? 0);

            return [
                'foodName' => $food->foodName ?? 'Món ăn không tồn tại',
                'quantity' => $detail->quantity,
                'unitPrice' => $unitPrice,
                'subtotal' => $detail->quantity * $unitPrice,
            ];
        });

        $calculatedTotal = $detailRows->sum('subtotal');
        $displayTotal = (float) ($invoice->total ?? $calculatedTotal);
        $formattedOrderDate = \Illuminate\Support\Carbon::parse($invoice->orderDate)->format('d/m/Y H:i');
        $detailCount = $detailRows->count();
        $totalQuantity = $detailRows->sum('quantity');

        return view(
            'admins.manageFoods.foodInvoiceDetail.index',
            compact(
                'invoice',
                'detailRows',
                'displayTotal',
                'formattedOrderDate',
                'detailCount',
                'totalQuantity'
            )
        );
    }

    public function edit($id)
    {
        $invoice = FoodInvoice::with('details')->findOrFail($id);

        $foods = Food::all();
        $customers = Customer::all();
        $payments = payment_method::all();

        if ($foods->isEmpty()) {
            return redirect()
                ->route('food.index')
                ->with('error', 'Vui lòng tạo món ăn trước khi cập nhật hóa đơn đồ ăn.');
        }

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

        $validated = $request->validate([
            'customerID' => 'required|exists:customers,customerID',
            'paymentID' => 'required|exists:payment_methods,paymentID',
            'orderTime' => 'required|date',
            'foods' => 'required|array',
            'foods.*' => 'integer|min:0',
        ]);

        // kiểm tra có món ăn nào được chọn không
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

        // xóa chi tiết cũ
        FoodInvoiceDetail::where('foodInvoiceID', $id)->delete();

        // tính total mới
        $total = 0;
        foreach ($validated['foods'] as $foodID => $qty) {
            if ($qty > 0) {
                $food = Food::find($foodID);
                if (!$food) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'foods' => "Món ăn với ID {$foodID} không tồn tại."
                        ]);
                }

                FoodInvoiceDetail::create([
                    'foodInvoiceID' => $id,
                    'foodID' => $foodID,
                    'quantity' => $qty
                ]);

                $total += $food->price * $qty;
            }
        }

        $invoice->update([
            'customerID' => $validated['customerID'],
            'paymentID' => $validated['paymentID'],
            'orderDate' => $validated['orderTime'],
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
