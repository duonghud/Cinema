@extends('layouts.appAdmin')

@section('content')
    <a href="{{ route('genre.create') }}">
        <button>
            Thêm thể loại
        </button>
    </a>

    <table class="table table-bordered">
        <tr>
            <th>STT</th>
            <th>Tên thể loại</th>
            <th></th>
            <th></th>
        </tr>

        @foreach($genres as $genre)
            <tr>
                <td>{{ $genre->genreID }}</td>
                <td>{{ $genre->name }}</td>
                <td>
                    <a href="{{ route('genre.edit', $genre->genreID) }}">
                        <button>
                            Sửa
                        </button>
                    </a>
                </td>
                <td>
                    <form action="{{ route('genre.destroy', $genre->genreID) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit">
                            Xóa
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
