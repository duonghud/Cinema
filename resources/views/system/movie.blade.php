@extends('layouts.app')
@section('content')

<div class="min-h-screen py-10">

    <div class="container mx-auto px-10">

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


                    <!-- Showtime -->
                        @if($movie->showTimes->count() > 0)

                        <div class="flex flex-wrap gap-2 mt-2">

                            @foreach($movie->showTimes as $show)

                                @php
                                    $duration = (strtotime($show->endTime) - strtotime($show->startTime)) / 60;
                                @endphp

                                <span class="px-2 py-1 text-xs bg-red-500/20 text-red-400 rounded">
                                    {{ substr($show->startTime,0,5) }}
                                    - {{ $duration }}p
                                </span>

                            @endforeach

                        </div>

                        @else
                            <p class="text-gray-500">Chưa có lịch chiếu</p>
                        @endif
                    

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

@endsection