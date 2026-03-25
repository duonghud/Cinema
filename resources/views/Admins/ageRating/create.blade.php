@extends('layouts.appAdmin')

@section('content')
    <form action="{{ route('ageRating.store') }}" method="post">
        @csrf

        <div>
            <label for="code">Tên Kiểm duyệt: </label>
            <input type="text" name="code" id="code" placeholder="Nhập tên kiểm duyệt">
        </div>

        <div>
            <label for="description">Mô tả: </label>
            <input type="text" name="description" id="description" placeholder="Nhập mô tả">
        </div>

        <div>
            <button type="submit" class="btn btn-primary">
                Thêm kiểu duyệt
            </button>
        </div>
    </form>
@endsection
