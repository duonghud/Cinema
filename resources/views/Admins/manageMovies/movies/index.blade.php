@extends('layouts.appAdmin')
@section('content')

<div class="container mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold">Danh sách phim</h4>

        <button class="btn btn-dark mb-3" data-bs-toggle="modal" data-bs-target="#createModal">
            + Thêm phim mới
        </button>
    </div>

    <!-- Card -->
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tên phim</th>
                        <th>Poster</th>
                        <th>Trailer</th>
                        <th>Đạo diễn</th>
                        <th>Mô tả</th>
                        <th>Phát hành</th>
                        <th>Độ tuổi</th>
                        <th>Thể loại</th>
                        <th>Hãng</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($movies as $movie)
                    <tr>

                        <td class="text-muted">{{ $movie->movieID }}</td>

                        <td class="fw-medium">
                            {{ $movie->movieTitle }}
                        </td>

                        <!-- Poster -->
                        <td>
                            <img src="{{ asset('posters/'.$movie->poster) }}"
                                 width="70"
                                 class="rounded shadow-sm">
                        </td>

                        <!-- Trailer -->
                        <td>
                            @php
                            $videoId = explode('v=', $movie->trailer);
                            $videoId = isset($videoId[1]) ? explode('&', $videoId[1])[0] : '';
                            @endphp

                            <iframe width="140" height="80"
                                class="rounded"
                                src="https://www.youtube.com/embed/{{ $videoId }}"
                                frameborder="0"
                                allowfullscreen>
                            </iframe>
                        </td>

                        <td>{{ $movie->director }}</td>

                        <!-- Description -->
                        <td style="max-width:200px;">
                            <small class="text-muted">
                                {{ Str::limit($movie->description, 80) }}
                            </small>
                        </td>

                        <td>{{ $movie->releaseDate->format('d/m/Y') }}</td>

                        <!-- Age -->
                        <td>
                            <span class="badge bg-secondary">
                                {{ $movie->ageRating->code }}
                            </span>
                        </td>

                        <td></td>

                        <!-- Studio -->
                        <td>
                            <span class="badge bg-light text-dark">
                                {{ $movie->studio->name }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="text-end">

                            <button class="btn btn-sm btn-outline-dark me-2"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal{{$movie->movieID}}">
                                Sửa
                            </button>

                            <form action="{{route('movies.destroy',$movie->movieID)}}"
                                  method="POST"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-outline-danger">
                                    Xóa
                                </button>
                            </form>

                        </td>

                    </tr>

                    @include('admins.manageMovies.movies.edit')

                    @endforeach
                </tbody>

            </table>

        </div>
    </div>

</div>

@include('admins.manageMovies.movies.create')

@endsection