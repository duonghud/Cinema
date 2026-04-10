@extends('layouts.app')

@section('content')

<style>
    .register-box {
        max-width: 600px;
        margin: auto;
        background: #020617;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 0 20px rgba(0,0,0,0.5);
    }

    .form-control {
        background: transparent;
        border: 1px solid #1e293b;
        color: #fff;
        border-radius: 10px;
        padding: 12px;
    }

    .form-control::placeholder {
        color: #64748b;
    }

    .form-control:focus {
        border-color: #ef4444;
        box-shadow: none;
    }

    .btn-registers {
        background: linear-gradient(90deg, #e63636, #ff6a6a);
        border: none;
        border-radius: 50px;
        padding: 12px;
        font-weight: bold;
        color: white;
    }

    .btn-registers:hover {
        opacity: 0.9;
    }

    .error-text {
        color: #f87171;
        font-size: 13px;
        margin-top: 5px;
    }

    a {
        color: #ef4444;
        text-decoration: none;
    }
</style>

<div class="container mt-5">
    <div class="register-box">

        <h4 class="mb-4 text-light">Đăng ký</h4>

        <form method="POST" action="{{ route('customer.register') }}">
            @csrf

            <!-- Họ + Tên -->
            <div class="row">
                <div class="mb-3">
                    <label class="text-light">Họ tên</label>
                    <input type="text" name="fullName"
                        value="{{ old('lastName') }}"
                        class="form-control @error('fullName') is-invalid @enderror"
                        placeholder="Họ Tên">

                    @error('fullName')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="text-light">Email</label>
                <input type="email" name="email"
                    value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="Email">

                @error('email')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <!-- Phone -->
            <div class="mb-3">
                <label class="text-light">Số điện thoại</label>
                <input type="text" name="phoneNumber"
                    value="{{ old('phoneNumber') }}"
                    class="form-control @error('phoneNumber') is-invalid @enderror"
                    placeholder="Số điện thoại">

                @error('phoneNumber')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="text-light">Mật khẩu</label>
                <input type="password" name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Mật khẩu">

                @error('password')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="text-light">Địa chỉ</label>
                <input type="text" name="address"
                    class="form-control @error('address') is-invalid @enderror"
                    placeholder="Địa chỉ">
                
                @error('address')
                    <div class="error-text">{{ $message }}</div>
                @enderror    

            </div>

            <!-- Button -->
            <button class="btn btn-registers w-100 mt-3">
                Đăng ký
            </button>

            <div class="text-center mt-3 text-light">
                Bạn đã có tài khoản?
                <a href="{{ route('auth.customerLogin') }}">Đăng nhập</a>
            </div>

        </form>

    </div>
</div>

@endsection