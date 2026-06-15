<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\Customer;
use App\Http\Requests\ProfileUpdateRequest;
use App\Services\CustomerProfileService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CustomerAuthController extends Controller
{
    public function __construct(
        protected CustomerProfileService $profileService
    ) {}

    // ── Đăng ký ──────────────────────────────────────────────────────────────

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'fullName'    => 'required|string|max:255',
            'email'       => 'required|email|unique:customers,email',
            'password'    => 'required|min:6',
            'phoneNumber' => 'required',
            'address'     => 'required',
        ], [
            'fullName.required'    => 'Họ tên không được để trống',
            'email.required'       => 'Email không được để trống',
            'email.email'          => 'Email không đúng định dạng',
            'email.unique'         => 'Email đã tồn tại',
            'password.required'    => 'Mật khẩu không được để trống',
            'password.min'         => 'Mật khẩu ít nhất 6 ký tự',
            'phoneNumber.required' => 'SĐT không được để trống',
            'address.required'     => 'Địa chỉ không được để trống',
        ]);

        $customer = Customer::create([
            'fullName'    => $request->fullName,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'phoneNumber' => $request->phoneNumber,
            'address'     => $request->address,
        ]);

        session(['customer' => $customer]);

        return redirect('/')->with('success', 'Đăng ký thành công!');
    }

    // ── Đăng nhập ─────────────────────────────────────────────────────────────

    public function showLogin()
    {
        return view('auth.customerLogin');
    }

    public function customerLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:customers,email',
            'password' => 'required|min:6',
        ], [
            'email.required'    => 'Email không được để trống',
            'email.email'       => 'Email không đúng định dạng',
            'email.exists'      => 'Email chưa được đăng ký',
            'password.required' => 'Mật khẩu không được để trống',
            'password.min'      => 'Mật khẩu phải có ít nhất 6 ký tự',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!Hash::check($request->password, $customer->password)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['password' => 'Mật khẩu không đúng']);
        }

        $request->session()->put('customer', $customer);

        return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
    }


    public function showProfile()
    {
        $customer = session('customer');

        if (!$customer) {
            return redirect()->route('customer.login');
        }

        // Lấy bản mới nhất từ DB để tránh dùng dữ liệu cũ từ session
        $customer = Customer::find($customer->customerID);

        return view('system.profile', compact('customer'));
    }

    public function updateProfile(ProfileUpdateRequest $request)
    {
        $customer = Customer::find(session('customer')->customerID);

        if (!$customer) {
            return redirect()->route('customer.login');
        }

        try {
            $updated = $this->profileService->update($customer, $request);

            // Đồng bộ lại session với dữ liệu mới
            session(['customer' => $updated]);

            return redirect()->route('customer.profile')
                ->with('success', 'Cập nhật hồ sơ thành công!');
        } catch (\Throwable $e) {
            Log::error('Profile update failed: ' . $e->getMessage());

            return back()->with('error', 'Đã xảy ra lỗi khi cập nhật. Vui lòng thử lại.');
        }
    }

    // ── Đăng xuất ─────────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        $request->session()->forget('customer');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Đã đăng xuất');
    }
}