@extends('layouts.appAdmin')

@section('content')
<div class="container mt-5" style="max-width: 900px;">
    <div class="bg-white p-4 rounded-3 shadow-sm border">

        <h4 class="mb-4 fw-semibold text-dark">
            Thêm phòng chiếu
        </h4>

        <form action="{{ route('screeningRoom.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Tên phòng</label>
                <input type="text"
                       name="roomName"
                       class="form-control @error('roomName') is-invalid @enderror"
                       value="{{ old('roomName') }}">

                @error('roomName')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row mb-4">
               <!--  <div class="col-md-6">
                    <label class="form-label">Số hàng ghế</label>
                    <input type="number"
                           name="rows"
                           min="1"
                           max="26"
                           class="form-control @error('rows') is-invalid @enderror"
                           value="{{ old('rows', 8) }}">

                    @error('rows')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> -->

                <div class="col-md-6">
                    <label class="form-label">Số cột ghế</label>
                    <input type="number"
                           name="cols"
                           min="1"
                           max="50"
                           class="form-control @error('cols') is-invalid @enderror"
                           value="{{ old('cols', 12) }}">

                    @error('cols')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Loại ghế</label>
                            <select name="vipSeatTypeID"
                                    class="form-select @error('vipSeatTypeID') is-invalid @enderror">
                                @foreach($seatTypes as $seatType)
                                    <option value="{{ $seatType->seatTypeID }}"
                                        {{ old('vipSeatTypeID', 1) == $seatType->seatTypeID ? 'selected' : '' }}>
                                        {{ $seatType->seatTypeName }}
                                        ({{ number_format($seatType->price) }}đ)
                                    </option>
                                @endforeach
                            </select>
                            @error('vipSeatTypeID')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Số lượng ghế</label>
                            <input type="number"
                                   name="vipSeats"
                                   min="0"
                                   class="form-control @error('vipSeats') is-invalid @enderror"
                                   value="{{ old('vipSeats', 20) }}">
                            @error('vipSeats')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Loại ghế</label>
                            <select name="normalSeatTypeID"
                                    class="form-select @error('normalSeatTypeID') is-invalid @enderror">
                                @foreach($seatTypes as $seatType)
                                    <option value="{{ $seatType->seatTypeID }}"
                                        {{ old('normalSeatTypeID', 2) == $seatType->seatTypeID ? 'selected' : '' }}>
                                        {{ $seatType->seatTypeName }}
                                        ({{ number_format($seatType->price) }}đ)
                                    </option>
                                @endforeach
                            </select>
                            @error('normalSeatTypeID')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Số lượng ghế</label>
                            <input type="number"
                                   name="normalSeats"
                                   min="0"
                                   class="form-control @error('normalSeats') is-invalid @enderror"
                                   value="{{ old('normalSeats', 60) }}">
                            @error('normalSeats')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Loại ghế</label>
                            <select name="doubleSeatTypeID"
                                    class="form-select @error('doubleSeatTypeID') is-invalid @enderror">
                                @foreach($seatTypes as $seatType)
                                    <option value="{{ $seatType->seatTypeID }}"
                                        {{ old('doubleSeatTypeID', 3) == $seatType->seatTypeID ? 'selected' : '' }}>
                                        {{ $seatType->seatTypeName }}
                                        ({{ number_format($seatType->price) }}đ)
                                    </option>
                                @endforeach
                            </select>
                            @error('doubleSeatTypeID')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Số lượng ghế</label>
                            <input type="number"
                                   name="doubleSeats"
                                   min="0"
                                   class="form-control @error('doubleSeats') is-invalid @enderror"
                                   value="{{ old('doubleSeats', 16) }}">
                            @error('doubleSeats')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

            {{-- LOẠI PHÒNG --}}
            <div class="mb-4">
                <label class="form-label">Loại phòng chiếu</label>
                <select name="screenTypeID"
                        class="form-select @error('screenTypeID') is-invalid @enderror">
                    <option value="">-- Chọn loại phòng --</option>
                    @foreach($screenTypes as $type)
                        <option value="{{ $type->screenTypeID }}"
                            {{ old('screenTypeID') == $type->screenTypeID ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>

                @error('screenTypeID')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- BUTTON --}}
            <div class="d-flex justify-content-between">
                <a href="{{ route('screeningRoom.index') }}"
                   class="btn btn-outline-secondary">
                    ← Quay lại
                </a>

                <button type="submit"
                        class="btn btn-dark px-4">
                    Lưu phòng chiếu
                </button>
            </div>

        </form>
    </div>
</div>
@endsection