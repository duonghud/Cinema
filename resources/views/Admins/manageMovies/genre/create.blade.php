@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <!-- Header -->
    <div class="mb-3">
        <h4 class="fw-semibold">Thêm thể loại</h4>
    </div>

    <!-- Card -->
    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" action="{{ route('genre.store') }}">
                @csrf

                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label fw-medium">
                        Tên thể loại
                    </label>

                    <input type="text" 
                           name="name" 
                           id="name"
                           value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="Nhập tên thể loại...">

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('genre.index') }}" 
                       class="btn btn-outline-secondary">
                        ← Quay lại
                    </a>

                    <button type="submit" 
                            class="btn btn-dark">
                        + Thêm thể loại
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection