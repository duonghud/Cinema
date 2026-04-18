@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold">Quản lý kiểu ghế</h4>

        <a href="{{ route('seatType.create') }}" class="btn btn-dark">
            + Thêm kiểu ghế
        </a>
    </div>

    @include('admins.partials.page-search', [
        'placeholder' => 'Tìm theo tên kiểu ghế'
    ])

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="10%">ID</th>
                        <th>Kiểu ghế</th>
                        <th class="text-end" width="25%">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($seatTypes as $seatType)
                        <tr>
                            <td class="text-muted">{{ $seatType->seatTypeID }}</td>
                            <td class="fw-medium">
                                <span class="badge bg-light text-dark">{{ $seatType->seatTypeName }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('seatType.edit', $seatType->seatTypeID) }}" class="btn btn-sm btn-outline-dark me-2">
                                    Sửa
                                </a>

                                <form action="{{ route('seatType.destroy', $seatType->seatTypeID) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">
                                        Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                Chưa có dữ liệu kiểu ghế
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $seatTypes->links() }}
    </div>
</div>
@endsection
