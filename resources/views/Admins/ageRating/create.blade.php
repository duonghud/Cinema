@extends('layouts.appAdmin')

@section('content')

<div class="container mt-4">

    <!-- Header -->
    <div class="mb-3">
        <h4 class="fw-semibold">Thêm kiểm duyệt độ tuổi</h4>
        <small class="text-muted">Nhập thông tin phân loại phim</small>
    </div>

    <!-- Card -->
    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('ageRating.store') }}" method="POST">
                @csrf

                <!-- Code -->
                <div class="mb-3">
                    <label class="form-label">Mã kiểm duyệt</label>
                    <input type="text"
                           name="code"
                           class="form-control @error('code') is-invalid @enderror"
                           placeholder="Ví dụ: P, C13, C18"
                           value="{{ old('code') }}">

                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description"
                              class="form-control @error('description') is-invalid @enderror"
                              rows="3"
                              placeholder="Mô tả chi tiết...">{{ old('description') }}</textarea>

                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-end">
                    <a href="{{ route('ageRating.index') }}"
                       class="btn btn-secondary me-2">
                        Quay lại
                    </a>

                    <button type="submit" class="btn btn-dark">
                        + Thêm kiểm duyệt
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection