@extends('layouts.appAdmin')

@section('content')
<a href="{{ route('food.create') }}">
    <button>
        Thêm món ăn
    </button>
</a>

<table class="table table-bordered">
    <tr>
        <th>STT</th>
        <th>Tên đồ ăn</th>
        <th>Giá</th>
        <th>Kiểu</th>
        <th></th>
        <th></th>
    </tr>
    @foreach($foods as $food)

    <tr>
        <td>{{ $food->foodID }}</td>
        <td>{{ $food->foodName }}</td>
        <td>{{ number_format($food->price, 0, ',', '.') }}đ</td>
        <td>{{ $food->foodType }}</td>
        <td>
            <a href="{{ route('food.edit', $food->foodID) }}">
                <button>
                    Sửa
                </button>
            </a>
        </td>
        <td>
            <form action="{{ route('food.destroy', $food->foodID) }}" method="POST">
                @csrf
                @method('DELETE')

                <button type="submit" onclick="return ">
                    Xóa
                </button>
            </form>
        </td>
    </tr>
    @endforeach
</table>

<div class="mt-3">
    {{ $foods->links() }}
</div>
@endsection