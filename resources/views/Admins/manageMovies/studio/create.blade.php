@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <!-- Card Form -->
    <div class="card shadow-sm">

        <!-- Header -->
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">Thêm nhà sản xuất mới</h5>
        </div>

        <!-- Body -->
        <div class="card-body">

            <form action="{{ route('studio.store') }}" method="post">
                @csrf

                <!-- Tên nhà sản xuất -->
                <div class="mb-3">
                    <label for="name" class="form-label fw-medium">Tên nhà sản xuất</label>
                    <input type="text" name="name" id="name"
                           placeholder="Nhập tên nhà sản xuất"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>

                    <!-- Hiển thị lỗi -->
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Submit -->
                <div class="text-end">
                    <a href="{{ route('studio.index') }}" class="btn btn-outline-dark btn-sm">Quay lại</a>
                    <button type="submit" class="btn btn-dark">
                        + Thêm nhà sản xuất
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection