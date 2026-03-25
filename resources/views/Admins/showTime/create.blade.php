@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">
    <h3>Thêm suất chiếu</h3>

    <form action="{{ route('showTime.store') }}" method="POST" class="p-4 bg-light rounded shadow-sm">
        @csrf

        <h4 class="mb-4 text-center">Thêm Lịch Chiếu</h4>

        <!-- Ngày chiếu -->
        <div class="mb-3">
            <label for="showDate" class="form-label">Ngày chiếu</label>
            <input type="date" name="showDate" id="showDate" class="form-control @error('showDate') is-invalid @enderror" value="{{ old('showDate') }}">
            @error('showDate')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Thời gian bắt đầu -->
        <div class="mb-3">
            <label for="startTime" class="form-label">Thời gian bắt đầu</label>
            <input type="time" name="startTime" id="startTime" class="form-control @error('startTime') is-invalid @enderror" value="{{ old('startTime') }}">
            @error('startTime')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Thời gian kết thúc-->
        <div class="mb-3">
            <label for="endTime" class="form-label">Thời gian kết thúc</label>
            <input type="time" name="endTime" id="endTime" class="form-control @error('endTime') is-invalid @enderror" value="{{ old('endTime') }}">
            @error('endTime')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Chọn phim -->
        <div class="mb-3">
            <label for="movieID" class="form-label">Tên phim</label>
            <select name="movieID" id="movieID" class="form-select @error('movieID') is-invalid @enderror">
                <option value="">-- Chọn phim --</option>
                @foreach($movies as $m)
                <option value="{{ $m->movieID }}" {{ old('movieID') == $m->movieID ? 'selected' : '' }}>
                    {{ $m->movieTitle }}
                </option>
                @endforeach
            </select>
            @error('movieID')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Chọn phòng -->
        <div class="mb-4">
            <label for="roomID" class="form-label">Phòng</label>
            <select name="roomID" id="roomID" class="form-select @error('roomID') is-invalid @enderror">
                <option value="">-- Chọn phòng --</option>
                @foreach($rooms as $r)
                <option value="{{ $r->roomID }}" {{ old('roomID') == $r->roomID ? 'selected' : '' }}>
                    {{ $r->roomName }}
                </option>
                @endforeach
            </select>
            @error('roomID')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="">
            <button type="submit" class="btn btn-success">Thêm Lịch Chiếu</button>
            <a href="{{ route('showTime.index') }}" class="btn btn-primary">Quay lại</a>
        </div>

        
    </form>
</div>


@endsection