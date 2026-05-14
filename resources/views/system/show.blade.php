@extends('layouts.app')
@section('content')

<div class="text-white">
    <div class="relative z-10 px-10 py-4 flex justify-between items-center">
        <div class="absolute inset-0">
            <img src="{{ asset('posters/' . $movie->poster) }}"
                class="w-full h-full object-cover">

            <div class="absolute inset-0 bg-gradient-to-r from-black via-black/90 to-black/70"></div>
        </div>



        <div class="relative z-10 px-10 py-4">
            <div class="w-full pt-10 pb-20">
                <div class="grid grid-cols-3 gap-10 items-center">
                    <div>
                        <img src="{{ asset('posters/' . $movie->poster) }}"
                            class="rounded-2xl shadow-2xl">
                    </div>

                    <div class="col-span-2 space-y-3">
                        <h1 class="text-3xl font-bold uppercase">
                            {{ $movie->movieTitle }} - {{ $movie->ageRating->code ?? 'T16' }}
                            <span class="text-sm border px-2 py-1 rounded ml-2">2D</span>
                        </h1>

                        <p class="text-gray-300">
                            {{ $movie->genres->pluck('name')->join(', ') }}
                            &nbsp;&nbsp;
                            Đạo diễn: {{ $movie->director }}
                        </p>

                        <p class="text-gray-300">
                            Thời lượng:
                            @if($movie->duration)
                            {{ $movie->duration }} phút
                            @else
                            Chưa có thông tin
                            @endif
                        </p>

                        <p class="text-gray-400 text-sm">
                            Khởi chiếu: {{ \Carbon\Carbon::parse($movie->releaseDate)->format('d/m/Y') }}
                        </p>

                        <p class="text-gray-300 leading-relaxed max-w-2xl">
                            {{ $movie->description }}
                        </p>

                        <p class="text-red-500 text-sm">
                            Kiểm duyệt: {{ $movie->ageRating->code ?? 'T16' }} - {{ $movie->ageRating->description }}
                        </p>

                        <div class="flex gap-6 pt-3">
                            <button
                                onclick="openTrailer('{{ $movie->trailer }}')"
                                class="border border-yellow-400 text-yellow-400 px-6 py-2 rounded-full hover:bg-yellow-400 hover:text-black transition">
                                Xem trailer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
    use Carbon\Carbon;
    $dates = $movie->showTimes
    ->groupBy('showDate')
    ->map(function ($shows) {
    return $shows->filter(function ($show) {
    $showDateTime = Carbon::parse(
    $show->showDate . ' ' . $show->startTime
    );

    // Chỉ hiển thị khi chưa đến giờ chiếu
    return now()->lt($showDateTime);
    });
    })
    ->filter(function ($shows) {
    // Chỉ giữ lại những ngày còn ít nhất 1 suất chiếu hợp lệ
    return $shows->count() > 0;
    });

    // Lấy ngày đầu tiên còn suất chiếu
    $firstDate = $dates->keys()->first();
    @endphp

    {{-- Nếu không còn suất chiếu nào --}}
    @if($dates->isEmpty())
    <div class="text-center py-16">
        <p class="text-gray-400 text-lg">
            Hiện tại không còn suất chiếu nào có thể đặt vé.
        </p>
    </div>
    @else
    <div class="mt-0 flex h-[91px] justify-start sm:justify-center bg-[#1A1D23] overflow-x-auto"
        role="tablist">

        @foreach($dates as $date => $shows)
        @php
        $isActive = $date == $firstDate;
        $day = date('d', strtotime($date));
        $month = date('m', strtotime($date));
        $weekday = date('l', strtotime($date));

        $weekMap = [
        'Monday' => 'Thứ hai',
        'Tuesday' => 'Thứ ba',
        'Wednesday' => 'Thứ tư',
        'Thursday' => 'Thứ năm',
        'Friday' => 'Thứ sáu',
        'Saturday' => 'Thứ bảy',
        'Sunday' => 'Chủ nhật'
        ];
        @endphp

        <button
            class="date-tab focus:outline-none"
            data-date="{{ $date }}"
            aria-selected="{{ $isActive ? 'true' : 'false' }}">

            <div
                class="w-[72px] h-full flex flex-col items-center justify-center text-xs transition-colors
                    {{ $isActive ? 'bg-red-600' : 'bg-transparent hover:bg-[#2A2F38]' }}">

                <p>Th. {{ $month }}</p>
                <p class="text-xl font-bold">{{ $day }}</p>
                <p>{{ $weekMap[$weekday] }}</p>
            </div>
        </button>
        @endforeach
    </div>

    {{-- ======================= DANH SÁCH SUẤT CHIẾU ======================= --}}
    <div class="mt-8 pb-20">
        @foreach($dates as $date => $shows)
        <div
            class="showtime-row {{ $date == $firstDate ? '' : 'hidden' }}"
            id="date-{{ $date }}">

            <div class="flex gap-6 flex-wrap">
                @foreach($shows as $show)
                <button
                    class="px-12 py-3 border border-gray-600 rounded-full
                                   hover:border-red-500 hover:text-red-400 transition
                                   showtime-btn"
                    data-url="{{ route('seat.select', $show->showTimeID) }}">

                    {{ substr($show->startTime, 0, 5) }}
                </button>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    @endif

    <div id="seat-container" class="mt-6"></div>
</div>

@include('layouts.trailer')
@endsection



<script>
    document.addEventListener("DOMContentLoaded", function() {

        const tabs = document.querySelectorAll('.date-tab');

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {

                const date = this.dataset.date;

                tabs.forEach(t => {
                    t.setAttribute('aria-selected', 'false');
                    const div = t.querySelector('div');
                    if (div) {
                        div.classList.remove('bg-red-600');
                        div.classList.add('bg-transparent');
                    }
                });

                this.setAttribute('aria-selected', 'true');
                const div = this.querySelector('div');
                if (div) {
                    div.classList.remove('bg-transparent');
                    div.classList.add('bg-red-600');
                }

                document.querySelectorAll('.showtime-row')
                    .forEach(row => row.classList.add('hidden'));

                const active = document.getElementById('date-' + date);
                if (active) active.classList.remove('hidden');
            });
        });

        // ===== CLICK SUẤT CHIẾU =====
        const showBtns = document.querySelectorAll('.showtime-btn');

        showBtns.forEach(btn => {
            btn.addEventListener('click', function() {

                const url = this.dataset.url;

                document.getElementById('seat-container').innerHTML = `
                <div style="text-align:center; padding:30px;">
                    Đang tải ghế...
                </div>
            `;

                fetch(url)
                    .then(res => res.text())
                    .then(html => {

                        document.getElementById('seat-container').innerHTML = html;

                        attachSeatEvents();
                        startSeatTimer();

                    })
                    .catch(err => {
                        console.error(err);
                        document.getElementById('seat-container').innerHTML =
                            "<p>Lỗi tải ghế</p>";
                    });
            });
        });

    });

    // ===== GLOBAL =====
    let selectedSeats = [];
    let totalPrice = 0;
    let seatTimer = null;

    function attachSeatEvents() {

        selectedSeats = [];
        totalPrice = 0;

        const seats = document.querySelectorAll(".seat");

        seats.forEach(seat => {
            seat.addEventListener("click", function() {

                if (seat.classList.contains("booked")) return;

                if (seat.classList.contains("couple")) {

                    const seatCode = seat.dataset.seat; // ví dụ A10
                    const match = seatCode.match(/^([A-Z]+)(\d+)$/);

                    if (!match) return;

                    const row = match[1];
                    const col = parseInt(match[2]);

                    const firstCol = (col % 2 === 0) ? col : col - 1;

                    const firstSeat = document.querySelector(
                        `.seat[data-seat="${row}${firstCol}"]`
                    );

                    const secondSeat = document.querySelector(
                        `.seat[data-seat="${row}${firstCol + 1}"]`
                    );

                    if (!firstSeat || !secondSeat) return;

                    if (
                        firstSeat.classList.contains("booked") ||
                        secondSeat.classList.contains("booked")
                    ) {
                        return;
                    }

                    const pairSeats = [firstSeat, secondSeat];

                    // Nếu đã chọn rồi -> bỏ chọn cả 2
                    const isSelected = pairSeats.every(s =>
                        s.classList.contains("selected")
                    );

                    if (isSelected) {
                        pairSeats.forEach(s => {
                            const code = s.dataset.seat;
                            const price = parseInt(s.dataset.price || 0);

                            s.classList.remove("selected");
                            selectedSeats = selectedSeats.filter(x => x !== code);
                            totalPrice -= price;
                        });
                    } else {
                        pairSeats.forEach(s => {
                            const code = s.dataset.seat;
                            const price = parseInt(s.dataset.price || 0);

                            if (!s.classList.contains("selected")) {
                                s.classList.add("selected");
                                selectedSeats.push(code);
                                totalPrice += price;
                            }
                        });
                    }
                } else {
                    // Ghế thường / VIP
                    const code = seat.dataset.seat;
                    const price = parseInt(seat.dataset.price || 0);

                    if (seat.classList.contains("selected")) {
                        seat.classList.remove("selected");
                        selectedSeats = selectedSeats.filter(s => s !== code);
                        totalPrice -= price;
                    } else {
                        seat.classList.add("selected");
                        selectedSeats.push(code);
                        totalPrice += price;
                    }
                }

                // Cập nhật UI
                document.getElementById("selectedSeats").innerText =
                    selectedSeats.length ?
                    selectedSeats.join(", ") :
                    "Chưa chọn";

                document.getElementById("seatInput").value =
                    selectedSeats.join(",");

                document.getElementById("totalPrice").innerText =
                    totalPrice.toLocaleString() + " đ";
            });
        });
    }

    // ===== TIMER =====
    function startSeatTimer() {

        if (seatTimer) clearInterval(seatTimer);

        let time = 300;

        seatTimer = setInterval(() => {

            let m = Math.floor(time / 60);
            let s = time % 60;

            if (s < 10) s = "0" + s;

            const timerEl = document.getElementById("timer");
            if (timerEl) timerEl.innerText = m + ":" + s;

            if (time <= 0) {
                clearInterval(seatTimer);
                window.location.href = "/";
            }

            time--;

        }, 1000);
    }
</script>
