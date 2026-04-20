@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-semibold mb-1">Quản lý ghế</h4>
            <small class="text-muted">Hiển thị theo từng phòng chiếu, 5 ghế mỗi trang</small>
        </div>

        <a href="{{ route('seat.create') }}" class="btn btn-dark">
            + Thêm ghế
        </a>
    </div>

    <form method="GET" action="{{ url()->current() }}" class="row g-2 mb-3">
        <div class="col-md-4">
            <select name="roomID" class="form-select" onchange="this.form.submit()">
                @foreach($rooms as $room)
                    <option value="{{ $room->roomID }}" {{ (string) $roomID === (string) $room->roomID ? 'selected' : '' }}>
                        {{ $room->roomName }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                class="form-control"
                placeholder="Tìm theo mã ghế, hàng/cột hoặc loại ghế">
        </div>

        <div class="col-auto">
            <button type="submit" class="btn btn-outline-dark">Tìm kiếm</button>
        </div>

        @if(request('search'))
            <div class="col-auto">
                <a href="{{ url()->current() . '?' . http_build_query(['roomID' => $roomID]) }}" class="btn btn-outline-secondary">
                    Xóa lọc
                </a>
            </div>
        @endif
    </form>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Phòng</th>
                        <th>Ghế</th>
                        <th>Hàng</th>
                        <th>Cột</th>
                        <th>Loại ghế</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($seats as $seat)
                        <tr>
                            <td class="text-muted">{{ $seat->seatID }}</td>
                            <td>{{ $seat->screeningRoom->roomName ?? 'N/A' }}</td>
                            <td class="fw-semibold">{{ $seat->rowSeat }}{{ $seat->colSeat }}</td>
                            <td>{{ $seat->rowSeat }}</td>
                            <td>{{ $seat->colSeat }}</td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $seat->seatType->seatTypeName ?? 'N/A' }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('seat.edit', $seat->seatID) }}" class="btn btn-sm btn-outline-dark me-2">
                                    Sửa
                                </a>

                                <form action="{{ route('seat.destroy', $seat->seatID) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa ghế này?')">
                                        Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Không có ghế nào phù hợp
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $seats->links() }}
    </div>
</div>
@endsection
