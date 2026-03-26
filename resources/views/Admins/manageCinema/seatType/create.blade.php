@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <!-- Header -->
    <div class="mb-3">
        <h4 class="fw-semibold">Thêm kiểu ghế</h4>
    </div>

    <!-- Card -->
    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" action="{{ route('seatType.store') }}">
                @csrf

                <!-- Seat Type Name -->
                <div class="mb-3">
                    <label for="seatTypeName" class="form-label">Tên kiểu ghế</label>
                    <input type="text" 
                           name="seatTypeName" 
                           id="seatTypeName" 
                           class="form-control"
                           placeholder="Nhập kiểu ghế (VIP, Thường...)"
                           value="{{ old('seatTypeName') }}">

                    @error('seatTypeName')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Button -->
                <div class="d-flex justify-content-end">
                    <a href="{{ route('seatType.index') }}" 
                       class="btn btn-secondary me-2">
                        Quay lại
                    </a>

                    <button type="submit" class="btn btn-dark">
                        + Thêm kiểu ghế
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection