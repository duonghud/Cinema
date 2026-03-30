@extends('layouts.app')
@section('content')

<div class="min-h-screen py-10">

    <div class="container mx-auto px-10">

        <h1 class="text-4xl font-bold text-center text-white mb-12 tracking-wide">
            Phim đang chiếu
        </h1>

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

            <div class="group relative rounded-xl overflow-hidden bg-[#10141B]/60 backdrop-blur hover:shadow-2xl">

                <!-- Poster -->
                <div class="relative overflow-hidden">
                    <a href="{{ route('movies.show', $movie) }}">
                        <img src="{{ asset('posters/'.$movie->poster) }}" class="w-full h-80 object-cover">
                    </a>
                </div>

                <!-- Info -->
                <div class="p-4 space-y-2">
                    <div class="text-xs text-gray-500 space-y-1">
                        <p>
                            <span class="text-gray-400">
                                {{ $movie->genres->pluck('name')->join(', ') }} {{ $movie->releaseDate->format('d/m/Y') }}
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

    </div>

</div>

@include('layouts.trailer')

@endsection