@extends('layouts.appAdmin')

@section('content')

<div class="container mt-5" style="max-width: 700px;">

    <div class="bg-white p-4 rounded-3 shadow-sm border">

        <h4 class="mb-4 fw-semibold text-dark">
            Thêm phòng chiếu
        </h4>

        <form action="{{ route('screeningRoom.store') }}" method="POST">
            @csrf

            <!-- TÊN PHÒNG -->
            <div class="mb-3">
                <label class="form-label text-muted">
                    Tên phòng
                </label>

                <input type="text"
                    name="roomName"
                    class="form-control @error('roomName') is-invalid @enderror"
                    value="{{ old('roomName') }}">

                @error('roomName')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <!-- GHẾ VIP -->
            <div class="mb-3">
                <div class="row">
                    <label class="form-label fw-semibold">
                        Ghế
                    </label>
                    <!-- loại ghế -->
                    <div class="col-md-6">


                        <select name="vipSeatTypeID"
                            class="form-select @error('vipSeatTypeID') is-invalid @enderror">

                            <option value="">
                                -- Chọn loại ghế --
                            </option>

                            @foreach($seatTypes as $seatType)

                            <option value="{{ $seatType->seatTypeID }}"
                                {{ old('vipSeatTypeID') == $seatType->seatTypeID ? 'selected' : '' }}>

                                {{ $seatType->seatTypeName }}
                                ({{ number_format($seatType->price) }}đ)

                            </option>

                            @endforeach

                        </select>

                        @error('vipSeatTypeID')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>


                    <!-- số lượng -->
                    <div class="col-md-6">
                        <input type="number"
                            name="vipSeats"
                            class="form-control @error('vipSeats') is-invalid @enderror"
                            placeholder="Số lượng ghế VIP"
                            value="{{ old('vipSeats',0) }}">

                        @error('vipSeats')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- GHẾ THƯỜNG -->
            <div class="mb-3">

                <label class="form-label fw-semibold">
                    Ghế
                </label>

                <div class="row">



                    <!-- loại ghế -->
                    <div class="col-md-6">

                        <select name="normalSeatTypeID"
                            class="form-select @error('normalSeatTypeID') is-invalid @enderror">

                            <option value="">
                                -- Chọn loại ghế --
                            </option>

                            @foreach($seatTypes as $seatType)

                            <option value="{{ $seatType->seatTypeID }}"
                                {{ old('normalSeatTypeID') == $seatType->seatTypeID ? 'selected' : '' }}>

                                {{ $seatType->seatTypeName }}
                                ({{ number_format($seatType->price) }}đ)

                            </option>

                            @endforeach

                        </select>

                        @error('normalSeatTypeID')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>

                    <!-- số lượng -->
                    <div class="col-md-6">

                        <input type="number"
                            name="normalSeats"
                            class="form-control @error('normalSeats') is-invalid @enderror"
                            placeholder="Số lượng ghế thường"
                            value="{{ old('normalSeats',0) }}">

                        @error('normalSeats')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>

                </div>
            </div>

            <!-- GHẾ ĐÔI -->
            <div class="mb-4">

                <label class="form-label fw-semibold">
                    Ghế
                </label>

                <div class="row">
                    <!-- loại ghế -->
                    <div class="col-md-6">

                        <select name="doubleSeatTypeID"
                            class="form-select @error('doubleSeatTypeID') is-invalid @enderror">

                            <option value="">
                                -- Chọn loại ghế --
                            </option>

                            @foreach($seatTypes as $seatType)

                            <option value="{{ $seatType->seatTypeID }}"
                                {{ old('doubleSeatTypeID') == $seatType->seatTypeID ? 'selected' : '' }}>

                                {{ $seatType->seatTypeName }}
                                ({{ number_format($seatType->price) }}đ)

                            </option>

                            @endforeach

                        </select>

                        @error('doubleSeatTypeID')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>


                    <!-- số lượng -->
                    <div class="col-md-6">

                        <input type="number"
                            name="doubleSeats"
                            class="form-control @error('doubleSeats') is-invalid @enderror"
                            placeholder="Số lượng ghế đôi"
                            value="{{ old('doubleSeats',0) }}">

                        @error('doubleSeats')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>


                </div>
            </div>

            <!-- LOẠI PHÒNG -->
            <div class="mb-4">

                <label class="form-label text-muted">
                    Loại phòng chiếu
                </label>

                <select name="screenTypeID"
                    class="form-select @error('screenTypeID') is-invalid @enderror">

                    <option value="">
                        -- Chọn loại phòng --
                    </option>

                    @foreach($screenTypes as $type)

                    <option value="{{ $type->screenTypeID }}"
                        {{ old('screenTypeID') == $type->screenTypeID ? 'selected' : '' }}>

                        {{ $type->name }}

                    </option>

                    @endforeach

                </select>

                @error('screenTypeID')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror

            </div>

            <!-- BUTTON -->
            <div class="d-flex justify-content-between">

                <a href="{{ route('screeningRoom.index') }}"
                    class="btn btn-outline-secondary">

                    ← Quay lại

                </a>

                <button type="submit"
                    class="btn btn-dark px-4">

                    Lưu

                </button>

            </div>

        </form>

    </div>

</div>

@endsection