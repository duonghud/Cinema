@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <!-- Header -->
    <div class="mb-3">
        <h4 class="fw-semibold">Cập nhật suất chiếu</h4>
    </div>

    <!-- Card -->
    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('showTime.update', $showTime->showTimeID) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">

                    <!-- Ngày -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-medium">Ngày chiếu</label>
                        <input type="date"
                               name="showDate"
                               class="form-control @error('showDate') is-invalid @enderror"
                               value="{{ old('showDate', $showTime->showDate) }}">
                        @error('showDate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Giờ bắt đầu -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-medium">Giờ bắt đầu</label>
                        <input type="time"
                               name="startTime"
                               id="startTime"
                               class="form-control @error('startTime') is-invalid @enderror"
                               value="{{ old('startTime', substr($showTime->startTime,0,5)) }}">
                        @error('startTime')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Giờ kết thúc -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-medium">Giờ kết thúc</label>
                        <input type="time"
                               name="endTime"
                               id="endTime"
                               class="form-control @error('endTime') is-invalid @enderror"
                               value="{{ old('endTime', substr($showTime->endTime,0,5)) }}">
                        @error('endTime')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="row">

                    <!-- Phim -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-medium">Phim</label>
                        <select name="movieID"
                                class="form-select @error('movieID') is-invalid @enderror">
                            <option value="">-- Chọn phim --</option>
                            @foreach($movies as $m)
                                <option value="{{ $m->movieID }}"
                                    {{ old('movieID', $showTime->movieID) == $m->movieID ? 'selected' : '' }}>
                                    {{ $m->movieTitle }}
                                </option>
                            @endforeach
                        </select>
                        @error('movieID')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phòng -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-medium">Phòng</label>
                        <select name="roomID"
                                class="form-select @error('roomID') is-invalid @enderror">
                            <option value="">-- Chọn phòng --</option>
                            @foreach($rooms as $r)
                                <option value="{{ $r->roomID }}"
                                    {{ old('roomID', $showTime->roomID) == $r->roomID ? 'selected' : '' }}>
                                    {{ $r->roomName }}
                                </option>
                            @endforeach
                        </select>
                        @error('roomID')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-end">
                    <a href="{{ route('showTime.index') }}" 
                       class="btn btn-light me-2">
                        Quay lại
                    </a>

                    <button type="submit" 
                            class="btn btn-dark">
                        Cập nhật
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection