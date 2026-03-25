@extends('layouts.appAdmin')

@section('content')
    <h2>Danh sách phòng chiếu</h2>

    <a href="{{ route('screeningRoom.create') }}" class="btn btn-success">
        Thêm phòng
    </a>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table border="1" width="800">
        <thead>
        <tr>
            <th>ID</th>
            <th>Tên phòng</th>
            <th>Sức chứa</th>
            <th>Loại phòng chiếu</th>
            <th>Hoạt động</th>
        </tr>
        </thead>

        <tbody>

        @foreach($room as $r)

            <tr>

                <td>{{ $r->roomID }}</td>
                <td>{{ $r->roomName }}</td>
                <td>{{ $r->capacity }}</td>

                <td>
                    @if($r->screenType)
                        {{ $r->screenType->name }}
                    @else
                        N/A
                    @endif
                </td>

                <td>

                    <a href="{{ route('screeningRoom.edit',$r->roomID) }}">
                        Sửa
                    </a>

                    <form action="{{ route('screeningRoom.destroy',$r->roomID) }}" method="POST" style="display:inline">

                        @csrf
                        @method('DELETE')

                        <button onclick="return confirm('Delete this room?')">
                            Xóa
                        </button>

                    </form>

                </td>

            </tr>

        @endforeach

        </tbody>

    </table>

@endsection

