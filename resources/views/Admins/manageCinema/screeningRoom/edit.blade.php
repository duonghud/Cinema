<!--Đang sửa để tự động tạo vé-->
@extends('layouts.appAdmin')

@section('content')
<div class="container mt-5" style="max-width: 600px;">

    <div class="bg-white p-4 rounded-3 shadow-sm border">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-semibold text-dark mb-0">
                Chỉnh sửa phòng chiếu
            </h4>

            <a href="{{ route('screeningRoom.index') }}" 
               class="btn btn-outline-secondary btn-sm">
                ← Quay lại
            </a>
        </div>

        <!-- Alert lỗi tổng quát -->
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('screeningRoom.update', $room->roomID) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Room Name -->
            <div class="mb-3">
                <label class="form-label text-muted">Tên phòng</label>
                <input type="text" 
                       name="roomName" 
                       value="{{ old('roomName', $room->roomName) }}"
                       class="form-control @error('roomName') is-invalid @enderror"
                       placeholder="Nhập tên phòng...">
                @error('roomName')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Capacity -->
            <div class="mb-3">
                <label class="form-label text-muted">Sức chứa</label>
                <input type="number" 
                       name="capacity" 
                       value="{{ old('capacity', $room->capacity) }}"
                       class="form-control @error('capacity') is-invalid @enderror">
                @error('capacity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Screen Type -->
            <div class="mb-4">
                <label class="form-label text-muted">Loại phòng chiếu</label>
                <select name="screenTypeID" 
                        class="form-select @error('screenTypeID') is-invalid @enderror">
                    <option value="">-- Chọn loại phòng --</option>
                    @foreach($screenTypes as $type)
                        <option value="{{ $type->screenTypeID }}"
                            {{ old('screenTypeID', $room->screenTypeID) == $type->screenTypeID ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                @error('screenTypeID')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Button -->
            <div class="text-end">
                <button type="submit" class="btn btn-dark px-4">
                    Cập nhật
                </button>
            </div>

        </form>

    </div>

</div>
@endsection