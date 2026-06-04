@extends('layouts.appAdmin')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-semibold text-dark">Danh sách phòng chiếu</h3>

        <a href="{{ route('screeningRoom.create') }}" class="btn btn-dark px-4">
            + Thêm phòng
        </a>
    </div>

    @include('admins.partials.page-search', [
        'placeholder' => 'Tìm theo tên phòng, sức chứa hoặc loại màn'
    ])

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <form method="GET" action="{{ route('screeningRoom.index') }}" class="d-flex gap-2">
            <select name="screenType" class="form-select" style="width: 220px;">
                <option value="">Loại phòng</option>

                @foreach($screenTypes as $type)
                <option value="{{ $type->screenTypeID }}"
                    {{ request('screenType') == $type->screenTypeID ? 'selected' : '' }}>
                    {{ $type->name }}
                </option>
                @endforeach
            </select>

            <button class="btn btn-dark">Lọc</button>
        </form>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="bg-white border rounded-3 shadow-sm">
        <table class="table mb-0 align-middle">
            <thead class="border-bottom">
                <tr class="text-muted small">
                    <th>ID</th>
                    <th>Tên phòng</th>
                    <th>Sức chứa</th>
                    <th>Loại màn</th>
                    <th class="text-end">Hành động</th>
                </tr>
            </thead>

            <tbody>
                @forelse($room as $r)
                <tr class="border-bottom">
                    <td class="text-muted">{{ $r->roomID }}</td>
                    <td class="fw-medium">{{ $r->roomName }}</td>
                    <td>{{ $r->capacity }}</td>
                    <td>
                        @if($r->screenType)
                            <span class="text-secondary">{{ $r->screenType->name }}</span>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td class="text-end">

                        <a href="{{ route('seat.index', ['roomID' => $r->roomID]) }}"
                           class="btn btn-sm btn-outline-primary">
                            Xem ghế
                        </a>

                        <a href="{{ route('screeningRoom.edit', $r->roomID) }}"
                           class="btn btn-sm btn-outline-dark me-2">
                            Sửa
                        </a>

                        <form action="{{ route('screeningRoom.destroy', $r->roomID) }}"
                              method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Xóa phòng {{ addslashes($r->roomName) }}? Toàn bộ ghế trong phòng sẽ bị xóa theo.')">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-sm btn-outline-danger">
                                Xóa
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        Chưa có dữ liệu phòng chiếu
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $room->links() }}
    </div>
</div>
@endsection