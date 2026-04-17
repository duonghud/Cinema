<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\admin;
use RuntimeException;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        if (session()->has('admin_auth')) {
            return redirect()->route('dashboard.index');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $admin = admin::where('email', $credentials['email'])->first();

        if (!$admin) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Email hoặc mật khẩu không chính xác.',
                ]);
        }

        $passwordMatched = false;

        try {
            $passwordMatched = Hash::check($credentials['password'], $admin->password);
        } catch (RuntimeException) {
            $passwordMatched = hash_equals((string) $admin->password, $credentials['password']);

            if ($passwordMatched) {
                $admin->password = Hash::make($credentials['password']);
                $admin->save();
            }
        }

        if (!$passwordMatched) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Email hoặc mật khẩu không chính xác.',
                ]);
        }

        $request->session()->put('admin_auth', [
            'adminID' => $admin->adminID,
            'fullName' => $admin->fullName,
            'email' => $admin->email,
            'role' => $admin->role,
        ]);

        $request->session()->regenerate();

        return redirect()
            ->route('dashboard.index')
            ->with('success', 'Đăng nhập thành công.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('admin_auth');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.login')
            ->with('success', 'Đã đăng xuất khỏi trang quản trị.');
    }
}
