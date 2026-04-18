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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $admins = admin::query()
            ->when($search, function ($query) use ($search) {
                $query->where('adminID', 'like', "%{$search}%")
                    ->orWhere('fullName', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            })
            ->paginate(5)
            ->withQueryString();

        return view('admins.manageUser.admin.index', ['admins' => $admins]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admins.manageUser.admin.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation
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

        // Tạo nhân viên mới
        Admin::create([
            'fullName' => $validated['fullName'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $adminID)
    {
        $admins = admin::findOrFail($adminID);
        return view('admins.manageUser.admin.edit', ['admins' => $admins]);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('admins')->ignore($admin->adminID, 'adminID'),
            ],
            'role' => 'required|in:admin,ticket_staff,food_staff',
            'password' => 'nullable|string|min:6',
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

        // Nếu nhập password mới, hash và update
        if (!empty($validated['password'])) {
            $admin->password = Hash::make($validated['password']);
        }

        $admin->save();

        return redirect()->route('admin.index')
            ->with('success', 'Cập nhật nhân viên thành công.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $admins = admin::findOrFail($id);
        $admins->delete();

        return redirect()->route('admin.index')->with('success', 'Xóa thành công');
    }
}
