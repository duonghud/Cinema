@extends('layouts.appAdmin')

@section('content')
<div class="container mt-5" style="max-width: 600px;">

    <div class="bg-white p-4 rounded-3 shadow-sm border">

        <h4 class="mb-4 fw-semibold text-dark">
            Thêm kiểu ghế
        </h4>

        <form method="POST" action="{{ route('seatType.store') }}">
            @csrf

            <!-- Seat Type Name -->
            <div class="mb-3">
                <label for="seatTypeName" class="form-label">Tên kiểu ghế</label>
                <input type="text" 
                       name="seatTypeName" 
                       id="seatTypeName" 
                       class="form-control @error('seatTypeName') is-invalid @enderror"
                       placeholder="Nhập kiểu ghế (VIP, Thường...)"
                       value="{{ old('seatTypeName') }}">

                @error('seatTypeName')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Buttons -->
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
@endsection