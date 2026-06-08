<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\foodInvoiceDetail;
use App\Models\Admin\foodInvoice;
use App\Models\Admin\food;

class FoodInvoiceDetailController extends Controller
{
    /**
     * Hiển thị danh sách chi tiết hóa đơn đồ ăn.
     */
    public function index()
    {
        // dữ liệu liên kết bảng để tối ưu hóa truy vấn SQL
        $details = foodInvoiceDetail::with(['foodInvoice', 'food'])->get();
        return view('admins.manageFoods.foodInvoiceDetail.index', compact('details'));
    }

    /**
     * Hiển thị form thêm mới đồ ăn vào hóa đơn.
     */
    public function create()
    {
        $foodInvoices = foodInvoice::all();
        $foods = food::all();

        return view('admins.manageFoods.foodInvoiceDetail.create', compact('foodInvoices', 'foods'));
    }

    /**
     * Xác thực và lưu mới một mục chi tiết hóa đơn.
     */
    public function store(Request $request)
    {
        $request->validate([
            'foodInvoiceID' => 'required',
            'foodID' => 'required',
            'quantity' => 'required|integer|min:1' // Số lượng bắt buộc phải là số nguyên và lớn hơn hoặc bằng 1
        ]);

        foodInvoiceDetail::create($request->all());

        return redirect()->route('foodInvoiceDetail.index')
            ->with('success', 'Created successfully');
    }

    /**
     * Hiển thị form sửa đổi (Sử dụng đồng thời 2 tham số vì đây là bảng trung gian dùng khóa chính hợp thành).
     */
    public function edit($foodInvoiceID, $foodID)
    {
        // Khớp đồng thời cả mã hóa đơn và mã đồ ăn để tìm ra bản ghi duy nhất, trả về 404 nếu sai mã
        $detail = foodInvoiceDetail::where('foodInvoiceID', $foodInvoiceID)
            ->where('foodID', $foodID)
            ->firstOrFail();

        $foodInvoices = foodInvoice::all();
        $foods = food::all();

        return view('admins.manageFoods.foodInvoiceDetail.edit', compact('detail', 'foodInvoices', 'foods'));
    }

    /**
     * Cập nhật số lượng đồ ăn trong hóa đơn dựa theo bộ đôi khóa chính.
     */
    public function update(Request $request, $foodInvoiceID, $foodID)
    {
        $detail = foodInvoiceDetail::where('foodInvoiceID', $foodInvoiceID)
            ->where('foodID', $foodID)
            ->firstOrFail();

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Chỉ cập nhật lại số lượng mua
        $detail->update([
            'quantity' => $request->quantity
        ]);

        return redirect()->route('foodInvoiceDetail.index')
            ->with('success', 'Updated successfully');
    }

    /**
     * Xóa một mục món ăn ra khỏi hóa đơn dựa theo bộ đôi khóa chính.
     */
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