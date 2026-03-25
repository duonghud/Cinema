@extends('layouts.appAdmin')

@section('content')

<style>
    body {
        background: #FFFFFF;
        color: black;
    }

    h2 {
        margin-bottom: 30px;
    }

    .screen {
        background: rgba(143, 96, 182, 0.3);
        color: #fff;
        font-weight: bold;
        padding: 12px 0;
        border-radius: 8px;
        width: 80%;
        margin: 0 auto 40px;
        box-shadow: 0 0 15px rgba(143, 96, 182, 0.3);
    }

    .seat-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .seat-row {
        display: flex;
        align-items: center;
        margin: 8px 0;
    }

    .row-label {
        width: 40px;
        font-weight: bold;
        font-size: 18px;
    }

    .seat {
        width: 45px;
        height: 45px;
        margin: 5px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        position: relative;
        cursor: pointer;
        transition: 0.3s;
    }

    .seat:hover {
        transform: scale(1.1);
    }

    .normal {
        background: #444;
    }

    .vip {
        background: #ff9800;
    }

    .couple {
        background: #e91e63;
    }

    .seat-edit {
        position: absolute;
        top: -6px;
        right: -6px;
        font-size: 11px;
        background: white;
        color: black;
        border-radius: 50%;
        padding: 3px 5px;
        text-decoration: none;
    }

    .legend {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin: 20px;
    }

    .legend div {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .box {
        width: 20px;
        height: 20px;
        border-radius: 5px;
    }

    .btn-add {
        display: block;
        width: fit-content;
        margin: 20px auto;
    }
</style>

<h2 class="text-center">Bố trí ghế </h2>


<a href="{{ route('seat.create') }}" class="btn btn-primary mb-3">
    <i class="bi bi-plus-circle"></i> Thêm ghế
</a>

<div class="legend">
    <div>
        <div class="box normal"></div> Ghế Thường
    </div>
    <div>
        <div class="box vip"></div> Ghế VIP
    </div>
    <div>
        <div class="box couple"></div> Ghế Đôi
    </div>
</div>

<div class="screen">SCREEN</div>

<div class="seat-container">

    @php $currentRow = null; @endphp

    @foreach($seats as $seat)

    @if($currentRow != $seat->rowSeat)

    @if($currentRow !== null)
</div>
@endif

<div class="seat-row">
    <span class="row-label">{{ $seat->rowSeat }}</span>

    @php $currentRow = $seat->rowSeat; @endphp
    @endif

    <div class="seat
            @if($seat->seatTypeID == 6) normal
            @elseif($seat->seatTypeID == 2) vip
            @elseif($seat->seatTypeID == 5) couple
            @endif">

        {{ $seat->rowSeat }}{{ $seat->colSeat }}

        <a href="{{ route('seat.edit',$seat->seatID) }}" class="seat-edit">
            ✏
        </a>
        <form action="{{ route('seat.destroy',$seat->seatID) }}" method="POST"
            style="position:absolute; bottom:-6px; right:-6px;">
            @csrf
            @method('DELETE')
            <button type="submit"
                style="font-size:11px; background:red; color:white; border:none; border-radius:50%; padding:3px 6px;">
                🗑
            </button>
        </form>

    </div>

    @endforeach

</div>

</div>

@endsection