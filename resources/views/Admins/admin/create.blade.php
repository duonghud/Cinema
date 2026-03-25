@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <!-- Header -->
    <div class="mb-3">
        <h4 class="fw-semibold">Thêm nhân viên</h4>
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

            <form method="POST" action="{{ route('admin.store') }}">
                @csrf

                <!-- Họ tên -->
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

                <!-- Role -->
                <div class="mb-3">
                    <label class="form-label">Chức vụ</label>
                    <select name="role" class="form-select">
                        <option value="">-- Chọn chức vụ --</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                            Quản trị viên
                        </option>
                        <option value="ticket_staff" {{ old('role') == 'ticket_staff' ? 'selected' : '' }}>
                            Nhân viên bán vé
                        </option>
                        <option value="food_staff" {{ old('role') == 'food_staff' ? 'selected' : '' }}>
                            Nhân viên bán đồ ăn
                        </option>
                    </select>
                </div>

                <!-- Button -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-dark">
                        + Thêm nhân viên
                    </button>

                    <a href="{{ route('admin.index') }}" 
                       class="btn btn-secondary">
                        Quay lại
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection