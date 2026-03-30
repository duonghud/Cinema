@extends('layouts.appAdmin')

@section('content')
<div class="container">
    <h4>Sửa vé</h4>

    <form action="{{ route('ticket.update', $ticket->ticketID) }}" method="POST">
        @csrf
        @method('PUT')

        <input type="number" name="price" value="{{ $ticket->price }}" class="form-control mb-2">

        <select name="status" class="form-control mb-2">
            <option value="available" {{ $ticket->status == 'available' ? 'selected' : '' }}>
                Còn trống
            </option>
            <option value="booked" {{ $ticket->status == 'booked' ? 'selected' : '' }}>
                Đã đặt
            </option>
        </select>

        <select name="showTimeID" class="form-control mb-2">
            @foreach($showTimes as $s)
                <option value="{{ $s->showTimeID }}"
                    {{ $ticket->showTimeID == $s->showTimeID ? 'selected' : '' }}>
                    Suất chiếu {{ $s->showTimeID }}
                </option>
            @endforeach
        </select>

        <select name="seatID" class="form-control mb-2">
            @foreach($seats as $seat)
                <option value="{{ $seat->seatID }}"
                    {{ $ticket->seatID == $seat->seatID ? 'selected' : '' }}>
                    Ghế {{ $seat->seatNumber }}
                </option>
            @endforeach
        </select>

        <button class="btn btn-primary">Cập nhật</button>
    </form>
</div>
@endsection