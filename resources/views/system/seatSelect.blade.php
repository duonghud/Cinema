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

    .seat:hover {
        transform: scale(1.1);
    }

    .normal {
        background: #1f2937;
    }

    .vip {
        background: #fb923c;
    }

    .couple {
        background: #ef4444;
    }

    .maintenance {
        background: #7f1d1d;
    }

    .selected {
        background: #3b82f6 !important;
    }

    .booked {
        background: #1f2937;
        cursor: not-allowed;
    }

    .booked::after {
        content: "";
        width: 12px;
        height: 12px;
        background: #ef4444;
        border-radius: 50%;
        position: absolute;
        top: 4px;
        right: 4px;
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
    }

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
            Giờ chiếu: <b>{{ $showtime->startTime }}</b>
        </div>

        <div class="timer">
            Thời gian chọn ghế: <span id="timer"></span>
        </div>
    </div>

    <!-- SCREEN -->
    <div class="screen-wrapper">
        <div class="screen"></div>
    </div>

    <h2 class="text-center mb-4">
        Phòng chiếu số {{ $showtime->room->roomName }}
    </h2>

    <!-- SEATS -->
    @php $currentRow = null; @endphp

    @foreach($seats as $seat)

    @if($currentRow !== $seat->rowSeat)

    @if($currentRow !== null)
</div>
@endif

<div class="seat-row">
    @php $currentRow = $seat->rowSeat; @endphp

    @endif

    <div class="seat
            @if(in_array($seat->seatID,$bookedSeats))
                booked
            @elseif($seat->seatTypeID==2)
                normal
            @elseif($seat->seatTypeID==1)
                vip
            @elseif($seat->seatTypeID==3)
                couple
            @else
                maintenance
            @endif
        ">
        {{ $seat->rowSeat }}{{ $seat->colSeat }}
    </div>

    @endforeach

</div> {{-- đóng row cuối --}}

<!-- LEGEND -->
<div class="legend">
    <div class="legend-item">
        <div class="box booked"></div> Đã đặt
    </div>
    <div class="legend-item">
        <div class="box selected"></div> Ghế bạn chọn
    </div>
    <div class="legend-item">
        <div class="box normal"></div> Ghế thường
    </div>
    <div class="legend-item">
        <div class="box vip"></div> Ghế VIP
    </div>
    <div class="legend-item">
        <div class="box couple"></div> Ghế đôi
    </div>
</div>



<!-- BOTTOM -->
<div class="bottom">
    <div>
        <div style="margin-top:20px; text-align:center;">
            Ghế đã chọn: <span id="selectedSeats" style="font-weight:bold; color:#3b82f6;"></span>
        </div>
        Tổng tiền: 0đ
    </div>

    <form action="{{ route('payment') }}" method="POST">
        @csrf
        <input type="hidden" name="showtime_id" value="{{ $showtime->id }}">
        <input type="hidden" name="seats" id="seatInput">

        <div class="bottom">
            <button type="button" class="btn-back" onclick="history.back()">Quay lại</button>
            <button type="submit" class="btn-pay">Thanh toán</button>
        </div>
    </form>

</div>
</div>

<!-- JS -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const seats = document.querySelectorAll(".seat");
        const selectedSeats = [];

        seats.forEach(seat => {
            seat.addEventListener("click", function() {
                if (seat.classList.contains("booked")) return;

                const seatCode = seat.textContent.trim();

                if (seat.classList.contains("selected")) {
                    seat.classList.remove("selected");
                    const index = selectedSeats.indexOf(seatCode);
                    if (index > -1) selectedSeats.splice(index, 1);
                } else {
                    seat.classList.add("selected");
                    selectedSeats.push(seatCode);
                }

                // Hiển thị danh sách ghế đã chọn
                document.getElementById("selectedSeats").innerText = selectedSeats.join(", ");
                // Cập nhật hidden input để gửi sang backend
                document.getElementById("seatInput").value = selectedSeats.join(",");
            });
        });

        // TIMER
        let time = 300;

        let timer = setInterval(() => {
            let m = Math.floor(time / 60);
            let s = time % 60;

            if (s < 10) s = "0" + s;

            document.getElementById("timer").innerText = m + ":" + s;

            if (time <= 0) {
                clearInterval(timer);
                window.location.href = "{{ route('home') }}";
                return;
            }

            time--;
        }, 1000);

    });
</script>