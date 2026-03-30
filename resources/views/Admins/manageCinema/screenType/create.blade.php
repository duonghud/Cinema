@extends('layouts.appAdmin')

@section('content')
<div class="container mt-5" style="max-width: 600px;">

    <div class="bg-white p-4 rounded-3 shadow-sm border">

        <h4 class="mb-4 fw-semibold text-dark">
            Thêm định dạng màn hình
        </h4>

        <form method="POST" action="{{ route('screenType.store') }}">
            @csrf

            <!-- Name -->
            <div class="mb-3">
                <label for="name" class="form-label">Tên định dạng</label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       class="form-control @error('name') is-invalid @enderror"
                       placeholder="Nhập tên định dạng"
                       value="{{ old('name') }}">

                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-end">
                <a href="{{ route('screenType.index') }}" 
                   class="btn btn-secondary me-2">
                    Quay lại
                </a>

                <button type="submit" class="btn btn-dark">
                    + Thêm định dạng
                </button>
            </div>

        </form>

    </div>

</div>
@endsection