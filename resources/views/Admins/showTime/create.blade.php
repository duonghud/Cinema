@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">
    <div class="mb-3">
        <h4 class="fw-semibold">Thêm suất chiếu</h4>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('showTime.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-medium">Ngày chiếu</label>
                        <input type="date" name="showDate"
                               class="form-control @error('showDate') is-invalid @enderror"
                               value="{{ old('showDate') }}"
                               min="{{ now()->addDay()->format('Y-m-d') }}">
                        @error('showDate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-medium">Giờ bắt đầu</label>
                        <input type="time" name="startTime" id="startTime"
                               class="form-control @error('startTime') is-invalid @enderror"
                               value="{{ old('startTime') }}">
                        @error('startTime')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-medium">Giờ kết thúc</label>
                        <input type="time" name="endTime" id="endTime"
                               class="form-control @error('endTime') is-invalid @enderror"
                               value="{{ old('endTime') }}">
                        @error('endTime')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-medium">Phim</label>
                        <select name="movieID" id="movieID"
                                class="form-select @error('movieID') is-invalid @enderror">
                            <option value="">-- Chọn phim --</option>
                            @foreach($movies as $m)
                                <option value="{{ $m->movieID }}" data-duration="{{ $m->duration }}"
                                    {{ old('movieID') == $m->movieID ? 'selected' : '' }}>
                                    {{ $m->movieTitle }}{{ $m->duration ? ' (' . $m->duration . ' phút)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('movieID')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-medium">Phòng</label>
                        <select name="roomID"
                                class="form-select @error('roomID') is-invalid @enderror">
                            <option value="">-- Chọn phòng --</option>
                            @foreach($rooms as $r)
                                <option value="{{ $r->roomID }}"
                                    {{ old('roomID') == $r->roomID ? 'selected' : '' }}>
                                    {{ $r->roomName }}
                                </option>
                            @endforeach
                        </select>
                        @error('roomID')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('showTime.index') }}" class="btn btn-light me-2">
                        Quay lại
                    </a>

                    <button type="submit" class="btn btn-dark">
                        + Thêm suất chiếu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const movieSelect = document.getElementById('movieID');
        const startTimeInput = document.getElementById('startTime');
        const endTimeInput = document.getElementById('endTime');

        function updateEndTime() {
            const selectedOption = movieSelect.options[movieSelect.selectedIndex];
            const duration = parseInt(selectedOption?.dataset?.duration || '', 10);
            const startTime = startTimeInput.value;
            const cleanupMinutes = 15;

            if (!duration || !startTime) {
                return;
            }

            const [hours, minutes] = startTime.split(':').map(Number);
            const totalMinutes = (hours * 60) + minutes + duration + cleanupMinutes;
            const endHours = String(Math.floor((totalMinutes / 60) % 24)).padStart(2, '0');
            const endMinutes = String(totalMinutes % 60).padStart(2, '0');

            endTimeInput.value = `${endHours}:${endMinutes}`;
        }

        movieSelect.addEventListener('change', updateEndTime);
        startTimeInput.addEventListener('change', updateEndTime);
    });
</script>
@endsection
