@extends('layouts.appAdmin')

@section('content')
<div class="container py-4">

    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4">
            <h4 class="mb-0">
                <i class="bi bi-ticket-perforated"></i> Thêm vé mới
            </h4>
        </div>

        <div class="card-body p-4">

            <form action="{{ route('ticket.store') }}" method="POST">
                @csrf

                {{-- Giá --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Giá vé</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-cash"></i>
                        </span>
                        <input type="number" name="price" class="form-control"
                               placeholder="Nhập giá vé">
                    </div>
                </div>

                {{-- Trạng thái --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">-- Chọn trạng thái --</option>
                        <option value="available">Còn trống</option>
                        <option value="booked">Đã đặt</option>
                    </select>
                </div>

                {{-- Suất chiếu --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Suất chiếu</label>
                    <select name="showTimeID" class="form-select">
                        @foreach($showTimes as $st)
                            <option value="{{ $st->showTimeID }}">
                                Suất #{{ $st->showTimeID }} 
                                ({{ $st->showDate }} - {{ $st->startTime }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Ghế --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Ghế</label>
                    <select name="seatID" class="form-select">
                        @foreach($seats as $seat)
                            <option value="{{ $seat->seatID }}">
                                Ghế #{{ $seat->seatID }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Button --}}
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('ticket.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>

                    <button class="btn btn-success px-4">
                        <i class="bi bi-check-circle"></i> Lưu vé
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection