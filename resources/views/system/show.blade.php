@extends('layouts.app')
@section('content')
@php
use Carbon\Carbon;

$dates = $movie->showTimes
    ->groupBy('showDate')
    ->map(function ($shows) {
        return $shows->filter(function ($show) {
            $showDateTime = Carbon::parse($show->showDate . ' ' . $show->startTime);
            return now()->lt($showDateTime);
        })->values();
    })
    ->filter(function ($shows) {
        return $shows->count() > 0;
    });

$firstDate = $selectedShowTime && $dates->has($selectedShowTime->showDate)
    ? $selectedShowTime->showDate
    : $dates->keys()->first();

$selectedShowTimeId = $selectedShowTime?->showTimeID;
@endphp

<style>
    .alert-error {
        width: 60%;
        margin: 15px auto;
        padding: 12px 18px;
        border-radius: 10px;
        background: rgba(239, 68, 68, 0.15);
        border: 1px solid #ef4444;
        color: #fca5a5;
        font-weight: 500;
        text-align: center;
    }

    .alert-success {
        width: 60%;
        margin: 15px auto;
        padding: 12px 18px;
        border-radius: 10px;
        background: rgba(34, 197, 94, 0.15);
        border: 1px solid #22c55e;
        color: #86efac;
        font-weight: 500;
        text-align: center;
    }

    .showtime-btn.is-active {
        border-color: #ef4444;
        color: #fca5a5;
        background: rgba(239, 68, 68, 0.12);
    }

    /* ── Căn giữa toàn bộ khu vực showtime + seat ── */
    .booking-wrapper {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 24px;
    }

    /* Showtime buttons row căn giữa */
    .showtime-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: center;
    }

    /* ── Realtime: ghế vừa bị lock bởi người khác ── */
    .seat.locked-by-other {
        background: #f59e0b !important;
        border-color: #f59e0b !important;
        cursor: not-allowed !important;
        opacity: 0.7;
    }

    /* Notification toast */
    #seat-notify {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        background: rgba(239, 68, 68, 0.92);
        color: #fff;
        padding: 12px 20px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
        display: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.4);
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from { transform: translateX(60px); opacity: 0; }
        to   { transform: translateX(0);    opacity: 1; }
    }
</style>

<!-- Toast thông báo ghế bị người khác đặt -->
<div id="seat-notify"></div>

@if(session('error'))
<div class="alert-error">{{ session('error') }}</div>
@endif

@if(session('success'))
<div class="alert-success">{{ session('success') }}</div>
@endif

<div class="text-white">

    {{-- ── Banner phim ── --}}
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

    {{-- ── Tabs ngày ── --}}
    @if($dates->isEmpty())
    <div class="text-center py-16">
        <p class="text-gray-400 text-lg">Hiện tại không còn suất chiếu nào có thể đặt vé.</p>
    </div>
    @else

    {{-- Tab chọn ngày --}}
    <div class="mt-0 flex h-[91px] justify-start sm:justify-center bg-[#1A1D23] overflow-x-auto" role="tablist">
        @foreach($dates as $date => $shows)
        @php
            $isActive = $date == $firstDate;
            $day      = date('d', strtotime($date));
            $month    = date('m', strtotime($date));
            $weekday  = date('l', strtotime($date));
            $weekMap  = [
                'Monday'    => 'Thứ hai',
                'Tuesday'   => 'Thứ ba',
                'Wednesday' => 'Thứ tư',
                'Thursday'  => 'Thứ năm',
                'Friday'    => 'Thứ sáu',
                'Saturday'  => 'Thứ bảy',
                'Sunday'    => 'Chủ nhật',
            ];
        @endphp
        <button class="date-tab focus:outline-none"
                data-date="{{ $date }}"
                aria-selected="{{ $isActive ? 'true' : 'false' }}">
            <div class="w-[72px] h-full flex flex-col items-center justify-center text-xs transition-colors
                         {{ $isActive ? 'bg-red-600' : 'bg-transparent hover:bg-[#2A2F38]' }}">
                <p>Th. {{ $month }}</p>
                <p class="text-xl font-bold">{{ $day }}</p>
                <p>{{ $weekMap[$weekday] }}</p>
            </div>
        </button>
        @endforeach
    </div>

    {{-- ── Giờ chiếu + sơ đồ ghế (căn giữa) ── --}}
    <div class="booking-wrapper pt-3">

        {{-- Giờ chiếu --}}
        @foreach($dates as $date => $shows)
        <div class="showtime-panel {{ $date == $firstDate ? '' : 'hidden' }}" id="date-{{ $date }}">
            <div class="showtime-row">
                @foreach($shows as $show)
                <button
                    class="px-10 py-2.5 border border-gray-600 rounded-full
                           hover:border-red-500 hover:text-red-400 transition showtime-btn
                           {{ $selectedShowTimeId === $show->showTimeID ? 'is-active' : '' }}"
                    data-showtime-id="{{ $show->showTimeID }}"
                    data-url="{{ route('seat.select', $show->showTimeID) }}">
                    {{ substr($show->startTime, 0, 5) }}
                </button>
                @endforeach
            </div>
        </div>
        @endforeach

        {{-- Sơ đồ ghế --}}
        <div id="seat-container" class="mt-8"></div>

    </div>{{-- /booking-wrapper --}}
    @endif

</div>

@include('layouts.trailer')
@endsection

<script>
document.addEventListener("DOMContentLoaded", function () {

    /* ════════════════════════════════════
       TABS NGÀY
    ════════════════════════════════════ */
    const tabs = document.querySelectorAll('.date-tab');

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            const date = this.dataset.date;

            // Reset tất cả tabs
            tabs.forEach(t => {
                t.setAttribute('aria-selected', 'false');
                const d = t.querySelector('div');
                if (d) { d.classList.remove('bg-red-600'); d.classList.add('bg-transparent'); }
            });

            // Active tab được chọn
            this.setAttribute('aria-selected', 'true');
            const d = this.querySelector('div');
            if (d) { d.classList.remove('bg-transparent'); d.classList.add('bg-red-600'); }

            // Ẩn/hiện showtime panel
            document.querySelectorAll('.showtime-panel').forEach(p => p.classList.add('hidden'));
            const panel = document.getElementById('date-' + date);
            if (panel) panel.classList.remove('hidden');

            // Reset seat container
            document.getElementById('seat-container').innerHTML = '';
            stopRealtimePolling();
        });
    });

    /* ════════════════════════════════════
       CHỌN GIỜ CHIẾU → TẢI SƠ ĐỒ GHẾ
    ════════════════════════════════════ */
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.showtime-btn');
        if (!btn) return;

        const url         = btn.dataset.url;
        const showtimeId  = btn.dataset.showtimeId;

        document.querySelectorAll('.showtime-btn').forEach(b => b.classList.remove('is-active'));
        btn.classList.add('is-active');

        document.getElementById('seat-container').innerHTML =
            '<div style="text-align:center;padding:40px;color:#9ca3af;">Đang tải sơ đồ ghế...</div>';

        fetch(url)
            .then(res => res.text())
            .then(html => {
                document.getElementById('seat-container').innerHTML = html;
                attachSeatEvents();
                startSeatTimer();
                startRealtimePolling(showtimeId);   // ← BẮT ĐẦU POLLING

                const nextUrl = new URL(window.location.href);
                nextUrl.searchParams.set('showtime', showtimeId);
                window.history.replaceState({}, '', nextUrl.toString());
            })
            .catch(err => {
                console.error(err);
                document.getElementById('seat-container').innerHTML =
                    '<p style="text-align:center;color:#ef4444;">Lỗi tải sơ đồ ghế.</p>';
            });
    });

    // Auto-click suất chiếu đang được chọn (nếu có)
    const selectedShowtimeId = @json($selectedShowTimeId);
    if (selectedShowtimeId) {
        const btn = document.querySelector(`.showtime-btn[data-showtime-id="${selectedShowtimeId}"]`);
        if (btn) btn.click();
    }
});


/* ════════════════════════════════════════════════════
   REALTIME POLLING — kiểm tra ghế đã được đặt bởi người khác
   Mỗi 5 giây gọi: GET /showtime/{id}/seat-status
   Backend trả về: { bookedSeats: ["A1","A2",...] }
════════════════════════════════════════════════════ */
let realtimeInterval = null;
let currentShowtimeId = null;

function startRealtimePolling(showtimeId) {
    stopRealtimePolling();
    currentShowtimeId = showtimeId;

    realtimeInterval = setInterval(() => {
        fetch(`/showtime/${showtimeId}/seat-status`)
            .then(res => res.json())
            .then(data => {
                const booked = data.bookedSeats || [];
                updateSeatAvailability(booked);
            })
            .catch(() => {}); // Im lặng nếu network lỗi
    }, 5000); // 5 giây / lần
}

function stopRealtimePolling() {
    if (realtimeInterval) {
        clearInterval(realtimeInterval);
        realtimeInterval = null;
    }
}

function updateSeatAvailability(bookedSeats) {
    const seats = document.querySelectorAll('.seat');
    let deselectedCount = 0;

    seats.forEach(seat => {
        const code = seat.dataset.seat;
        if (!code) return;

        const isNowBooked = bookedSeats.includes(code);

        if (isNowBooked && !seat.classList.contains('booked')) {
            // Ghế vừa bị người khác đặt
            seat.classList.add('booked');

            // Nếu người dùng đang chọn ghế này → bỏ chọn
            if (seat.classList.contains('selected')) {
                seat.classList.remove('selected');
                const price = parseInt(seat.dataset.price || 0);
                selectedSeats = selectedSeats.filter(s => s !== code);
                totalPrice -= price;
                deselectedCount++;
            }
        }
    });

    // Cập nhật UI tổng tiền / danh sách ghế
    if (deselectedCount > 0) {
        refreshBookingSummary();
        showSeatNotify(`${deselectedCount} ghế bạn đang chọn vừa được người khác đặt!`);
    }
}

function showSeatNotify(msg) {
    const el = document.getElementById('seat-notify');
    if (!el) return;
    el.textContent = msg;
    el.style.display = 'block';
    setTimeout(() => { el.style.display = 'none'; }, 4000);
}

function refreshBookingSummary() {
    const selectedSeatsEl = document.getElementById('selectedSeats');
    const seatInput       = document.getElementById('seatInput');
    const totalPriceEl    = document.getElementById('totalPrice');

    if (selectedSeatsEl)
        selectedSeatsEl.innerText = selectedSeats.length ? selectedSeats.join(', ') : 'Chưa chọn';
    if (seatInput)
        seatInput.value = selectedSeats.join(',');
    if (totalPriceEl)
        totalPriceEl.innerText = totalPrice.toLocaleString() + ' đ';
}


/* ════════════════════════════════════
   GHẾ
════════════════════════════════════ */
let selectedSeats = [];
let totalPrice    = 0;
let seatTimer     = null;

function attachSeatEvents() {
    selectedSeats = [];
    totalPrice    = 0;

    document.querySelectorAll('.seat').forEach(seat => {
        seat.addEventListener('click', function () {
            if (seat.classList.contains('booked')) return;

            /* ── GHẾ ĐÔI ── */
            if (seat.classList.contains('couple')) {
                const row = seat.dataset.seat.match(/[A-Z]+/)[0];
                const rowCoupleSeats = [...document.querySelectorAll(`.seat.couple[data-seat^="${row}"]`)]
                    .sort((a, b) => parseInt(a.dataset.seat.match(/\d+/)[0]) - parseInt(b.dataset.seat.match(/\d+/)[0]));

                const idx = rowCoupleSeats.indexOf(seat);
                if (idx === -1) return;

                const pairSeats = idx % 2 === 0
                    ? [rowCoupleSeats[idx], rowCoupleSeats[idx + 1]]
                    : [rowCoupleSeats[idx - 1], rowCoupleSeats[idx]];

                if (pairSeats.length !== 2 || pairSeats.some(s => !s)) return;
                if (pairSeats.some(s => s.classList.contains('booked'))) return;

                const isSelected = pairSeats.every(s => s.classList.contains('selected'));
                pairSeats.forEach(s => {
                    const code  = s.dataset.seat;
                    const price = parseInt(s.dataset.price || 0);
                    if (isSelected) {
                        s.classList.remove('selected');
                        selectedSeats = selectedSeats.filter(i => i !== code);
                        totalPrice -= price;
                    } else {
                        if (!s.classList.contains('selected')) {
                            s.classList.add('selected');
                            if (!selectedSeats.includes(code)) {
                                selectedSeats.push(code);
                                totalPrice += price;
                            }
                        }
                    }
                });

            /* ── GHẾ THƯỜNG / VIP ── */
            } else {
                const code  = seat.dataset.seat;
                const price = parseInt(seat.dataset.price || 0);

                if (seat.classList.contains('selected')) {
                    seat.classList.remove('selected');
                    selectedSeats = selectedSeats.filter(i => i !== code);
                    totalPrice -= price;
                } else {
                    seat.classList.add('selected');
                    if (!selectedSeats.includes(code)) {
                        selectedSeats.push(code);
                        totalPrice += price;
                    }
                }
            }

            refreshBookingSummary();
        });
    });
}


/* ════════════════════════════════════
   ĐẾM NGƯỢC 5 PHÚT
════════════════════════════════════ */
function startSeatTimer() {
    if (seatTimer) clearInterval(seatTimer);
    let time = 300;

    seatTimer = setInterval(() => {
        const m = Math.floor(time / 60);
        let   s = time % 60;
        if (s < 10) s = '0' + s;

        const timerEl = document.getElementById('timer');
        if (timerEl) timerEl.innerText = m + ':' + s;

        if (time <= 0) {
            clearInterval(seatTimer);
            stopRealtimePolling();
            window.location.href = '/';
        }
        time--;
    }, 1000);
}
</script>