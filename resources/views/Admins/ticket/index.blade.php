@extends('layouts.appAdmin')

@section('content')
<div class="container">
    <h4>Danh sách vé</h4>
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- 🔥 Nút --}}
    <div class="mb-3">
        <a href="{{ route('ticket.create') }}" class="btn btn-primary">
            + Thêm vé
        </a>
        @if(isset($showTimes))
            @foreach($showTimes as $st)
                <a href="{{ route('ticket.generate', $st->showTimeID) }}" 
                   class="btn btn-success">
                   🎟 Tạo vé ST {{ $st->showTimeID }}
                </a>
            @endforeach
        @endif
    </div>
    @if ($tickets->isEmpty())
        <div class="text-center text-muted py-4">
            Không có vé nào
        </div>
    @else

    <table class="table table-bordered align-middle">
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
            @foreach($tickets as $t)
            <tr>
                <td>{{ $t->ticketID }}</td>

                <td>
                    Ghế {{ $t->seat->colSeat ?? 'N/A' }}
                </td>

                <td>
                    {{ $t->showTime->showTimeID ?? 'N/A' }}
                </td>

                <td>
                    {{ number_format($t->price) }} đ
                </td>

                <td>
                    @if($t->status == 'available')
                        <span class="badge bg-success">Còn trống</span>
                    @else
                        <span class="badge bg-danger">Đã đặt</span>
                    @endif
                </td>

                <td>
                    <a href="{{ route('ticket.edit', $t->ticketID) }}" 
                       class="btn btn-warning btn-sm">
                        Sửa
                    </a>

                    <form action="{{ route('ticket.destroy', $t->ticketID) }}" 
                          method="POST" 
                          style="display:inline;">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger btn-sm"
                            onclick="return confirm('Bạn có chắc muốn xóa vé này?')">
                            Xóa
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @endif
</div>
@endsection