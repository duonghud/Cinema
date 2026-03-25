@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <!-- Header -->
    <div class="mb-3">
        <h4 class="fw-semibold">Thêm khách hàng</h4>
    </div>

    <!-- Alert lỗi -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Card -->
    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" action="{{ route('customer.store') }}">
                @csrf

                <!-- Name -->
                <div class="mb-3">
                    <label class="form-label">Họ tên</label>
                    <input type="text"
                           name="fullName"
                           class="form-control"
                           placeholder="Nhập họ tên"
                           value="{{ old('fullName') }}">
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email"
                           name="email"
                           class="form-control"
                           placeholder="Nhập email"
                           value="{{ old('email') }}">
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password"
                           name="password"
                           class="form-control"
                           placeholder="Nhập mật khẩu">
                </div>

                <!-- Phone -->
                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text"
                           name="phoneNumber"
                           class="form-control"
                           placeholder="Nhập số điện thoại"
                           value="{{ old('phoneNumber') }}">
                </div>

                <!-- Address -->
                <div class="mb-3">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text"
                           name="address"
                           class="form-control"
                           placeholder="Nhập địa chỉ"
                           value="{{ old('address') }}">
                </div>

                <!-- Actions -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-dark">
                        + Thêm khách hàng
                    </button>

                    <a href="{{ route('customer.index') }}" 
                       class="btn btn-secondary">
                        Quay lại
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection