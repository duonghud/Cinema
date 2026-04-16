@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <div class="mb-3">
        <h4 class="fw-semibold">Cap nhat mon an</h4>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" action="{{ route('food.update', $foods->foodID) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Ten do an</label>
                    <input type="text"
                           name="foodName"
                           class="form-control"
                           value="{{ old('foodName', $foods->foodName) }}">

                    @error('foodName')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Gia</label>
                    <input type="number"
                           name="price"
                           class="form-control"
                           value="{{ old('price', $foods->price) }}">

                    @error('price')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="size" class="form-label">Size</label>
                    <select name="size" id="size" class="form-select">
                        <option value="">-- Chon size --</option>
                        <option value="S" {{ old('size', $foods->size) == 'S' ? 'selected' : '' }}>S</option>
                        <option value="M" {{ old('size', $foods->size) == 'M' ? 'selected' : '' }}>M</option>
                        <option value="L" {{ old('size', $foods->size) == 'L' ? 'selected' : '' }}>L</option>
                    </select>

                    @error('size')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="foodType" class="form-label">Loai</label>
                    <select name="foodType" id="foodType" class="form-select">
                        <option value="Đồ ăn" {{ old('foodType', $foods->foodType) == 'Đồ ăn' ? 'selected' : '' }}>Do an</option>
                        <option value="Đồ uống" {{ old('foodType', $foods->foodType) == 'Đồ uống' ? 'selected' : '' }}>Do uong</option>
                    </select>

                    @error('foodType')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('food.index') }}" class="btn btn-secondary me-2">Quay lai</a>
                    <button type="submit" class="btn btn-dark">Cap nhat</button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection