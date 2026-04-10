@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <!-- Header -->
    <div class="mb-3">
        <h4 class="fw-semibold">Cập nhật nhân viên</h4>
    </div>


    <!-- Card -->
    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" action="{{ route('admin.update', $admins->adminID) }}">
                @csrf
                @method('PUT')

                <!-- Họ tên -->
                <div class="mb-3">
                    <label class="form-label">Họ tên</label>
                    <input type="text"
                           name="fullName"
                           class="form-control @error('fullName') is-invalid @enderror"
                           value="{{ old('fullName', $admins->fullName) }}">
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
                           value="{{ old('email', $admins->email) }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Role -->
                <div class="mb-3">
                    <label class="form-label">Chức vụ</label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror">
                        <option value="admin" {{ old('role', $admins->role) == 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                        <option value="ticket_staff" {{ old('role', $admins->role) == 'ticket_staff' ? 'selected' : '' }}>Nhân viên bán vé</option>
                        <option value="food_staff" {{ old('role', $admins->role) == 'food_staff' ? 'selected' : '' }}>Nhân viên bán đồ ăn</option>
                    </select>
                    @error('role')
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

                <!-- Actions -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-dark">Cập nhật</button>
                    <a href="{{ route('admin.index') }}" class="btn btn-secondary">Quay lại</a>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection