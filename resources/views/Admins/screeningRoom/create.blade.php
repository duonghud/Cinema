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
                       class="form-control"
                       placeholder="Nhập tên phòng...">
            </div>

            <!-- Capacity -->
            <div class="mb-3">
                <label class="form-label text-muted">Sức chứa</label>
                <input type="number" 
                       name="capacity" 
                       class="form-control"
                       placeholder="Ví dụ: 100">
            </div>

            <!-- Screen Type -->
            <div class="mb-4">
                <label class="form-label text-muted">Loại phòng chiếu</label>
                <select name="screenTypeID" class="form-select">
                    @foreach($screenTypes as $type)
                        <option value="{{ $type->screenTypeID }}">
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
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