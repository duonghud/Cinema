<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class customerController extends Controller
{
    /**
     * Hiển thị danh sách khách hàng kèm bộ phân trang, bộ lọc địa chỉ và tìm kiếm từ khóa.
     */
    public function index(Request $request)
    {
        // Thu thập và loại bỏ khoảng trắng thừa từ dữ liệu lọc request gửi lên
        $search = trim((string) $request->input('search'));
        $address = trim((string) $request->input('address'));

        $customers = customer::query()
            // Tìm kiếm chuỗi gần đúng (LIKE) trên hầu hết các cột thông tin cá nhân của khách hàng
            ->when($search, function ($query) use ($search) {
                $query->where('customerID', 'like', "%{$search}%")
                    ->orWhere('fullName', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phoneNumber', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            })
            // Lọc chính xác theo tỉnh/thành phố hoặc địa chỉ cụ thể được chọn từ dropdown
            ->when($address, function ($query) use ($address) {
                $query->where('address', $address);
            })
            ->paginate(5)
            ->withQueryString(); // Duy trì trạng thái các tham số tìm kiếm/lọc khi chuyển đổi trang phân trang

        // Trích xuất danh sách địa chỉ duy nhất (distinct) và loại bỏ giá trị rỗng để làm menu lọc
        $addresses = customer::query()
            ->select('address')
            ->whereNotNull('address')
            ->where('address', '!=', '')
            ->distinct()
            ->orderBy('address')
            ->pluck('address', 'address');

        return view('admins.manageUser.customer.index', [
            'customers' => $customers,
            'filters' => [[
                'name' => 'address',
                'all_label' => 'Tất cả địa chỉ',
                'options' => $addresses->toArray(),
            ]],
        ]);
    }

    /**
     * Hiển thị giao diện form thêm mới tài khoản khách hàng.
     */
    public function create()
    {
        return view('admins.manageUser.customer.create');
    }

    /**
     * Kiểm tra ràng buộc dữ liệu nghiêm ngặt, băm mật khẩu bảo mật và thêm khách hàng mới.
     */
    public function store(Request $request)
    {
        // Thực hiện xác thực dữ liệu
        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|string|min:6',
            'phoneNumber' => 'required|string|max:15',
            'address' => 'required|string|max:255',
        ], [
            'fullName.required' => 'Họ tên không được để trống.',
            'fullName.max' => 'Họ tên không được vượt quá 255 ký tự.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã tồn tại.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'phoneNumber.required' => 'Số điện thoại không được để trống.',
            'phoneNumber.max' => 'Số điện thoại quá dài.',
            'address.required' => 'Địa chỉ không được để trống.',
            'address.max' => 'Địa chỉ quá dài.',
        ]);

        // Tạo bản ghi mới kèm cơ chế băm mật khẩu Bcrypt an toàn thông qua Facade Hash
        Customer::create([
            'fullName' => $validated['fullName'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']), // Đảm bảo an toàn, không lưu mật khẩu thô vào DB
            'phoneNumber' => $validated['phoneNumber'],
            'address' => $validated['address'],
        ]);

        return redirect()->route('customer.index')
            ->with('success', 'Khách hàng mới đã được thêm thành công.');
    }

    /**
     * Hiển thị giao diện form cập nhật thông tin khách hàng dựa trên ID truyền vào.
     */
    public function edit(string $customerID)
    {
        // Trả về lỗi 404 ngay lập tức nếu không tìm thấy khách hàng ứng với ID yêu cầu
        $customers = customer::findOrFail($customerID);
        return view('admins.manageUser.customer.edit', ['customers' => $customers]);
    }

    /**
     * Xác thực thông tin chỉnh sửa và tiến hành cập nhật.
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        // Xác thực thông tin thay đổi từ form gửi lên
        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                // Bỏ qua kiểm tra trùng lặp với chính ID khách hàng hiện tại để tránh lỗi báo trùng khi lưu giữ nguyên email cũ
                Rule::unique('customers')->ignore($customer->customerID, 'customerID'),
            ],
            'password' => 'nullable|string|min:6', // Hỗ trợ giữ nguyên mật khẩu cũ nếu trường này để trống
            'phoneNumber' => 'required|string|max:15',
            'address' => 'required|string|max:255',
        ], [
            'fullName.required' => 'Họ tên không được để trống.',
            'fullName.max' => 'Họ tên không được vượt quá 255 ký tự.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã tồn tại.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'phoneNumber.required' => 'Số điện thoại không được để trống.',
            'phoneNumber.max' => 'Số điện thoại quá dài.',
            'address.required' => 'Địa chỉ không được để trống.',
            'address.max' => 'Địa chỉ quá dài.',
        ]);

        // Đổ dữ liệu đã được xác thực vào đối tượng model
        $customer->fullName = $validated['fullName'];
        $customer->email = $validated['email'];
        $customer->phoneNumber = $validated['phoneNumber'];
        $customer->address = $validated['address'];

        // Kiểm tra nếu người dùng nhập mật khẩu mới thì tiến hành băm mã hóa rồi mới gán cập nhật
        if (!empty($validated['password'])) {
            $customer->password = Hash::make($validated['password']);
        }

        $customer->save();

        return redirect()->route('customer.index')
            ->with('success', 'Cập nhật khách hàng thành công.');
    }

    /**
     * Xóa vĩnh viễn tài khoản khách hàng khỏi hệ thống.
     */
    public function destroy(string $id)
    {
        $customers = customer::findOrFail($id);
        $customers->delete();

        return redirect()->route('customer.index')->with('success', 'Xóa thành công');
    }
}