@extends('layouts.appAdmin')

@section('content')
    <a href="{{ route('screenType.create') }}">
        <button>
            Thêm định dạng
        </button>
    </a>

    <table class="table table-bordered">
        <tr>
            <th>STT</th>
            <th>Tên định dạng</th>
            <th></th>
            <th></th>
        </tr>

        @foreach($screenTypes as $screenType)
            <tr>
                <td>{{ $screenType->screenTypeID }}</td>
                <td>{{ $screenType->name }}</td>
                <td>
                    <a href="{{ route('screenType.edit', $screenType->screenTypeID) }}">
                        <button>
                            Sửa
                        </button>
                    </a>
                </td>
                <td>
                    <form action="{{ route('screenType.destroy', $screenType->screenTypeID) }}" method="POST">
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
