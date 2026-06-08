@extends('layouts.appAdmin')
@section('content')

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold">Danh sách phim</h4>

        <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#createModal">
            + Thêm phim mới
        </button>
    </div>

    @include('admins.partials.page-search', [
        'placeholder' => 'Tìm theo tên phim, đạo diễn, thể loại hoặc hãng'
    ])

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tên phim</th>
                        <th>Poster</th>
                        <th>Trailer</th>
                        <th>Thời lượng</th>
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
                    @forelse($movies as $movie)
                        <tr>
                            <td class="text-muted">{{ $movie->movieID }}</td>
                            <td class="fw-medium">{{ $movie->movieTitle }}</td>
                            <td>
                                <img src="{{ asset('posters/' . $movie->poster) }}" width="70" class="rounded shadow-sm">
                            </td>
                            <td>
                                @if($movie->trailer)
                                    <video width="140" height="80" class="rounded" controls>
                                        <source src="{{ asset($movie->trailer) }}">
                                    </video>
                                @endif
                            </td>
                            <td>{{ $movie->duration }} phút</td>
                            <td>{{ $movie->director }}</td>
                            <td style="max-width:200px;">
                                <small class="text-muted">{{ Str::limit($movie->description, 36) }}</small>
                            </td>
                            <td>{{ $movie->releaseDate->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $movie->ageRating->code }}</span>
                            </td>
                            <td>
                                @foreach($movie->genres as $genre)
                                    <span class="badge bg-secondary">{{ $genre->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $movie->studio->name }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <button class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#editModal{{$movie->movieID}}">
                                        Sửa
                                    </button>

                                    <form action="{{ route('admin.movies.destroy', $movie->movieID) }}" method="POST" class="m-0">
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm btn-outline-danger">
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        @include('admins.manageMovies.movies.edit')
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                Chưa có dữ liệu phim
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $movies->links() }}
    </div>
</div>

@include('admins.manageMovies.movies.create')

@endsection
