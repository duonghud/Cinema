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
    /**
     * Hiển thị danh sách hóa đơn kèm chức năng phân trang, tìm kiếm nâng cao và bộ lọc.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $customerId = trim((string) $request->input('customer_id'));
        $paymentId = trim((string) $request->input('payment_id'));
        $adminId = trim((string) $request->input('admin_id'));
        $createDate = trim((string) $request->input('create_date'));

        // Eager loading nạp trước dữ liệu Khách hàng, Admin xử lý, Phương thức thanh toán để tối ưu số câu lệnh SQL
        $invoices = Invoice::with([
            'customer',
            'admin',
            'paymentMethod'
        ])
            // Xử lý khối tìm kiếm tổng hợp (Hóa đơn ID, số tiền, ngày tạo hoặc thông tin liên bảng)
            ->when($search, function ($query) use ($search) {
                $query->where('invoiceID', 'like', "%{$search}%")
                    ->orWhere('totalAmount', 'like', "%{$search}%")
                    ->orWhere('createDate', 'like', "%{$search}%")
                    // Tìm kiếm dựa trên Tên hoặc Email của Khách hàng
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('fullName', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    // Tìm kiếm dựa trên Tên hoặc Email của Nhân viên (Admin) lập hóa đơn
                    ->orWhereHas('admin', function ($q) use ($search) {
                        $q->where('fullName', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    // Tìm kiếm dựa trên tên Phương thức thanh toán
                    ->orWhereHas('paymentMethod', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            // Lọc chính xác theo ID Khách hàng từ dropdown (nếu có chọn)
            ->when($customerId, function ($query) use ($customerId) {
                $query->where('customerID', $customerId);
            })
            // Lọc chính xác theo ID Phương thức thanh toán
            ->when($paymentId, function ($query) use ($paymentId) {
                $query->where('paymentID', $paymentId);
            })
            // Lọc chính xác theo ID Admin phụ trách
            ->when($adminId, function ($query) use ($adminId) {
                $query->where('adminID', $adminId);
            })
            // So sánh ngày (bỏ qua phần giờ phút giây) của trường createDate với bộ lọc
            ->when($createDate, function ($query) use ($createDate) {
                $query->whereDate('createDate', $createDate);
            })
            // Sắp xếp hóa đơn mới nhất lên trên đầu theo cả ngày tạo và ID
            ->orderByDesc('createDate')
            ->orderByDesc('invoiceID')
            ->paginate(5)
            ->withQueryString(); // Giữ lại các tham số filter trên URL khi bấm chuyển trang phân trang

        // Chuẩn bị dữ liệu mảng đổ vào các thẻ <select> bộ lọc trên giao diện index
        $customers = Customer::query()->orderBy('fullName')->pluck('fullName', 'customerID');
        $payments = payment_method::query()->orderBy('name')->pluck('name', 'paymentID');
        $admins = Admin::query()->orderBy('fullName')->pluck('fullName', 'adminID');
        
        // Trích xuất danh sách ngày duy nhất (Distinct) từ DB, chuyển định dạng timestamp sang d/m/Y để làm bộ lọc ngày
        $dates = Invoice::query()
            ->selectRaw('DATE(createDate) as date_only')
            ->distinct()
            ->orderByDesc('date_only')
            ->pluck('date_only')
            ->mapWithKeys(function ($date) {
                return [$date => \Carbon\Carbon::parse($date)->format('d/m/Y')];
            });

        return view('admins.invoices.index', [
            'invoices' => $invoices,
            'filters' => [
                [
                    'name' => 'payment_id',
                    'all_label' => 'Tất cả thanh toán',
                    'options' => $payments->toArray(),
                ],
                [
                    'name' => 'create_date',
                    'all_label' => 'Tất cả ngày tạo',
                    'options' => $dates->toArray(),
                ],
            ],
        ]);
    }

    /**
     * Hiển thị giao diện lập hóa đơn mới kèm toàn bộ danh sách khách, admin, phương thức thanh toán.
     */
    public function create()
    {
        return view('admins.invoices.create', [
            'customers' => Customer::all(),
            'admins'    => Admin::all(),
            'payments'  => payment_method::all(),
        ]);
    }

    /**
     * Xác thực tính hợp lệ của dữ liệu đầu vào và lưu hóa đơn mới vào cơ sở dữ liệu.
     */
    public function store(Request $request)
    {
        // Ràng buộc dữ liệu ngoại lai bắt buộc phải tồn tại trong các bảng tương ứng
        $request->validate([
            'customerID'  => 'required|exists:customers,customerID',
            'adminID'     => 'required|exists:admins,adminID',
            'paymentID'   => 'required|exists:payment_methods,paymentID',
            'totalAmount' => 'required|numeric|min:0',
        ]);

        Invoice::create([
            'createDate'  => now(), // Tự động lấy thời gian hiện tại của hệ thống làm ngày lập hóa đơn
            'totalAmount' => $request->totalAmount,
            'customerID'  => $request->customerID,
            'adminID'     => $request->adminID,
            'paymentID'   => $request->paymentID,
        ]);

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Tạo hóa đơn thành công');
    }

    /**
     * Hiển thị giao diện chỉnh sửa thông tin hóa đơn dựa theo ID.
     */
    public function edit($id)
    {
        return view('admins.invoices.edit', [
            'invoice'   => Invoice::findOrFail($id),
            'customers' => Customer::all(),
            'admins'    => Admin::all(),
            'payments'  => payment_method::all(),
        ]);
    }

    /**
     * Cập nhật thông tin chi tiết của hóa đơn.
     */
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

    /**
     * Hiển thị chi tiết nội dung hóa đơn (Bao gồm thông tin thanh toán, danh sách vé đi kèm, ghế ngồi và tên phim).
     */
    public function show($id)
    {
        // Nạp sâu (Deep Eager Loading) các mối quan hệ lồng nhau để lấy thông tin chi tiết từng vé thuộc hóa đơn
        $invoice = Invoice::with([
            'customer',
            'admin',
            'paymentMethod',
            'tickets.seat',             // Nạp thông tin Ghế của chiếc vé đó
            'tickets.showTime.movie'    // Nạp thông tin Suất chiếu và Tên phim của chiếc vé đó
        ])->findOrFail($id);

        return view('admins.invoices.show', compact('invoice'));
    }

    /**
     * Xóa bản ghi hóa đơn khỏi hệ thống.
     */
    public function destroy($id)
    {
        Invoice::destroy($id);

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Xóa thành công');
    }
}