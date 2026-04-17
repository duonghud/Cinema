@extends('layouts.app')
@section('content')
<!--Thông tin người dùng-->
<style>
    .profile-box {
        max-width: 600px;
        margin: auto;
        background: #020617;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
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

    .btn-update {
        background: linear-gradient(90deg, #22c55e, #4ade80);
        border: none;
        border-radius: 50px;
        padding: 12px;
        font-weight: bold;
        color: white;
    }

    .btn-update:hover {
        opacity: 0.9;
    }

    .error-text {
        color: #f87171;
        font-size: 13px;
        margin-top: 5px;
    }

    .success-text {
        color: #4ade80;
        font-size: 14px;
        margin-bottom: 10px;
    }
</style>

<div class="container mt-5">
    <div class="profile-box">

        <h4 class="mb-4 text-light">Thông tin cá nhân</h4>

        {{-- Thông báo thành công --}}
        @if(session('success'))
        <div class="success-text">
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('customer.profile.update') }}">
            @csrf
            @method('PUT')

            <!-- Họ tên -->
            <div class="mb-3">
                <label class="text-light">Họ tên</label>
                <input type="text" name="fullName"
                    value="{{ old('fullName', auth()->user()->fullName) }}"
                    class="form-control @error('fullName') is-invalid @enderror"
                    placeholder="Họ tên">

                @error('fullName')
                <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="text-light">Email</label>
                <input type="email" name="email"
                    value="{{ old('email', auth()->user()->email) }}"
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
                    value="{{ old('phoneNumber', auth()->user()->phoneNumber) }}"
                    class="form-control @error('phoneNumber') is-invalid @enderror"
                    placeholder="Số điện thoại">

                @error('phoneNumber')
                <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <!-- Address -->
            <div class="mb-3">
                <label class="text-light">Địa chỉ</label>
                <input type="text" name="address"
                    value="{{ old('address', auth()->user()->address) }}"
                    class="form-control @error('address') is-invalid @enderror"
                    placeholder="Địa chỉ">

                @error('address')
                <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="text-light">Mật khẩu mới (nếu đổi)</label>
                <input type="password" name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Nhập mật khẩu mới">

                @error('password')
                <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
                <label class="text-light">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation"
                    class="form-control"
                    placeholder="Nhập lại mật khẩu">
            </div>

            <button class="btn btn-update w-100 mt-3">
                Cập nhật thông tin
            </button>

        </form>

    </div>
</div>
@endsection