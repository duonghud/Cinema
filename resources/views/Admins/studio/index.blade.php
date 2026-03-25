@extends('layouts.appAdmin')

@section('content')
    <a href="{{ route('studio.create') }}">
        <button>
            Thêm nhà sản xuất
        </button>
    </a>

    <table class="table table-bordered">
        <tr>
            <th>STT</th>
            <th>Tên nhà sản xuất</th>
            <th></th>
            <th></th>
        </tr>

        @foreach ($studios as $studio)
            <tr>
                <td>{{ $studio->studioID }}</td>
                <td>{{ $studio->name }}</td>
                <td>
                    <a href="{{ route('studio.edit', $studio->studioID) }}">
                        <button>
                            Sửa
                        </button>
                    </a>
                </td>
                <td>
                    <form action="{{ route('studio.destroy', $studio->studioID) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit" onclick="return confirm('Bạn có chắc muốn xóa?')">
                            Xóa
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
