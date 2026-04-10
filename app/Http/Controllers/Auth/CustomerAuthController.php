<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\Customer;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends Controller
{
    // Form đăng ký
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
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã tồn tại',
            'password.required' => 'Mật khẩu không được để trống',
            'password.min' => 'Mật khẩu ít nhất 6 ký tự',
            'phoneNumber.required' => 'SĐT không được để trống',
            'address.required' => 'Địa chỉ không được để trống',
        ]);

        $customer = Customer::create([
            'fullName' => $request->fullName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phoneNumber' => $request->phoneNumber,
            'address' => $request->address,
        ]);

        // lưu session
        session([
            'customer' => $customer
        ]);

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
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Email không được để trống',
            'password.required' => 'Mật khẩu không được để trống',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer) {
            return back()->with('error', 'Email không tồn tại');
        }

        if (!Hash::check($request->password, $customer->password)) {
            return back()->with('error', 'Sai mật khẩu');
        }

        // lưu session
        session([
            'customer' => $customer
        ]);

        return redirect('/')->with('success', 'Đăng nhập thành công!');
    }

    // logout
    public function logout()
    {
        session()->forget('customer');

        return redirect('/')->with('success', 'Đã đăng xuất');
    }
}   