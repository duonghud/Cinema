@extends('layouts.app')
@section('content')

<div class="text-white">

    <!-- HEADER -->
    <div class="relative z-10 px-10 py-4 flex justify-between items-center">

        <div class="absolute inset-0">
            <img src="{{ asset('posters/'.$movie->poster) }}"
                class="w-full h-full object-cover">

            <div class="absolute inset-0 
                        bg-gradient-to-r 
                        from-black 
                        via-black/90 
                        to-black/70">
            </div>
        </div>

        <!-- CONTENT -->
        <div class="relative z-10 px-10 py-4">

            <div class="w-full pt-10 pb-20">

                <div class="grid grid-cols-3 gap-10 items-center">

                    <!-- POSTER -->
                    <div>
                        <img src="{{ asset('posters/'.$movie->poster) }}"
                            class="rounded-2xl shadow-2xl">
                    </div>

                    <!-- INFO -->
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

                        @php $show = $movie->showTimes->first(); @endphp

                        <p class="text-gray-300">
                            Thời lượng:
                            @if($show)
                            {{ (strtotime($show->endTime) - strtotime($show->startTime)) / 60 }} phút
                            @else
                            Chưa có lịch chiếu
                            @endif
                        </p>

                        <p class="text-gray-400 text-sm">
                            Khởi chiếu: {{ \Carbon\Carbon::parse($movie->releaseDate)->format('d/m/Y') }}
                        </p>

                        <p class="text-gray-300 leading-relaxed max-w-2xl">
                            {{ $movie->description }}
                        </p>

                        <!-- AGE WARNING -->
                        <p class="text-red-500 text-sm">
                            Kiểm duyệt: {{ $movie->ageRating->code ?? 'T16' }} - {{ $movie->ageRating->description }}
                        </p>

                        <!-- BUTTON -->
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
    $dates = $movie->showTimes->groupBy('showDate');
    $firstDate = $dates->keys()->first();
    @endphp

    <!-- DATE TABS -->
    <div class="mt-0 flex h-[91px] justify-start sm:justify-center bg-[#1A1D23] overflow-x-auto" role="tablist">
        @foreach($dates as $date => $shows)
        @php
        $isActive = $date == $firstDate;
        $day = date('d', strtotime($date));
        $month = date('m', strtotime($date));
        $weekday = date('l', strtotime($date));
        $weekMap = [
        'Monday' => 'Thứ hai','Tuesday' => 'Thứ ba','Wednesday' => 'Thứ tư',
        'Thursday' => 'Thứ năm','Friday' => 'Thứ sáu','Saturday' => 'Thứ bảy','Sunday' => 'Chủ nhật'
        ];
        @endphp

        <button class="date-tab focus:outline-none" data-date="{{ $date }}" aria-selected="{{ $isActive ? 'true' : 'false' }}">
            <div class="w-[72px] h-full flex flex-col items-center justify-center text-xs transition-colors
                            {{ $isActive ? 'bg-red-600' : 'bg-transparent hover:bg-[#2A2F38]' }}">
                <p>Th. {{ $month }}</p>
                <p class="text-xl font-bold">{{ $day }}</p>
                <p>{{ $weekMap[$weekday] }}</p>
            </div>
        </button>
        @endforeach
    </div>


    <!-- SHOWTIMES -->
    <div class="mt-8 pb-20">
        @foreach($dates as $date => $shows)
        <div class="showtime-row {{ $date == $firstDate ? '' : 'hidden' }}" id="date-{{ $date }}">
            <div class="flex gap-6 flex-wrap">
                @foreach($shows as $show)
                <button
                    class="px-12 py-3 border border-gray-600 rounded-full hover:border-red-500 hover:text-red-400 transition showtime-btn"
                    data-url="{{ route('seat.select', $show->showTimeID) }}">
                    {{ substr($show->startTime,0,5) }}
                </button>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <!-- SEAT CONTAINER -->
    <div id="seat-container" class="mt-6"></div>

</div>

@include('layouts.trailer')

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tabs = document.querySelectorAll('.date-tab');
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                let date = this.getAttribute('data-date');
                tabs.forEach(t => {
                    t.setAttribute('aria-selected', 'false');
                    t.querySelector('div').classList.remove('bg-red-600');
                    t.querySelector('div').classList.add('bg-transparent');
                });
                this.setAttribute('aria-selected', 'true');
                this.querySelector('div').classList.remove('bg-transparent');
                this.querySelector('div').classList.add('bg-red-600');
                document.querySelectorAll('.showtime-row').forEach(row => row.classList.add('hidden'));
                document.getElementById('date-' + date).classList.remove('hidden');
            });
        });
        
        document.querySelectorAll('.showtime-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                let url = this.getAttribute('data-url');
                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('seat-container').innerHTML = html;
                    })
                    .catch(err => console.error(err));
            });
        });
    });
</script>

@endsection