<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\foodInvoiceDetail;
use App\Models\Admin\foodInvoice;
use App\Models\Admin\food;

class FoodInvoiceDetailController extends Controller
{

    // Danh sách
    public function index()
    {
        $details = foodInvoiceDetail::with(['foodInvoice', 'food'])->get();
        return view('admins.foodInvoiceDetail.index', compact('details'));
    }

    // Form thêm
    public function create()
    {
        $foodInvoices = foodInvoice::all();
        $foods = food::all();

        return view('admins.foodInvoiceDetail.create', compact('foodInvoices', 'foods'));
    }

    // Lưu dữ liệu
    public function store(Request $request)
    {
        $request->validate([
            'foodInvoiceID' => 'required',
            'foodID' => 'required',
            'quantity' => 'required|integer|min:1'
        ]);

        foodInvoiceDetail::create($request->all());

        return redirect()->route('foodInvoiceDetail.index')
            ->with('success', 'Created successfully');
    }

    // Form sửa
    public function edit($foodInvoiceID, $foodID)
    {
        $detail = foodInvoiceDetail::where('foodInvoiceID', $foodInvoiceID)

            ->where('foodID', $foodID)
            ->firstOrFail();

        $foodInvoices = foodInvoice::all();
        $foods = food::all();

        return view('admins.foodInvoiceDetail.edit', compact('detail', 'foodInvoices', 'foods'));
    }

    // Cập nhật
    public function update(Request $request, $foodInvoiceID, $foodID)
    {
        $detail = foodInvoiceDetail::where('foodInvoiceID', $foodInvoiceID)
            ->where('foodID', $foodID)
            ->firstOrFail();

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $detail->update([
            'quantity' => $request->quantity
        ]);

        return redirect()->route('foodInvoiceDetail.index')
            ->with('success', 'Updated successfully');
    }

    // Xóa
    public function destroy($foodInvoiceID, $foodID)
    {
        $detail = foodInvoiceDetail::where('foodInvoiceID', $foodInvoiceID)
            ->where('foodID', $foodID)
            ->firstOrFail();

        $detail->delete();

        return redirect()->route('foodInvoiceDetail.index')
            ->with('success', 'Deleted successfully');
    }
}
