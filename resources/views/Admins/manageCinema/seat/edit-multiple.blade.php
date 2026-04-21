@extends('layouts.appAdmin')

@section('content')
<div class="container">

    <h3 class="mb-4">Chỉnh sửa nhiều ghế</h3>

    <form method="POST" action="{{ route('seat.updateMultiple') }}">
        @csrf

        <input type="hidden" name="seatIDs[]" value="">
        
        @foreach($seats as $seat)
            <input type="hidden" name="seatIDs[]" value="{{ $seat->seatID }}">
        @endforeach

        <div class="mb-3">
            <label class="form-label">Chọn loại ghế</label>
            <select name="seatTypeID" class="form-select">
                @foreach($seatTypes as $type)
                    <option value="{{ $type->seatTypeID }}">
                        {{ $type->seatTypeName }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <strong>Danh sách ghế:</strong>
            <div>
                @foreach($seats as $seat)
                    <span class="badge bg-secondary me-1">
                        {{ $seat->rowSeat }}{{ $seat->colSeat }}
                    </span>
                @endforeach
            </div>
        </div>

        <button class="btn btn-success">Cập nhật</button>
        <a href="{{ route('seat.index') }}" class="btn btn-secondary">Quay lại</a>

    </form>
</div>
@endsection