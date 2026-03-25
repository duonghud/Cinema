@extends('layouts.appAdmin')

@section('content')
</style>
    <a href="{{ route('seatType.create') }}">
        <button>
            Thêm kiểu ghế
        </button>
    </a>

    <table class="table table-bordered">
        <tr>
            <th>STT</th>
            <th>Tên kiểu ghế</th>
            <th></th>
            <th></th>
        </tr>

        @foreach($seatTypes as $seatType)
            <tr>
                <td>{{ $seatType->seatTypeID }}</td>
                <td>{{ $seatType->seatTypeName }}</td>
                <td>
                    <a href="{{ route('seatType.edit', $seatType->seatTypeID) }}">
                        <button>
                            Sửa
                        </button>
                    </a>
                </td>
                <td>
                    <form action="{{ route('seatType.destroy', $seatType->seatTypeID) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit" >
                            Xóa
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
