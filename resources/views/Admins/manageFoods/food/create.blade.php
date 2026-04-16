@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <div class="mb-3">
        <h4 class="fw-semibold">Thêm món ăn</h4>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" action="{{ route('food.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="foodName" class="form-label">Tên đồ ăn</label>
                    <input type="text"
                           name="foodName"
                           id="foodName"
                           class="form-control"
                           placeholder="Nhập tên đồ ăn"
                           value="{{ old('foodName') }}">

                    @error('foodName')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Giá</label>
                    <input type="number"
                           name="price"
                           id="price"
                           class="form-control"
                           placeholder="Nhập giá"
                           value="{{ old('price') }}">

                    @error('price')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="size" class="form-label">Size</label>
                    <select name="size" id="size" class="form-select">
                        <option value="">-- Chọn size --</option>
                        <option value="S" {{ old('size') == 'S' ? 'selected' : '' }}>S</option>
                        <option value="M" {{ old('size') == 'M' ? 'selected' : '' }}>M</option>
                        <option value="L" {{ old('size') == 'L' ? 'selected' : '' }}>L</option>
                    </select>

                    @error('size')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="foodType" class="form-label">Loại</label>
                    <select name="foodType" id="foodType" class="form-select">
                        <option value="">-- Chọn loại --</option>
                        <option value="Đồ ăn" {{ old('foodType') == 'Đồ ăn' ? 'selected' : '' }}>Đồ ăn</option>
                        <option value="Đồ uống" {{ old('foodType') == 'Đồ uống' ? 'selected' : '' }}>Đồ uống</option>
                    </select>

                    @error('foodType')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('food.index') }}" class="btn btn-secondary me-2">Quay lại</a>
                    <button type="submit" class="btn btn-dark">+ Thêm món ăn</button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
