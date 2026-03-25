@extends('layouts.appAdmin')
@section('content')


<div class="container mt-4">

    <h2>Danh sách phim</h2>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createModal">
        Thêm phim mới
    </button>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên phim</th>
                <th>Poster</th>
                <th>Trailer</th>
                <th>Đạo diễn</th>
                <th>Mô tả</th>
                <th>Thời gian phát hành</th>
                <th>Kiểm duyệt độ tuổi</th>
                <th>Thể loại</th>
                <th>Hãng</th>
                <th>Hoạt động</th>
            </tr>
        </thead>

        <tbody>
            @foreach($movies as $movie)
            <tr>
                <td>{{ $movie->movieID }}</td>
                <td>{{ $movie->movieTitle }}</td>
                <td>
                    <img src="{{ asset('posters/'.$movie->poster) }}" width="80">
                </td>
                <td>
                    @php
                    $videoId = explode('v=', $movie->trailer);
                    $videoId = isset($videoId[1]) ? explode('&', $videoId[1])[0] : '';
                    @endphp

                    <iframe width="200" height="120"
                        src="https://www.youtube.com/embed/{{ $videoId }}"
                        frameborder="0"
                        allowfullscreen>
                    </iframe>
                </td>
                <td>{{ $movie->director }}</td>
                <td>{{ $movie->description }}</td>
                <td>{{ $movie->releaseDate }}</td>
                <td>{{ $movie->ageRating->code }}</td>
                <td></td>
                <td>{{ $movie->studio->name }}</td>

                <td>
                    <button class="btn btn-warning btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#editModal{{$movie->movieID}}">
                        Sửa
                    </button>

                    <form action="{{route('movies.destroy',$movie->movieID)}}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Xóa</button>
                    </form>
                </td>
            </tr>

            @include('admins.movies.edit')

            @endforeach
        </tbody>
    </table>

</div>

@include('admins.movies.create')


@endsection
