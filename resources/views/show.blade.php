@extends('layouts.app')
@section('content')

<div class="relative min-h-screen text-white">

    <!-- BACKGROUND -->
    <div class="absolute inset-0">
        <img src="{{ asset('posters/'.$movie->poster) }}"
             class="w-full h-full object-cover ">
        <div class="absolute inset-0 bg-gradient-to-r from-black via-black/90 to-black/70"></div>
    </div>

    <div class="relative z-10 px-10 py-4 flex justify-between items-center">
    <!-- MAIN -->
    <div class="relative z-10 max-w-6xl mx-auto px-10 pt-10 pb-20">

        <div class="grid grid-cols-3 gap-10 items-center">

            <!-- POSTER -->
            <div>
                <img src="{{ asset('posters/'.$movie->poster) }}"
                     class="rounded-2xl shadow-2xl">
            </div>

            <!-- INFO -->
            <div class="col-span-2 space-y-3">

                <h1 class="text-3xl font-bold uppercase">
                    {{ $movie->movieTitle }}
                    <span class="text-sm border px-2 py-1 rounded ml-2">
                        {{ $movie->ageRating->code ?? 'T16' }}
                    </span>
                </h1>

                <p class="text-gray-300">
                    {{ $movie->genres->pluck('name')->join(', ') }}
                    &nbsp;&nbsp; Đạo diễn: {{ $movie->director }}
                </p>

                <p class="text-gray-300">
                    Diễn viên: {{ $movie->cast }}
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
                    <!-- <a href="#" class="underline">Chi tiết nội dung</a> -->

                    <button onclick="openTrailer('{{ $movie->trailer }}')"
                        class="border border-yellow-400 text-yellow-400 px-6 py-2 rounded-full hover:bg-yellow-400 hover:text-black transition">
                        Xem trailer
                    </button>
                </div>

            </div>
        </div>
        <!-- DATE SELECT -->
        <div class="flex gap-4 mt-12">
            <div class="bg-red-500 px-6 py-4 rounded text-center">
                <p class="text-sm">Th. 03</p>
                <p class="text-2xl font-bold">29</p>
                <p class="text-sm">Chủ nhật</p>
            </div>

            <div class="bg-gray-800 px-6 py-4 rounded text-center">
                <p class="text-sm">Th. 03</p>
                <p class="text-2xl font-bold">30</p>
                <p class="text-sm">Thứ hai</p>
            </div>
        </div>

    </div>
</div>

@include('layouts.trailer')
@endsection
