@extends('layouts.appAdmin')
@section('content')
<div class="card mb-4">
    <div class="card-header">
        <h5>Thêm ghế mới</h5>
    </div>
    <div class="card-body">

        <!-- Hiển thị lỗi nếu có -->
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('seat.store') }}" method="POST">
            @csrf
            <div class="row">

                <!-- ROW -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Row</label>
                    <select name="rowSeat" class="form-select">
                        @foreach(range('A','z') as $row)
                        <option value="{{ $row }}">{{ $row }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- COLUMN -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Column</label>
                    <input type="number" name="colSeat" class="form-control" min="1">
                </div>

                <!-- ROOM -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Room</label>
                    <select name="roomID" class="form-select">
                        @foreach($rooms as $room)
                        <option value="{{ $room->roomID }}">{{ $room->roomName }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- SEAT TYPE -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Seat Type</label>
                    <select name="seatTypeID" class="form-select">
                        @forelse($seatTypes as $type)
                        <option value="{{ $type->seatTypeID }}">{{ $type->seatTypeName ?? $type->name }}</option>
                        @empty
                        <option disabled>Không có dữ liệu</option>
                        @endforelse
                    </select>
                </div>

            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Lưu
                </button>
                <a href="{{ route('seat.index') }}" class="btn btn-primary">Hủy</a>
            </div>

        </form>
    </div>
</div>
@endsection