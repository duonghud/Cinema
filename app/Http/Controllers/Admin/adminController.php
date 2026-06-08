<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class adminController extends Controller
{
    /**
     * Hiển thị danh sách nhân viên kèm bộ lọc chức vụ và tìm kiếm từ khóa.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $role = trim((string) $request->input('role'));

        $admins = admin::query()
            // Tìm kiếm chuỗi gần đúng trên các thông tin định danh của nhân viên
            ->when($search, function ($query) use ($search) {
                $query->where('adminID', 'like', "%{$search}%")
                    ->orWhere('fullName', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            })
            // Lọc chính xác theo nhóm chức vụ (admin, ticket_staff, food_staff)
            ->when($role, function ($query) use ($role) {
                $query->where('role', $role);
            })
            ->paginate(5)
            ->withQueryString(); // Duy trì từ khóa tìm kiếm và bộ lọc khi nhấn chuyển trang phân trang

        // Lấy danh sách các chức vụ hiện có trong DB và chuyển đổi thành ngôn ngữ hiển thị (Tiếng Việt)
        $roles = admin::query()
            ->select('role')
            ->distinct()
            ->orderBy('role')
            ->pluck('role')
            ->mapWithKeys(function ($item) {
                // Ánh xạ giá trị gốc trong DB thành nhãn thân thiện với người dùng trên giao diện
                return [$item => match ($item) {
                    'admin' => 'Quản trị',
                    'ticket_staff' => 'Bán vé',
                    'food_staff' => 'Đồ ăn',
                    default => $item,
                }];
            });

        return view('admins.manageUser.admin.index', [
            'admins' => $admins,
            'filters' => [[
                'name' => 'role',
                'all_label' => 'Tất cả chức vụ',
                'options' => $roles->toArray(),
            ]],
        ]);
    }

    /**
     * Hiển thị giao diện form thêm mới tài khoản nhân viên.
     */
    public function create()
    {
        return view('admins.manageUser.admin.create');
    }

    /**
     * Kiểm tra ràng buộc dữ liệu đầu vào và tiến hành mã hóa mật khẩu, tạo mới tài khoản.
     */
    public function store(Request $request)
    {
        // Thực hiện xác thực (Email không được phép trùng lặp trong bảng admins)
        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,ticket_staff,food_staff',
        ], [
            'fullName.required' => 'Họ tên không được để trống.',
            'fullName.max' => 'Họ tên không được vượt quá 255 ký tự.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã tồn tại.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'role.required' => 'Chức vụ không được để trống.',
            'role.in' => 'Chức vụ không hợp lệ.',
        ]);

        // Tạo bản ghi nhân viên mới kèm cơ chế băm mật khẩu bảo mật Bcrypt thông qua lớp Hash
        Admin::create([
            'fullName' => $validated['fullName'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']), // Đảm bảo không lưu mật khẩu dạng chữ thô (Plain text)
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.index')
            ->with('success', 'Nhân viên mới đã được thêm thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Hiển thị giao diện form chỉnh sửa thông tin nhân viên theo mã ID.
     */
    public function edit(string $adminID)
    {
        $admins = admin::findOrFail($adminID);
        return view('admins.manageUser.admin.edit', ['admins' => $admins]);
    }

    /**
     * Cập nhật thông tin tài khoản (Hỗ trợ cơ chế giữ nguyên hoặc cập nhật mật khẩu tùy chọn).
     */
    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        // Xác thực thông tin sửa đổi
        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                // Bỏ qua kiểm tra trùng lặp với chính ID của nhân viên hiện tại để tránh lỗi khi lưu mà không đổi email
                Rule::unique('admins')->ignore($admin->adminID, 'adminID'),
            ],
            'role' => 'required|in:admin,ticket_staff,food_staff',
            'password' => 'nullable|string|min:6', // Cho phép để trống (nullable) nếu người quản trị không muốn đổi mật khẩu
        ], [
            'fullName.required' => 'Họ tên không được để trống.',
            'fullName.max' => 'Họ tên không được vượt quá 255 ký tự.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã tồn tại.',
            'role.required' => 'Chức vụ không được để trống.',
            'role.in' => 'Chức vụ không hợp lệ.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        $admin->fullName = $validated['fullName'];
        $admin->email = $validated['email'];
        $admin->role = $validated['role'];

        // Chỉ tiến hành băm và cập nhật mật khẩu nếu có dữ liệu text mới được nhập vào form
        if (!empty($validated['password'])) {
            $admin->password = Hash::make($validated['password']);
        }

        $admin->save();

        return redirect()->route('admin.index')
            ->with('success', 'Cập nhật nhân viên thành công.');
    }

    /**
     * Xóa tài khoản nhân viên khỏi hệ thống quản trị.
     */
    public function destroy(string $id)
    {
        $admins = admin::findOrFail($id);
        $admins->delete();

        return redirect()->route('admin.index')->with('success', 'Xóa thành công');
    }
}