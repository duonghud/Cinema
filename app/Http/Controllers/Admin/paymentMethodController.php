<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\payment_method;

class paymentMethodController extends Controller
{
    /**
     * Hiển thị danh sách phương thức thanh toán (kèm tìm kiếm, lọc và phân trang).
     */
    public function index(Request $request)
    {
        // Lấy dữ liệu tìm kiếm từ Request và cắt bỏ khoảng trắng thừa
        $search = trim((string) $request->input('search'));
        $paymentName = trim((string) $request->input('payment_name'));

        // Khởi tạo Eloquent Query trên Model payment_method
        $paymentMethods = payment_method::query()
            // Bộ lọc tìm kiếm theo từ khóa (Nếu người dùng nhập ô search)
            ->when($search, function ($query) use ($search) {
                // Tìm gần đúng (LIKE) theo ID hoặc Tên phương thức
                $query->where('paymentID', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            })
            // Bộ lọc chính xác theo tên phương thức thanh toán chọn từ Dropdown
            ->when($paymentName, function ($query) use ($paymentName) {
                $query->where('name', $paymentName);
            })
            ->paginate(5) // Phân trang: 5 bản ghi trên mỗi trang
            ->withQueryString(); // Giữ lại các tham số tìm kiếm (query params) trên URL khi chuyển trang

        // Lấy danh sách các tên phương thức (không trùng lặp) để làm dữ liệu cho thẻ Select (bộ lọc)
        $paymentNames = payment_method::query()
            ->select('name')
            ->distinct() // Loại bỏ các tên trùng lặp
            ->orderBy('name') // Sắp xếp theo bảng chữ cái từ A-Z
            ->pluck('name', 'name'); // Chuyển kết quả thành mảng Key => Value (dạng ['MoMo' => 'MoMo'])

        // Trả về View danh sách cùng dữ liệu đã xử lý
        return view('admins.paymentMethod.index', [
            'paymentMethods' => $paymentMethods,
            // Cấu hình cấu trúc mảng bộ lọc gửi ra ngoài giao diện Blade để render động
            'filters' => [[
                'name' => 'payment_name',
                'all_label' => 'Tất cả phương thức',
                'options' => $paymentNames->toArray(),
            ]],
        ]);
    }

    /**
     * Hiển thị form giao diện thêm mới phương thức thanh toán.
     */
    public function create()
    {
        return view('admins.paymentMethod.create');
    }

    /**
     * Tiếp nhận dữ liệu và lưu mới phương thức thanh toán vào cơ sở dữ liệu.
     */
    public function store(Request $request)
    {
        // Xác thực dữ liệu đầu vào (Yêu cầu trường 'name' không được để trống)
        $request->validate([
            'name' => 'required'
        ]);

        // Khởi tạo một đối tượng Model mới
        $paymentMethods = new payment_method();
        // Gán giá trị nhận được từ form vào thuộc tính 'name' của Model
        $paymentMethods->name = request('name');
        // Lưu bản ghi mới vào Database
        $paymentMethods->save();

        // Chuyển hướng về trang danh sách kèm theo thông báo flash session thành công
        return redirect()->route('paymentMethod.index')->with('success', 'Tạo thành công');
    }

    /**
     * Hiển thị chi tiết một phương thức thanh toán cụ thể (Hiện tại không dùng).
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Hiển thị form giao diện chỉnh sửa phương thức thanh toán.
     */
    public function edit(string $paymentID)
    {
        // Tìm bản ghi theo ID khóa chính, nếu không tồn tại tự động ném ra lỗi 404
        $paymentMethods = payment_method::findOrFail($paymentID);
        
        // Trả về view sửa và truyền dữ liệu của bản ghi cần sửa sang
        return view('admins.paymentMethod.edit', ['paymentMethods' => $paymentMethods]);
    }

    /**
     * Tiếp nhận dữ liệu cập nhật và sửa đổi bản ghi trong cơ sở dữ liệu.
     */
    public function update(Request $request, string $paymentID)
    {
        // Tìm bản ghi cần sửa trong DB, trả về lỗi 404 nếu không tìm thấy
        $paymentMethods = payment_method::findOrFail($paymentID);

        // Xác thực dữ liệu đầu vào (Trường 'name' bắt buộc phải có)
        $request->validate([
            'name' => 'required'
        ]);

        // Cập nhật lại giá trị thuộc tính 'name' từ dữ liệu Request mới gửi lên
        $paymentMethods->name = $request->input('name');
        // Lưu các thay đổi vào Database
        $paymentMethods->save();

        // Chuyển hướng về trang danh sách kèm thông báo thành công (Lưu ý: Chỗ này bạn đang để chuỗi là 'Tạo thành công', bạn có thể sửa thành 'Cập nhật thành công' nếu muốn)
        return redirect()->route('paymentMethod.index')->with('success', 'Tạo thành công');
    }

    /**
     * Xóa phương thức thanh toán khỏi cơ sở dữ liệu.
     */
    public function destroy(string $id)
    {
        // Tìm bản ghi cần xóa dựa trên ID truyền vào, lỗi 404 nếu không thấy
        $paymentMethods = payment_method::findOrFail($id);
        // Thực hiện xóa bản ghi khỏi hệ thống
        $paymentMethods->delete();

        // Chuyển hướng về trang danh sách kèm thông báo xóa thành công
        return redirect()->route('paymentMethod.index')->with('success', 'Xóa thành công');
    }
}