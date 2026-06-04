<style>
    body {
        color: white;
        font-family: Arial, sans-serif;
    }

    /* ── TOP BAR ── */
    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        font-size: 15px;
    }

    .timer {
        border: 1.5px solid #ef4444;
        padding: 7px 16px;
        border-radius: 10px;
        color: white;
        font-size: 14px;
        font-weight: bold;
        white-space: nowrap;
    }

    /* ── SCREEN ── */
    .screen-wrapper {
        width: 75%;
        margin: 0 auto 10px;
        text-align: center;
    }

    .screen {
        height: 64px;
        width: 100%;
        background: linear-gradient(to bottom, #fbbf24, #f59e0b);
        clip-path: ellipse(90% 100% at 50% 100%);
    }

    .screen-label {
        color: #9ca3af;
        font-size: 12px;
        letter-spacing: .1em;
        text-transform: uppercase;
        margin-top: 8px;
    }

    /* ── SEAT GRID ── */
    .seat-row {
        display: flex;
        justify-content: center;
        gap: 5px;
        margin-bottom: 5px;
    }

    .seat {
        width: 34px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: transform .15s;
        color: rgba(255, 255, 255, 0.85);
        position: relative;
    }

    .seat:hover {
        transform: scale(1.08);
    }

    .normal {
        background: #1f2937;
        border: 1px solid #374151;
    }

    .vip {
        background: #fb923c;
    }

    .couple {
        background: #ef4444;
    }

    .maintenance {
        background: #7f1d1d;
        cursor: not-allowed;
    }

    .selected {
        background: #3b82f6 !important;
    }

    /* ── Ghế đã đặt ── */
    .booked {
        background: #1a1f2a;
        border: 1px solid #2d3748;
        cursor: not-allowed;
        pointer-events: none;
        opacity: .65;
        font-size: 0;
        user-select: none;
    }

    .booked::after {
        content: "✕";
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 900;
        color: #ef4444;
    }

    /* ── LEGEND ── */
    .legend {
        display: flex;
        justify-content: center;
        gap: 24px;
        margin-top: 24px;
        font-size: 12px;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 7px;
        color: #d1d5db;
    }

    .box {
        width: 18px;
        height: 17px;
        border-radius: 4px;
        flex-shrink: 0;
        position: relative;
    }

    .box.booked {
        background: #1a1f2a;
        border: 1px solid #374151;
        opacity: .7;
        font-size: 0;
    }

    .box.booked::after {
        content: "✕";
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 900;
        color: #ef4444;
    }

    .box.selected {
        background: #3b82f6;
    }

    .box.normal {
        background: #1f2937;
        border: 1px solid #374151;
    }

    .box.vip {
        background: #fb923c;
    }

    .box.couple {
        background: #ef4444;
    }

    /* ── BOTTOM BAR ── */
    .bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 24px;
        padding-top: 18px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        font-size: 14px;
    }

    .summary {
        line-height: 1.9;
    }

    .summary .label {
        color: #9ca3af;
    }

    .summary .val {
        font-weight: 700;
        color: #fff;
    }

    .btn-row {
        display: flex;
        gap: 10px;
    }

    .btn-back {
        padding: 9px 22px;
        border: 1px solid #334155;
        border-radius: 20px;
        background: transparent;
        color: #d1d5db;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-back:hover {
        border-color: #64748b;
        color: #fff;
    }

    .btn-pay {
        padding: 9px 26px;
        border: none;
        border-radius: 20px;
        background: #7f1d1d;
        color: white;
        cursor: pointer;
        font-size: 14px;
        font-weight: bold;
        text-decoration: none;
    }

    .btn-pay:hover {
        background: #991b1b;
    }
</style>


<div class="container">

    <!-- TOP -->
    <div class="top-bar">
        <div>
            Giờ chiếu:
            <b>{{ substr($showtime->startTime,0,5) }}</b>
        </div>

        <div class="timer">
            Thời gian chọn ghế:
            <span id="timer">05:00</span>
        </div>
    </div>

    <!-- SCREEN -->
    <div class="screen-wrapper">
        <img src="{{ asset('posters/screen.webp') }}" alt="Screen">
    </div>

    <h2 class="text-center mb-5">
        Phòng chiếu {{ $showtime->room->roomName }}
    </h2>

    @php
    $groupedSeats = $seats->groupBy('rowSeat');
    @endphp

    @foreach($groupedSeats as $row => $rowSeats)
    <div class="seat-row">

        @foreach($rowSeats->sortBy('colSeat') as $seat)
        <div class="seat
            @if(in_array($seat->seatID, $bookedSeats))
                booked
            @elseif($seat->seatTypeID == 2)
                normal
            @elseif($seat->seatTypeID == 1)
                vip
            @elseif($seat->seatTypeID == 3)
                couple
            @else
                maintenance
            @endif
        "
            data-price="{{ $seat->seatType->price ?? 0 }}"
            data-seat="{{ $seat->rowSeat }}{{ $seat->colSeat }}"
            data-seat-id="{{ $seat->seatID }}">

            {{ $seat->rowSeat }}{{ $seat->colSeat }}
        </div>
        @endforeach

    </div>
    @endforeach

    <!-- LEGEND -->
    <div class="legend">

        <div class="legend-item">
            <span class="box booked"></span>
            Đã đặt
        </div>

        <div class="legend-item">
            <span class="box selected"></span>
            Đang chọn
        </div>

        <div class="legend-item">
            <span class="box normal"></span>
            Thường
        </div>

        <div class="legend-item">
            <span class="box vip"></span>
            VIP
        </div>

        <div class="legend-item">
            <span class="box couple"></span>
            Đôi
        </div>

    </div>

    <!-- BOTTOM -->
    <div class="bottom">
        <div class="summary">
            <div>
                <span class="label">Ghế đã chọn: </span>
                <span class="val" id="selectedSeats">Chưa chọn</span>
            </div>
            <div>
                <span class="label">Tổng tiền: </span>
                <span class="val" id="totalPrice">0 đ</span>
            </div>
        </div>

        <form action="{{ route('invoice.confirm') }}" method="POST">
            @csrf
            <input type="hidden" name="showtime_id" value="{{ $showtime->showTimeID }}">
            <input type="hidden" name="seats" id="seatInput">
            <div class="btn-row">
                <button type="button" class="btn-back" onclick="history.back()">Quay lại</button>
                <button type="submit" class="btn-pay">Thanh toán</button>
            </div>
        </form>
    </div>

</div>

<script>
    let selectedSeats = [];
    let totalPrice = 0;

    document.querySelectorAll('.seat:not(.booked)').forEach(seat => {
        seat.addEventListener('click', function() {
            const code = seat.textContent.trim();
            const price = parseInt(seat.dataset.price || 0);

            if (seat.classList.contains('selected')) {
                seat.classList.remove('selected');
                selectedSeats = selectedSeats.filter(s => s !== code);
                totalPrice -= price;
            } else {
                seat.classList.add('selected');
                selectedSeats.push(code);
                totalPrice += price;
            }

            document.getElementById('selectedSeats').innerText =
                selectedSeats.length ? selectedSeats.join(', ') : 'Chưa chọn';

            document.getElementById('seatInput').value = selectedSeats.join(',');

            document.getElementById('totalPrice').innerText =
                totalPrice.toLocaleString() + ' đ';
        });
    });
</script>