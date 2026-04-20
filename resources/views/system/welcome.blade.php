<style>
    .btn-buy-ticket {
        display: inline-flex;
        align-items: center;
        padding: 8px 24px;

        background: linear-gradient(to right, #f55454, #ec4899);
        color: white;
        border-radius: 8px;

        text-decoration: none;

        transition: all 0.3s ease;
        transform: translateY(0) scale(1);

        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-buy-ticket:hover {
        transform: translateY(-4px) scale(1.05);
        box-shadow: 0 10px 25px rgba(255, 0, 0, 0.6);
    }

    .btn-buy-ticket .icon {
        width: 20px;
        height: 20px;
        margin-right: 8px;
    }
</style>

@extends('layouts.app')
@section('content')

<!-- HERO MOVIE SLIDER -->
<div class="relative w-full h-[520px] overflow-hidden">

    @foreach($banners as $index => $movie)
    <div class="slide absolute inset-0 transition-all duration-700 
        {{ $index == 0 ? 'opacity-100 scale-100' : 'opacity-0 scale-105' }}">

        <!-- Background -->
        <div class="w-full h-full">

            @if($movie->trailer)
            <video
                class="w-full h-full object-cover"
                autoplay
                muted
                loop
                playsinline>

                <source src="{{ asset($movie->trailer) }}" type="video/mp4">
            </video>
            @else
            <img src="{{ asset('posters/'.$movie->poster) }}"
                class="w-full h-full object-cover">
            @endif

        </div>

        <!-- Overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-black via-black/70 to-transparent"></div>

        <!-- Content -->
        <div class="absolute left-20 bottom-20 text-white max-w-2xl">

            <h1 class="text-4xl md:text-5xl font-extrabold mb-4 uppercase">
                {{ $movie->movieTitle }}
            </h1>

            <div class="flex flex-wrap gap-4 text-gray-300 text-sm mb-3">
                <span>{{ $movie->genres->pluck('name')->join(', ') }}</span>

                @php $show = $movie->showTimes->first(); @endphp
                <p>
                    Thời lượng:
                    @if($show)
                    {{ (strtotime($show->endTime) - strtotime($show->startTime)) / 60 }} phút
                    @else
                    Chưa có lịch
                    @endif
                </p>

                <p>Đạo diễn: {{ $movie->director }}</p>
            </div>

            <p class="text-gray-300 mb-4 line-clamp-3">
                {{ $movie->description ?? 'Đang chiếu tại rạp' }}
            </p>

            <p class="text-red-500 text-sm mb-3">
                Kiểm duyệt: {{ $movie->ageRating->description }}
            </p>

            <p class="text-gray-300 mb-2">
                Khởi chiếu: {{ $movie->releaseDate->format('d/m/Y') }}
            </p>

            <a href="{{ route('movies.show', $movie) }}"
                class="btn-buy-ticket">

                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5 mr-2"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                </svg>

                <span>Mua vé ngay</span>
            </a>
        </div>

    </div>
    @endforeach

    <!-- Buttons -->
    <button onclick="prevSlide()" class="absolute left-5 top-1/2 text-white text-4xl">‹</button>
    <button onclick="nextSlide()" class="absolute right-5 top-1/2 text-white text-4xl">›</button>

</div>

<!-- MAIN -->
<div class="min-h-screen py-10">
    <div class="container mx-auto px-10 max-w-7xl">

        <!-- PHIM ĐANG CHIẾU -->
        <div class="flex items-center gap-2 mb-6">
            <div class="w-4 h-4 bg-red-500 rounded-full"></div>
            <h3 class="text-2xl font-bold text-white">Phim đang chiếu</h3>
        </div>

        <div class="grid grid-cols-4 gap-8">
            @foreach($nowShowing as $movie)

            <div class="group bg-[#10141B]/60 rounded-xl overflow-hidden hover:shadow-2xl transition">

                <a href="{{ route('movies.show', $movie) }}">
                    <img src="{{ asset('posters/'.$movie->poster) }}"
                        class="w-full h-80 object-cover group-hover:scale-105 transition">
                </a>

                <div class="p-4">
                    <p class="text-gray-400 text-sm">
                        {{ $movie->genres->pluck('name')->join(', ') }} |
                        {{ $movie->releaseDate->format('d/m/Y') }}
                    </p>

                    <h2 class="text-white font-semibold mt-2 group-hover:text-red-400">
                        {{ $movie->movieTitle }}
                    </h2>
                </div>

            </div>

            @endforeach
        </div>

        <!-- PHIM SẮP CHIẾU -->
        <div class="flex items-center gap-2 mt-12 mb-6">
            <div class="w-4 h-4 bg-red-500 rounded-full"></div>
            <h3 class="text-2xl font-bold text-white">Phim sắp chiếu</h3>
        </div>

        <div class="grid grid-cols-4 gap-8">
            @foreach($comingSoon as $movie)

            <div class="group bg-[#10141B]/60 rounded-xl overflow-hidden hover:shadow-2xl transition">

                <a href="{{ route('movies.show', $movie) }}">
                    <img src="{{ asset('posters/'.$movie->poster) }}"
                        class="w-full h-80 object-cover group-hover:scale-105 transition">
                </a>


                <div class="p-4">
                    <p class="text-gray-400 text-sm">
                        {{ $movie->genres->pluck('name')->join(', ') }}
                    </p>

                    <h2 class="text-white font-semibold mt-2">
                        {{ $movie->movieTitle }}
                    </h2>

                    <p class="text-gray-500 text-sm">
                        Khởi chiếu: {{ $movie->releaseDate->format('d/m/Y') }}
                    </p>
                </div>

            </div>

            @endforeach
        </div>

    </div>
</div>

@include('layouts.trailer')

<!-- SLIDER SCRIPT -->
<script>
    let current = 0;
    const slides = document.querySelectorAll('.slide');

    function showSlide(index) {
        slides.forEach(s => {
            s.classList.remove('opacity-100', 'scale-100');
            s.classList.add('opacity-0', 'scale-105');
        });

        slides[index].classList.remove('opacity-0', 'scale-105');
        slides[index].classList.add('opacity-100', 'scale-100');
    }

    function nextSlide() {
        current = (current + 1) % slides.length;
        showSlide(current);
    }

    function prevSlide() {
        current = (current - 1 + slides.length) % slides.length;
        showSlide(current);
    }

    setInterval(nextSlide, 5000);
</script>

@endsection