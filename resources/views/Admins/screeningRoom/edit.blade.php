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

        <!-- Error -->
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
                       value="{{ $room->roomName }}"
                       class="form-control"
                       placeholder="Nhập tên phòng...">
            </div>

            <!-- Capacity -->
            <div class="mb-3">
                <label class="form-label text-muted">Sức chứa</label>
                <input type="number" 
                       name="capacity" 
                       value="{{ $room->capacity }}"
                       class="form-control">
            </div>

            <!-- Screen Type -->
            <div class="mb-4">
                <label class="form-label text-muted">Loại phòng chiếu</label>
                <select name="screenTypeID" class="form-select">
                    @foreach($screenTypes as $type)
                        <option value="{{ $type->screenTypeID }}"
                            {{ $type->screenTypeID == $room->screenTypeID ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
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