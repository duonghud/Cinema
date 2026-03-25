@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <!-- Header -->
    <div class="mb-3">
        <h4 class="fw-semibold">Cập nhật món ăn</h4>
    </div>

    <!-- Card -->
    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" action="{{ route('food.update', $foods->foodID) }}">
                @csrf
                @method('PUT')

                <!-- Food Name -->
                <div class="mb-3">
                    <label class="form-label">Tên đồ ăn</label>
                    <input type="text" 
                           name="foodName" 
                           class="form-control"
                           value="{{ old('foodName', $foods->foodName) }}">

                    @error('foodName')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Price -->
                <div class="mb-3">
                    <label class="form-label">Giá</label>
                    <input type="number" 
                           name="price" 
                           class="form-control"
                           value="{{ old('price', $foods->price) }}">

                    @error('price')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Food Type -->
                <div class="mb-3">
                    <label for="foodType" class="form-label">Loại</label>
                    <select name="foodType" id="foodType" class="form-select">

                        <option value="Đồ ăn"
                            {{ old('foodType', $foods->foodType) == 'Đồ ăn' ? 'selected' : '' }}>
                            Đồ ăn
                        </option>

                        <option value="Đồ uống"
                            {{ old('foodType', $foods->foodType) == 'Đồ uống' ? 'selected' : '' }}>
                            Đồ uống
                        </option>

                    </select>

                    @error('foodType')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Button -->
                <div class="d-flex justify-content-end">
                    <a href="{{ route('food.index') }}" 
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