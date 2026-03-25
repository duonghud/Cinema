@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">
    <h3>Danh sách suất chiếu</h3>

    <a href="{{ route('showTime.create') }}" class="btn btn-primary mb-3">+ Thêm</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ngày</th>
                <th>Giờ bắt đầu</th>
                <th>Giờ kết thúc</th>
                <th>Phim</th>
                <th>Phòng</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($showTimes as $item)
            <tr>
                <td>{{ $item->showTimeID }}</td>
                <td>{{ $item->showDate }}</td>
                <td>{{ substr($item->startTime, 0, 5) }}</td>
                <td>{{ substr($item->endTime, 0, 5) }}</td>
                <td>{{ $item->movie->movieTitle ?? '' }}</td>
                <td>{{ $item->room->roomName ?? '' }}</td>
                <td>
                    <a href="{{ route('showTime.edit', $item->showTimeID) }}" class="btn btn-warning btn-sm">Sửa</a>

                    <form action="{{ route('showTime.destroy', $item->showTimeID) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $showTimes->links() }}
</div>
@endsection