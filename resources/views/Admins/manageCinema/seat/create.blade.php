@extends('layouts.appAdmin')

@section('content')

<style>
body {
    background: #0b1220;
    color: white;
}

.box {
    max-width: 500px;
    margin: auto;
    background: #020617;
    padding: 30px;
    border-radius: 12px;
}
</style>

<div class="container">

    <h2 class="text-center mb-4">Thêm ghế</h2>

    <div class="box">

        @php
            $row = request('row');
            $col = request('col');
            $room = request('room');
        @endphp

        <form method="POST" action="{{ route('seat.store') }}">
            @csrf

            <div class="mb-3">
                <label>Hàng</label>
                <input type="text" name="rowSeat" value="{{ $row }}" class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label>Cột</label>
                <input type="number" name="colSeat" value="{{ $col }}" class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label>Phòng</label>
                <select name="roomID" class="form-select">
                    @foreach($rooms as $r)
                        <option value="{{ $r->roomID }}"
                            {{ $room == $r->roomID ? 'selected' : '' }}>
                            {{ $r->roomName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Loại ghế</label>
                <select name="seatTypeID" class="form-select">
                    @foreach($seatTypes as $type)
                        <option value="{{ $type->seatTypeID }}">
                            {{ $type->seatTypeName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('seat.index', ['roomID'=>$room]) }}" class="btn btn-secondary">
                    ← Quay lại
                </a>

                <button class="btn btn-success">Lưu</button>
            </div>

        </form>

    </div>

</div>

@endsection