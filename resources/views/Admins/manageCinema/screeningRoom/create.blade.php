<!--Đang sửa để tự động tạo vé-->
@extends('layouts.appAdmin')

@section('content')
<div class="container mt-5" style="max-width: 600px;">

    <div class="bg-white p-4 rounded-3 shadow-sm border">

        <h4 class="mb-4 fw-semibold text-dark">
            Thêm phòng chiếu
        </h4>

        <form action="{{ route('screeningRoom.store') }}" method="POST">
            @csrf

            <!-- Room Name -->
            <div class="mb-3">
                <label class="form-label text-muted">Tên phòng</label>
                <input type="text" 
                       name="roomName" 
                       class="form-control @error('roomName') is-invalid @enderror"
                       placeholder="Nhập tên phòng..."
                       value="{{ old('roomName') }}">
                @error('roomName')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Capacity -->
            <div class="mb-3">
                <label class="form-label text-muted">Sức chứa</label>
                <input type="text" 
                       name="capacity" 
                       class="form-control @error('capacity') is-invalid @enderror"
                       placeholder="Ví dụ: 100"
                       value="{{ old('capacity') }}">
                @error('capacity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Screen Type -->
            <div class="mb-4">
                <label class="form-label text-muted">Loại phòng chiếu</label>
                <select name="screenTypeID" class="form-select @error('screenTypeID') is-invalid @enderror">
                    <option value="">-- Chọn loại phòng --</option>
                    @foreach($screenTypes as $type)
                        <option value="{{ $type->screenTypeID }}" 
                            {{ old('screenTypeID') == $type->screenTypeID ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                @error('screenTypeID')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('screeningRoom.index') }}" 
                   class="btn btn-outline-secondary">
                    ← Quay lại
                </a>

                <button type="submit" 
                        class="btn btn-dark px-4">
                    Lưu
                </button>
            </div>

        </form>

    </div>

</div>
@endsection