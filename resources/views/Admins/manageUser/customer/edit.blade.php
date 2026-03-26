@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <!-- Header -->
    <div class="mb-3">
        <h4 class="fw-semibold">Cập nhật khách hàng</h4>
    </div>

    <!-- Card -->
    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" action="{{ route('customer.update', $customers->customerID) }}">
                @csrf
                @method('PUT')

                <!-- Họ tên -->
                <div class="mb-3">
                    <label class="form-label">Họ tên</label>
                    <input type="text"
                           name="fullName"
                           class="form-control @error('fullName') is-invalid @enderror"
                           value="{{ old('fullName', $customers->fullName) }}">
                    @error('fullName')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email"
                           name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $customers->email) }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label class="form-label">Mật khẩu mới (bỏ trống nếu không đổi)</label>
                    <input type="password"
                           name="password"
                           class="form-control @error('password') is-invalid @enderror">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Số điện thoại -->
                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text"
                           name="phoneNumber"
                           class="form-control @error('phoneNumber') is-invalid @enderror"
                           value="{{ old('phoneNumber', $customers->phoneNumber) }}">
                    @error('phoneNumber')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Địa chỉ -->
                <div class="mb-3">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text"
                           name="address"
                           class="form-control @error('address') is-invalid @enderror"
                           value="{{ old('address', $customers->address) }}">
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-dark">Cập nhật</button>
                    <a href="{{ route('customer.index') }}" class="btn btn-secondary">Quay lại</a>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection