@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <div class="mb-3">
        <h4 class="fw-semibold">Them mon an</h4>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" action="{{ route('food.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="foodName" class="form-label">Ten do an</label>
                    <input type="text"
                           name="foodName"
                           id="foodName"
                           class="form-control"
                           placeholder="Nhap ten do an"
                           value="{{ old('foodName') }}">

                    @error('foodName')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Gia</label>
                    <input type="number"
                           name="price"
                           id="price"
                           class="form-control"
                           placeholder="Nhap gia"
                           value="{{ old('price') }}">

                    @error('price')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="size" class="form-label">Size</label>
                    <select name="size" id="size" class="form-select">
                        <option value="">-- Chon size --</option>
                        <option value="S" {{ old('size') == 'S' ? 'selected' : '' }}>S</option>
                        <option value="M" {{ old('size') == 'M' ? 'selected' : '' }}>M</option>
                        <option value="L" {{ old('size') == 'L' ? 'selected' : '' }}>L</option>
                    </select>

                    @error('size')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="foodType" class="form-label">Loai</label>
                    <select name="foodType" id="foodType" class="form-select">
                        <option value="">-- Chon loai --</option>
                        <option value="Đồ ăn" {{ old('foodType') == 'Đồ ăn' ? 'selected' : '' }}>Do an</option>
                        <option value="Đồ uống" {{ old('foodType') == 'Đồ uống' ? 'selected' : '' }}>Do uong</option>
                    </select>

                    @error('foodType')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('food.index') }}" class="btn btn-secondary me-2">Quay lai</a>
                    <button type="submit" class="btn btn-dark">+ Them mon an</button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection