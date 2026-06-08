@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold">Quản lý thể loại</h4>

        <a href="{{ route('genre.create') }}" class="btn btn-dark">
            + Thêm thể loại
        </a>
    </div>

    @include('admins.partials.page-search', [
        'placeholder' => 'Tìm theo tên thể loại'
    ])

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="10%">ID</th>
                        <th>Tên thể loại</th>
                        <th class="text-end" width="25%">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($genres as $genre)
                        <tr>
                            <td class="text-muted">{{ $genre->genreID }}</td>
                            <td class="fw-medium">
                                <span class="badge bg-light text-dark">{{ $genre->name }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('genre.edit', $genre->genreID) }}" class="btn btn-sm btn-outline-dark me-2">
                                    Sửa
                                </a>

                                <form action="{{ route('genre.destroy', $genre->genreID) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                Không có dữ liệu thể loại
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $genres->links() }}
    </div>
</div>
@endsection
