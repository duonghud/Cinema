@extends('layouts.app')
@section('content')

    <body>
    <div class="container mx-auto p-10">

        <h1 class="text-3xl font-bold text-center mb-10">
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

                <div class="bg-white rounded-xl shadow-lg overflow-hidden group relative">

                    <img
                        src="{{ asset('posters/'.$movie->poster) }}"
                        class="w-full h-72 object-cover">

                    <div class="absolute inset-0 bg-black bg-opacity-60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">

                        <button
                            onclick="openTrailer('{{ $videoID }}')"
                            class="bg-red-600 text-white px-5 py-2 rounded-lg hover:bg-red-700">

                            Xem trailer

                        </button>
                    </div>

                    <div class="p-4">

                        <h2 class="text-lg font-bold mb-2">
                             {{ $movie->movieTitle }}
                        </h2>

                        <p class="text-sm">
                            Ngày phát hành: {{ $movie->releaseDate }}
                        </p>

                        <p class="text-red-500 text-sm">
                            <b>Độ tuổi:</b> {{ $movie->ageRating->code ?? '' }}
                        </p>

                    </div>

                </div>

            @endforeach

        </div>
            

    </div>
    @include('layouts.trailer')
    </body>

@endsection



