<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\admin;

class adminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = admin::paginate(2);
        return view('admins.admin.index', ['admins' => $admins]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admins.admin.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fullName' => 'required',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:6',
            'role' => 'required'
        ],[
            'fullName.required' => 'Tên không được để trong',
            'email.required' => 'Email không được để trống',
            'password.required' => 'Password không được để trống',
            'role.required'=> 'Vui lòng chọn chức vụ'
        ]);

        $admins = new admin();
        $admins->fullName = $request->input('fullName');
        $admins->email = $request->input('email');
        $admins->password = $request->input('password');
        $admins->role = $request->input('role');
        $admins->save();

        return redirect()->route('admin.index')->with('success', 'Tạo thành công');
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
        return view('admins.admin.edit', ['admins' => $admins]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $adminID)
    {
        $admins = admin::findOrFail($adminID);

        $request->validate([
            'fullName' => 'required',
            'email' => 'required|email|unique:admins,email,' . $adminID . ',adminID',
            'password' => 'nullable|min:6',
            'role' => 'required'
        ]);

        $admins->fullName = $request->input('fullName');
        $admins->email = $request->input('email');
        if ($request->input('password')) {
            $admins->password = $request->input('password');
        }
        $admins->role = $request->input('role');
        $admins->save();

        return redirect()->route('admin.index')->with('success', 'Sửa thành công');
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
