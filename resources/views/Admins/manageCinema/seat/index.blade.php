@extends('layouts.appAdmin')

@section('content')

<style>

.cinema-seat {
    background: #2f2f2f;
    padding: 20px;
    border-radius: 10px;
}

.screen {
    background: linear-gradient(to right,#f5a623,#f2c94c);
    padding: 12px;
    width: 80%;
    margin: 20px auto;
    border-radius: 50px;
    text-align: center;
    font-weight: bold;
}

.seat-row {
    display: flex;
    align-items: center;
    justify-content: center;
}

.row-label {
    width: 40px;
    color: white;
}

.seat {
    width: 45px;
    height: 45px;
    margin: 5px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    position: relative;
}

.normal {
    background: #1f2937;
}

.vip {
    background: #f97316;
}

.couple {
    background: #ef4444;
}

.legend {
    display: flex;
    justify-content: center;
    gap: 20px;
    color: white;
}

.box {
    width: 20px;
    height: 20px;
}

</style>

<div class="cinema-seat">

<h3 class="text-center text-white">Bố trí ghế</h3>

<a href="{{ route('seat.create') }}" class="btn btn-primary">
    Thêm ghế
</a>

<div class="legend mt-3">

    <div>
        <div class="box normal"></div> Ghế thường
    </div>

    <div>
        <div class="box vip"></div> Ghế VIP
    </div>

    <div>
        <div class="box couple"></div> Ghế đôi
    </div>

</div>

<div class="screen">SCREEN</div>

@php $currentRow = null; @endphp

@foreach($seats as $seat)

@if($currentRow != $seat->rowSeat)

@if($currentRow !== null)
</div>
@endif

<div class="seat-row">

<span class="row-label">
    {{ $seat->rowSeat }}
</span>

@php $currentRow = $seat->rowSeat; @endphp

@endif

<div class="seat
@if($seat->seatTypeID==1) normal
@elseif($seat->seatTypeID==2) vip
@else couple
@endif">

{{ $seat->rowSeat }}{{ $seat->colSeat }}

<a href="{{ route('seat.edit',$seat->seatID) }}"
style="position:absolute;top:-5px;right:-5px">
✏
</a>

</div>

@endforeach

</div>

@endsection