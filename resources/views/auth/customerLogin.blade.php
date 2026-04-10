@extends('layouts.app')

@section('content')

<style>
    .logins-box {
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

    .btn-logins {
        background: linear-gradient(90deg, #e63636, #ff6a6a);
        border: none;
        border-radius: 50px;
        padding: 12px;
        font-weight: bold;
        color: white;
    }

    .btn-logins:hover {
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

    <div class="logins-box">

        <h4 class="mb-4 text-light text-center">Đăng nhập</h4>

        {{-- Thông báo lỗi --}}
        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('customer.login.post') }}">
            @csrf

            <!-- Email -->
            <div class="mb-3">
                <label class="text-light">Email</label>
                <input type="email"
                    name="email"
                    class="form-control"
                    placeholder="Nhập email"
                    required>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="text-light">Mật khẩu</label>
                <input type="password"
                    name="password"
                    class="form-control"
                    placeholder="Nhập mật khẩu"
                    required>
            </div>

            <!-- Button -->
            <button class="btn btn-logins w-100 mt-3">
                Đăng nhập
            </button>

            <div class="text-center mt-3 text-light">
                Chưa có tài khoản?
                <a href="{{ route('customer.register') }}">
                    Đăng ký
                </a>
            </div>

        </form>

    </div>

</div>

@endsection
