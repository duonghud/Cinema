@extends('layouts.appAdmin')

@section('content')

<div class="container mt-4">

    <!-- Header -->
    <div class="mb-3">
        <h4 class="fw-semibold">Sửa kiểm duyệt</h4>
        <small class="text-muted">Cập nhật thông tin phân loại phim</small>
    </div>

    <!-- Card -->
    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('ageRating.update', $ageRating) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Code -->
                <div class="mb-3">
                    <label class="form-label">Mã kiểm duyệt</label>
                    <input type="text"
                           name="code"
                           class="form-control @error('code') is-invalid @enderror"
                           value="{{ old('code', $ageRating->code) }}"
                           placeholder="Ví dụ: P, C13, C18">

                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description"
                              rows="3"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="Mô tả chi tiết...">{{ old('description', $ageRating->description) }}</textarea>

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
                        Cập nhật
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection