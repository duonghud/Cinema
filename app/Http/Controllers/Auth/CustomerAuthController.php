<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CustomerAuthController extends Controller
{
    // Hiển thị form đăng ký
    public function showRegister()
    {
        return view('auth.register');
    }

    // Xử lý đăng ký
    public function register(Request $request)
    {
        $request->validate([
            'fullName' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|min:6',
            'phoneNumber' => 'required',
            'address' => 'required',
        ], [
            'fullName.required' => 'Họ tên không được để trống',
            'fullName.string' => 'Họ tên phải là chuỗi',
            'fullName.max' => 'Họ tên tối đa 255 ký tự',
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email này đã tồn tại',
            'password.required' => 'Mật khẩu không được để trống',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'phoneNumber.required' => 'Số điện thoại không được để trống',
            'address.required' => 'Địa chỉ không được để trống',
        ]);

        $customer = Customer::create([
            'fullName' => $request->fullName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phoneNumber' => $request->phoneNumber,
            'address' => $request->address,
        ]);

        // login luôn sau khi đăng ký
        Auth::guard('customer')->login($customer);

        return redirect('/')->with('success', 'Đăng ký thành công!');
    }

    // Form login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Xử lý login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('customer')->attempt($credentials)) {
            return redirect('/')->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors([
            'email' => 'Sai email hoặc mật khẩu'
        ]);
    }

    // logout
    public function logout()
    {
        Auth::guard('customer')->logout();
        return redirect('/');
    }
}
