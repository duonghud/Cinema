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
        return view('auth.customerLogin');
    }

    // Xử lý login
    public function customerLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email',
            'password' => 'required|min:6'
        ], [
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không đúng định dạng',
            'email.exists' => 'Email chưa được đăng ký',

            'password.required' => 'Mật khẩu không được để trống',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        // Check mật khẩu
        if (!Hash::check($request->password, $customer->password)) {
            return back()->with('error', 'Mật khẩu không đúng');
        }

        // Đăng nhập thành công
        // Navbar và middleware đều đọc thông tin đăng nhập từ key customer
        $request->session()->put('customer', $customer);

        return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
    }

    // logout
    public function logout(Request $request)
    {
        // Xoa trang thai dang nhap customer va reset session khi logout.
        $request->session()->forget('customer');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Đã đăng xuất');
    }
}
