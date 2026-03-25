@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <!-- Header -->
    <div class="mb-3">
        <h4 class="fw-semibold">Cập nhật khách hàng</h4>
    </div>

    <!-- Error -->
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

            <form method="POST" action="{{ route('customer.update', $customers->customerID) }}">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div class="mb-3">
                    <label class="form-label">Họ tên</label>
                    <input type="text"
                           name="fullName"
                           class="form-control"
                           value="{{ old('fullName', $customers->fullName) }}">
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email"
                           name="email"
                           class="form-control"
                           value="{{ old('email', $customers->email) }}">
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label class="form-label">
                        Mật khẩu mới (bỏ trống nếu không đổi)
                    </label>
                    <input type="password"
                           name="password"
                           class="form-control">
                </div>

                <!-- Phone -->
                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text"
                           name="phoneNumber"
                           class="form-control"
                           value="{{ old('phoneNumber', $customers->phoneNumber) }}">
                </div>

                <!-- Address -->
                <div class="mb-3">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text"
                           name="address"
                           class="form-control"
                           value="{{ old('address', $customers->address) }}">
                </div>

                <!-- Actions -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-dark">
                        Cập nhật
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