@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <!-- Header -->
    <div class="mb-3">
        <h4 class="fw-semibold">Thêm phương thức thanh toán</h4>
    </div>

    <!-- Card -->
    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" action="{{ route('paymentMethod.store') }}">
                @csrf

                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">
                        Tên phương thức
                    </label>

                    <input type="text"
                           name="name"
                           id="name"
                           class="form-control"
                           placeholder="Nhập tên phương thức"
                           value="{{ old('name') }}">

                    @error('name')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-dark">
                        + Thêm
                    </button>

                    <a href="{{ route('paymentMethod.index') }}" 
                       class="btn btn-secondary">
                        Quay lại
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection