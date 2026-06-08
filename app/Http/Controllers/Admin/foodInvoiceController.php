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
    /**
     * Hiển thị danh sách hóa đơn đồ ăn kèm chức năng phân trang, tìm kiếm đa trường và các bộ lọc dropdown.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $customerId = trim((string) $request->input('customer_id'));
        $paymentId = trim((string) $request->input('payment_id'));

        // Eager loading dữ liệu Khách hàng, Phương thức thanh toán và lồng sâu (nested) dữ liệu Món ăn qua bảng chi tiết
        $invoices = FoodInvoice::with([
            'customer',
            'payment',
            'details.food'
        ])
            // Khối xử lý tìm kiếm tổng hợp (Mã hóa đơn, ngày đặt, số tiền hoặc thông tin từ bảng liên kết)
            ->when($search, function ($query) use ($search) {
                $query->where('foodInvoiceID', 'like', "%{$search}%")
                    ->orWhere('orderDate', 'like', "%{$search}%")
                    ->orWhere('total', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('fullName', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('payment', function ($paymentQuery) use ($search) {
                        $paymentQuery->where('name', 'like', "%{$search}%");
                    })
                    // Tìm kiếm hóa đơn dựa trên việc có chứa món ăn khớp với tên từ khóa
                    ->orWhereHas('details.food', function ($foodQuery) use ($search) {
                        $foodQuery->where('foodName', 'like', "%{$search}%");
                    });
            })
            // Lọc chính xác theo ID Khách hàng
            ->when($customerId, function ($query) use ($customerId) {
                $query->where('customerID', $customerId);
            })
            // Lọc chính xác theo ID Phương thức thanh toán
            ->when($paymentId, function ($query) use ($paymentId) {
                $query->where('paymentID', $paymentId);
            })
            // Sắp xếp ưu tiên theo ngày tạo (nếu có), nếu không có thì lấy ngày đặt và xếp giảm dần (mới nhất lên đầu)
            ->orderByRaw('COALESCE(created_at, orderDate) DESC')
            ->orderByDesc('foodInvoiceID')
            ->paginate(5)
            ->withQueryString();

        // Lấy danh sách đổ vào thẻ select lọc trên giao diện
        $customers = Customer::query()
            ->orderBy('fullName')
            ->pluck('fullName', 'customerID');

        $payments = payment_method::query()
            ->orderBy('name')
            ->pluck('name', 'paymentID');

        return view(
            'admins.manageFoods.foodInvoice.index',
            [
                'invoices' => $invoices,
                'filters' => [
                    [
                        'name' => 'customer_id',
                        'all_label' => 'Tất cả khách hàng',
                        'options' => $customers->toArray(),
                    ],
                    [
                        'name' => 'payment_id',
                        'all_label' => 'Tất cả thanh toán',
                        'options' => $payments->toArray(),
                    ],
                ],
            ]
        );
    }

    /**
     * Giao diện tạo hóa đơn mới (Chặn không cho tạo nếu hệ thống chưa có món ăn nào dữ liệu sẵn).
     */
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

    /**
     * Validate dữ liệu, tự động tính tổng tiền và tạo hóa đơn song song với chi tiết hóa đơn (Dùng Transaction).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customerID' => 'required|exists:customers,customerID',
            'paymentID' => 'required|exists:payment_methods,paymentID',
            'orderTime' => 'required|date',
            'foods' => 'required|array', // Mảng có cấu trúc: [foodID => số lượng]
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

        // Logic kiểm tra thủ công: Đảm bảo người dùng phải nhập số lượng > 0 cho ít nhất 1 món ăn
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

        // Lọc bỏ các món ăn có số lượng bằng 0 và chuẩn hóa mảng dữ liệu [foodID => quantity]
        $selectedFoods = collect($validated['foods'])
            ->filter(fn($quantity) => (int) $quantity > 0)
            ->mapWithKeys(fn($quantity, $foodID) => [(int) $foodID => (int) $quantity]);

        // Truy vấn nhanh toàn bộ món ăn được chọn dựa trên danh sách Key (foodID)
        $foods = Food::whereIn('foodID', $selectedFoods->keys()->all())->get()->keyBy('foodID');
        $total = 0;

        // Tính tổng tiền dựa trên giá gốc lưu trong DB để tránh can thiệp client-side sửa giá
        foreach ($selectedFoods as $foodID => $quantity) {
            $food = $foods->get($foodID);
            if ($food) {
                $total += $food->price * $quantity;
            }
        }

        // Sử dụng Transaction để đảm bảo tính toàn vẹn: Hóa đơn lỗi thì không lưu Chi tiết và ngược lại
        DB::transaction(function () use ($validated, $selectedFoods, $total, $request) {
            $invoice = FoodInvoice::create([
                'customerID' => $validated['customerID'],
                'paymentID' => $validated['paymentID'],
                'adminID' => $request->session()->get('admin_auth.adminID', 1), // Mặc định lấy từ Session, nếu trống lấy ID là 1
                'orderDate' => $validated['orderTime'],
                'total' => $total
            ]);

            // Vòng lặp lưu thông tin vào bảng chi tiết hóa đơn đồ ăn
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

    /**
     * Tính toán động các chỉ số phụ (Thành tiền từng món, tổng số lượng) và hiển thị chi tiết hóa đơn.
     */
    public function show($id)
    {
        $invoice = FoodInvoice::with([
            'customer',
            'payment',
            'details.food'
        ])->findOrFail($id);

        // Map lại danh sách để tính toán giá trị thành tiền (subtotal) động phục vụ cho giao diện hiển thị
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
        $detailCount = $detailRows->count(); // Đếm xem có bao nhiêu món ăn khác nhau
        $totalQuantity = $detailRows->sum('quantity'); // Tính tổng số lượng đồ ăn

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

    /**
     * Hiển thị giao diện sửa thông tin hóa đơn đồ ăn.
     */
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
            compact('invoice', 'foods', 'customers', 'payments')
        );
    }

    /**
     * Xử lý cập nhật: Làm sạch toàn bộ chi tiết cũ trước khi nạp lại mảng chi tiết mới nhằm tránh xung đột dữ liệu.
     */
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

        // LÀM SẠCH: Xóa hết các bản ghi chi tiết cũ của hóa đơn này để chuẩn bị ghi đè dữ liệu mới
        FoodInvoiceDetail::where('foodInvoiceID', $id)->delete();

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

                // Ghi mới chi tiết hóa đơn sau khi đã xóa cũ
                FoodInvoiceDetail::create([
                    'foodInvoiceID' => $id,
                    'foodID' => $foodID,
                    'quantity' => $qty
                ]);

                $total += $food->price * $qty;
            }
        }

        // Cập nhật lại thông tin hóa đơn cha bao gồm tổng tiền mới tính toán
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

    /**
     * Xóa hóa đơn đồ ăn khỏi hệ thống.
     */
    public function destroy($id)
    {
        FoodInvoice::findOrFail($id)->delete();

        return redirect()
            ->route('foodInvoice.index')
            ->with('success', 'Xóa thành công');
    }
}