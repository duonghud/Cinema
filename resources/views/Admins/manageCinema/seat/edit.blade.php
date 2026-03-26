@extends('layouts.appAdmin')

@section('content')

<div class="container mt-4">
    <div class="card shadow p-4">
        <h3 class="mb-3">✏ Edit Seat</h3>

        <form action="{{ route('seat.update',$seat->seatID) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Row</label>
                <input type="text" name="rowSeat" class="form-control"
                       value="{{ $seat->rowSeat }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Column</label>
                <input type="number" name="colSeat" class="form-control"
                       value="{{ $seat->colSeat }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Room</label>
                <select name="roomID" class="form-select">
                    @foreach($rooms as $room)
                        <option value="{{ $room->roomID }}"
                            {{ $room->roomID == $seat->roomID ? 'selected' : '' }}>
                            {{ $room->roomName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Seat Type</label>

                <select name="seatTypeID" class="form-select" style="color:black;">

                    @forelse($seatTypes as $type)
                    <option value="{{ $type->seatTypeID }}">
                        {{ $type->seatTypeName ?? $type->name }}
                    </option>
                    @empty
                    <option disabled>Không có dữ liệu</option>
                    @endforelse

                </select>
            </div>

            <button class="btn btn-primary">Update</button>
            <a href="{{ route('seat.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>

@endsection