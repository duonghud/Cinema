<style>
    body {
        color: white;
        font-family: Arial, sans-serif;
    }

    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        font-size: 16px;
    }

    .timer {
        border: 1px solid red;
        padding: 8px 18px;
        border-radius: 12px;
        color: white;
        font-weight: bold;
    }

    .screen-wrapper {
        width: 60%;
        margin: 20px auto 50px;
        position: relative;
    }

    .screen {
        position: relative;
        height: 70px;
        width: 100%;
        background: linear-gradient(to bottom, #fbbf24, #f59e0b);
        clip-path: ellipse(90% 100% at 50% 100%);
        overflow: hidden;
    }

    .screen::before {
        content: "";
        position: absolute;
        bottom: -30px;
        left: 0;
        width: 100%;
        height: 60px;
        background: #10141B;
        border-radius: 50% / 100%;
    }

    .seat-row {
        display: flex;
        justify-content: center;
        margin-bottom: 8px;
    }

    .seat {
        width: 36px;
        height: 36px;
        margin: 5px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        cursor: pointer;
        transition: 0.3s;
        position: relative;
    }

    .seat:hover { transform: scale(1.1); }

    .normal      { background: #1f2937; }
    .vip         { background: #fb923c; }
    .couple      { background: #ef4444; }
    .maintenance { background: #7f1d1d; }

    .selected { background: #3b82f6 !important; }

    /* ── Ghế đã đặt ── */
    .booked {
        background: #1e2533;
        border: 1px solid #374151;
        cursor: not-allowed;
        pointer-events: none;   /* chặn mọi tương tác chuột */
        opacity: .6;
        font-size: 0;           /* ẩn chữ tên ghế */
        user-select: none;
    }

    .booked::after {
        content: "✕";
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 900;
        color: #ef4444;
        line-height: 1;
    }

    .legend {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin-top: 30px;
        font-size: 14px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .box {
        width: 20px;
        height: 20px;
        border-radius: 5px;
        flex-shrink: 0;
        position: relative;
    }

    /* Legend: ghế đã đặt cũng hiển thị ✕ */
    .box.booked {
        background: #1e2533;
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
        font-size: 12px;
        font-weight: 900;
        color: #ef4444;
    }

    .box.selected  { background: #3b82f6; }
    .box.normal    { background: #1f2937; }
    .box.vip       { background: #fb923c; }
    .box.couple    { background: #ef4444; }

    .bottom {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
        font-size: 15px;
    }

    .btn-back {
        padding: 10px 20px;
        border: 1px solid #334155;
        border-radius: 20px;
        background: transparent;
        color: white;
        cursor: pointer;
    }

    .btn-pay {
        padding: 10px 30px;
        border: none;
        border-radius: 20px;
        background: #7f1d1d;
        color: white;
        cursor: pointer;
        font-weight: bold;
        text-decoration: none;
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
        <div class="screen"></div>
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

        <div>
            <p>
                Ghế:
                <span id="selectedSeats">Chưa chọn</span>
            </p>

            <p>
                Tổng tiền:
                <span id="totalPrice">0 đ</span>
            </p>
        </div>

        <form action="{{ route('invoice.confirm') }}" method="POST">

            @csrf

            <input type="hidden" name="showtime_id" value="{{ $showtime->showTimeID }}">
            <input type="hidden" name="seats" id="seatInput">

            <div class="flex gap-3">

                <button type="button" class="btn-back" onclick="history.back()">
                    Quay lại
                </button>

                <button type="submit" class="btn-pay">
                    Thanh toán
                </button>

            </div>

        </form>

    </div>

</div>

<script>
    let selectedSeats = [];
    let totalPrice = 0;

    document.querySelectorAll('.seat:not(.booked)').forEach(seat => {
        seat.addEventListener('click', function () {
            const code  = seat.textContent.trim();
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