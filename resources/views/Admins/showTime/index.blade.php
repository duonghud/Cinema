@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold">Danh sách suất chiếu</h4>

        <a href="{{ route('showTime.create') }}" class="btn btn-dark">
            + Thêm suất chiếu
        </a>
    </div>

    @include('admins.partials.page-search', [
        'placeholder' => 'Tìm theo phim, phòng hoặc ngày chiếu'
    ])

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="6%">ID</th>
                        <th>Ngày</th>
                        <th>Thời lượng</th>
                        <th>Phim</th>
                        <th>Phòng</th>
                        <th class="text-end" width="20%">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($showTimes as $item)
                        <tr>
                            <td class="text-muted">{{ $item->showTimeID }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->showDate)->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ substr($item->startTime, 0, 5) }}-{{ substr($item->endTime, 0, 5) }}
                                    ({{ (strtotime($item->endTime) - strtotime($item->startTime)) / 60 }} phút)
                                </span>
                            </td>
                            <td class="fw-medium">{{ $item->movie->movieTitle ?? '---' }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $item->room->roomName ?? '---' }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('showTime.edit', $item->showTimeID) }}" class="btn btn-sm btn-outline-dark me-2">
                                    Sửa
                                </a>

                                <form action="{{ route('showTime.destroy', $item->showTimeID) }}" method="POST" class="d-inline">
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
                            <td colspan="6" class="text-center text-muted py-4">
                                Không có suất chiếu nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-end">
        {{ $showTimes->links() }}
    </div>
</div>
@endsection
