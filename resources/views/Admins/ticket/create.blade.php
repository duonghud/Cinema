@extends('layouts.appAdmin')

@section('content')
<div class="container py-4">

    <div class="card  rounded-3">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Thêm vé mới</h5>
        </div>

        <div class="card-body">
            <form action="{{ route('ticket.store') }}" method="POST">
                @csrf

                {{-- PRICE --}}
                <div class="mb-3">
                    <label class="form-label">Giá vé</label>
                    <input 
                        type="number" 
                        name="price" 
                        value="{{ old('price') }}"
                        class="form-control @error('price') is-invalid @enderror"
                        placeholder="Nhập giá vé"
                    >
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- STATUS --}}
                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select 
                        name="status" 
                        class="form-control @error('status') is-invalid @enderror"
                    >
                        <option value="">-- Chọn trạng thái --</option>
                        <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>
                            Còn trống
                        </option>
                        <option value="booked" {{ old('status') == 'booked' ? 'selected' : '' }}>
                            Đã đặt
                        </option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- SHOWTIME --}}
                <div class="mb-3">
                    <label class="form-label">Suất chiếu</label>
                    <select 
                        name="showTimeID" 
                        class="form-control @error('showTimeID') is-invalid @enderror"
                    >
                        <option value="">-- Chọn suất chiếu --</option>
                        @foreach($showTimes as $s)
                            <option 
                                value="{{ $s->showTimeID }}"
                                {{ old('showTimeID') == $s->showTimeID ? 'selected' : '' }}
                            >
                                Suất chiếu #{{ $s->showTimeID }}
                            </option>
                        @endforeach
                    </select>
                    @error('showTimeID')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- SEAT --}}
                <div class="mb-3">
                    <label class="form-label">Ghế</label>
                    <select 
                        name="seatID" 
                        class="form-control @error('seatID') is-invalid @enderror"
                    >
                        <option value="">-- Chọn ghế --</option>
                        @foreach($seats as $seat)
                            <option 
                                value="{{ $seat->seatID }}"
                                {{ old('seatID') == $seat->seatID ? 'selected' : '' }}
                            >
                                Ghế {{ $seat->colSeat }} - {{ $seat->seatType->seatTypeName }}
                            </option>
                        @endforeach
                    </select>
                    @error('seatID')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- BUTTON --}}
                <div class="d-flex justify-content-between">
                    <a href="{{ route('ticket.index') }}" class="btn btn-secondary">
                        ← Quay lại
                    </a>

                    <button type="submit" class="btn btn-success">
                        💾 Lưu vé
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection