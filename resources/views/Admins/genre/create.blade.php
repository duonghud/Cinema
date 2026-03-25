@extends('layouts.appAdmin')

@section('content')
    <form method="post" action="{{ route('genre.store') }}">
        @csrf

        <div>
            <label for="name">Tên thể loại: </label>
            <input type="text" name="name" id="name" placeholder="Nhập tên thể loại">
        </div>

        <div>
            <button type="submit" class="btn btn-primary">
                Thêm thể loại
            </button>
        </div>
    </form>
@endsection
