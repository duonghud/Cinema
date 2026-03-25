@extends('layouts.appAdmin')

@section('content')
    <form method="post" action="{{ route('food.update', $foods->foodID) }}">
        @csrf
        @method('PUT')

        <div>
            <label>Tên đồ ăn:</label>
            <input type="text" name="foodName" value="{{ $foods->foodName }}">
        </div>

        <div>
            <label>Giá:</label>
            <input type="text" name="price" value="{{ $foods->price }}">
        </div>

        <div>
            <label for="foodType">Kiểu: </label>
            <select name="foodType" id="foodType">

                <option value="Đồ ăn"
                    {{ old('role', $foods->foodType) == 'Đồ ăn' ? 'selected' : '' }}>
                    Đồ ăn
                </option>

                <option value="Đồ uống"
                    {{ old('role', $foods->foodType) == 'Đồ uống' ? 'selected' : '' }}>
                    Đồ uống
                </option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            Cập nhật
        </button>
    </form>
@endsection
