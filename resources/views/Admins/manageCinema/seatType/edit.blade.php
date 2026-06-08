@extends('layouts.appAdmin')

@section('content')
<div class="container mt-5" style="max-width: 600px;">

    <div class="bg-white p-4 rounded-3 shadow-sm border">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-semibold text-dark mb-0">
                Cập nhật kiểu ghế
            </h4>
        </div>

        <form method="POST" action="{{ route('seatType.update', $seatTypes->seatTypeID) }}">
            @csrf
            @method('PUT')

            <!-- Seat Type Name -->
            <div class="mb-3">
                <label for="seatTypeName" class="form-label">Tên kiểu ghế</label>
                <input type="text" 
                       name="seatTypeName" 
                       id="seatTypeName" 
                       class="form-control @error('seatTypeName') is-invalid @enderror"
                       value="{{ old('seatTypeName', $seatTypes->seatTypeName) }}"
                       placeholder="Nhập kiểu ghế (VIP, Thường...)">

                @error('seatTypeName')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Price -->
            <div class="mb-3">
                <label for="price" class="form-label">Giá</label>
                <input type="text" 
                       name="price" 
                       id="price" 
                       class="form-control @error('price') is-invalid @enderror"
                       value="{{ old('price', $seatTypes->price) }}"
                       placeholder="Nhập giá"
                       min="0"
                       max="500000">

                @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
                       class="form-control @error('seatTypeName') is-invalid @enderror"
                       value="{{ old('seatTypeName', $seatTypes->seatTypeName) }}"
                       placeholder="Nhập kiểu ghế (VIP, Thường...)">

                @error('seatTypeName')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Button -->
            <div class="d-flex justify-content-end">
                <a href="{{ route('seatType.index') }}" 
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
@endsection