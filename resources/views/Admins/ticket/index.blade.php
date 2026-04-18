@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Danh sách vé</h4>

        <a href="{{ route('ticket.create') }}" class="btn btn-primary">
            + Thêm vé
        </a>
    </div>

    @include('admins.partials.page-search', [
        'placeholder' => 'Tìm theo mã vé, ghế, suất chiếu hoặc trạng thái'
    ])

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-bordered align-middle mb-0">
                <thead>
                    <tr>
                        <th>Mã vé</th>
                        <th>Ghế</th>
                        <th>Suất chiếu</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($tickets as $t)
                        <tr>
                            <td>{{ $t->ticketID }}</td>
                            <td>Ghế {{ ($t->seat->rowSeat ?? '') . ($t->seat->colSeat ?? 'N/A') }}</td>
                            <td>
                                ST {{ $t->showTime->showTimeID ?? 'N/A' }}
                                @if($t->showTime?->movie)
                                    <div class="small text-muted">{{ $t->showTime->movie->movieTitle }}</div>
                                @endif
                            </td>
                            <td>{{ number_format($t->price) }} đ</td>
                            <td>
                                @if($t->status == 'available')
                                    <span class="badge bg-success">Còn trống</span>
                                @else
                                    <span class="badge bg-danger">Đã đặt</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('ticket.edit', $t->ticketID) }}" class="btn btn-warning btn-sm">
                                    Sửa
                                </a>

                                <form action="{{ route('ticket.destroy', $t->ticketID) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa vé này?')">
                                        Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Không có vé nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $tickets->links() }}
    </div>
</div>
@endsection
