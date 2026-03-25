@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">
    <h3>Sửa suất chiếu</h3>

    <form action="{{ route('showTime.update', $showTime->showTimeID) }}" method="POST">
        @csrf
        @method('PUT')

        <input type="date" name="showDate" value="{{ $showTime->showDate }}" class="form-control mb-2">

        <input type="time" name="startTime" value="{{ $showTime->startTime }}" class="form-control mb-2">

        <input type="time" name="endTime" value="{{ $showTime->endTime }}" class="form-control mb-2">

        <select name="movieID" class="form-control mb-2">
            @foreach($movies as $m)
                <option value="{{ $m->movieID }}" {{ $m->movieID == $showTime->movieID ? 'selected' : '' }}>
                    {{ $m->movieTitle }}
                </option>
            @endforeach
        </select>

        <select name="roomID" class="form-control mb-2">
            @foreach($rooms as $r)
                <option value="{{ $r->roomID }}" {{ $r->roomID == $showTime->roomID ? 'selected' : '' }}>
                    {{ $r->roomName }}
                </option>
            @endforeach
        </select>

        <button class="btn btn-primary">Cập nhật</button>
    </form>
</div>
@endsection