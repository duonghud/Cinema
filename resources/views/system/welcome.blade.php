@extends('layouts.app')
@section('content')

<!-- HERO MOVIE SLIDER -->
<div class="relative w-full h-[520px] overflow-hidden">

    @foreach($banners as $index => $movie)
    <div class="slide absolute inset-0 transition-all duration-700 
        {{ $index == 0 ? 'opacity-100 scale-100' : 'opacity-0 scale-105' }}">

        <!-- Background -->
        <img src="{{ asset('posters/'.$movie->poster) }}"
            class="w-full h-full object-cover">

        <!-- Overlay gradient -->
        <div class="absolute inset-0 bg-gradient-to-r from-black via-black/70 to-transparent"></div>

        <!-- Content -->
        <div class="absolute left-20 bottom-20 text-white max-w-xl">

            <h1 class="text-4xl md:text-5xl font-bold mb-3">
                {{ $movie->movieTitle }}
            </h1>

            <p class="text-gray-300 mb-4 line-clamp-3">
                {{ $movie->description ?? 'Đang chiếu tại rạp' }}
            </p>

            <div class="flex gap-4">

                <!-- Xem chi tiết -->
                <a href="{{ route('movies.show', $movie) }}"
                    class="px-6 py-2 bg-red-600 hover:bg-red-700 rounded-lg">
                    Mua vé
                </a>

                <!-- Trailer -->
                <button onclick="openTrailer('{{ $movie->trailer }}')"
                    class="px-6 py-2 border border-white rounded-lg hover:bg-white hover:text-black">
                    Xem trailer
                </button>

            </div>
        </div>

    </div>
    @endforeach

    <!-- Buttons -->
    <button onclick="prevSlide()" class="absolute left-5 top-1/2 text-white text-4xl">‹</button>
    <button onclick="nextSlide()" class="absolute right-5 top-1/2 text-white text-4xl">›</button>

</div>

<div class="min-h-screen py-10">

    <div class="container mx-auto px-10 max-w-7xl">

        <div class="flex items-center gap-2 mb-6">
            <div class="rounded-full bg-red-500 w-4 h-4"></div>
            <h3 class="font-bold md:text-2xl text-light">Phim đang chiếu</h3>
        </div>

        <div class="grid grid-cols-4 gap-8">

            @foreach($movies as $movie)

            @php
            $videoID = '';

            if(str_contains($movie->trailer,'watch?v=')){
            $videoID = explode('watch?v=',$movie->trailer)[1];
            }
            elseif(str_contains($movie->trailer,'youtu.be/')){
            $videoID = explode('youtu.be/',$movie->trailer)[1];
            }
            else{
            $videoID = $movie->trailer;
            }
            @endphp

            <div class="group relative rounded-xl overflow-hidden bg-[#10141B]/60 backdrop-blur hover:shadow-2xl transition">

                <!-- Poster -->
                <div class="relative overflow-hidden">
                    <a href="{{ route('movies.show', $movie) }}">
                        <img src="{{ asset('posters/'.$movie->poster) }}"
                            class="w-full h-80 object-cover group-hover:scale-105 transition duration-300">
                    </a>
                </div>

                <!-- Info -->
                <div class="p-4 space-y-2">

                    <div class="text-xs text-gray-500 space-y-1">

                        <!-- Genre + Release Date -->
                        <p>
                            <span class="text-gray-400">
                                {{ $movie->genres->pluck('name')->join(', ') }}
                                |
                                {{ $movie->releaseDate->format('d/m/Y') }}
                            </span>
                        </p>

                    </div>


                    <!-- Title -->
                    <h2 class="text-base font-semibold text-white leading-tight line-clamp-2 group-hover:text-red-400 transition">
                        {{ $movie->movieTitle }} - {{ $movie->ageRating->code ?? 'N/A' }}
                    </h2>

                </div>

            </div>

            @endforeach

        </div>

        <div class="flex items-center gap-2 mb-6">
            <div class="rounded-full bg-red-500 w-4 h-4"></div>
            <h3 class="font-bold md:text-2xl text-light">Phim sắp chiếu</h3>
        </div>

    </div>

</div>

@include('layouts.trailer')


<script>
    let current = 0;
    const slides = document.querySelectorAll('.slide');

    function showSlide(index) {
        slides.forEach((s, i) => {
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

    // auto chạy
    setInterval(nextSlide, 5000);
</script>
@endsection