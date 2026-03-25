@extends('layouts.appAdmin')

@section('content')
    <form method="post" action="{{ route('screenType.store') }}">
        @csrf

        <div>
            <label for="name">Tên định dạng: </label>
            <input type="text" name="name" id="name" placeholder="Nhập tên định dạng">
        </div>

        <div>
            <button type="submit" class="btn btn-primary">
                Thêm định dạng
            </button>
        </div>
    </form>
@endsection
