@extends('layouts.appAdmin')

@section('content')
    <form method="post" action="{{ route('food.store') }}">
        @csrf

        <div>
            <label for="foodName">Tên đồ ăn: </label>
            <input type="text" name="foodName" id="foodName" placeholder="Nhập tên đồ ăn">
        </div>

        <div>
            <label for="price">Giá: </label>
            <input type="text" name="price" id="price" placeholder="Nhập giá đồ ăn">
        </div>

        <div>
            <label for="foodType">Kiểu: </label>
            <select name="foodType" id="foodType">
                <option>Đồ ăn</option>
                <option>Đồ uống</option>
            </select>
        </div>

        <div>
            <button type="submit" class="btn btn-primary">
                Thêm món ăn
            </button>
        </div>
    </form>
@endsection
