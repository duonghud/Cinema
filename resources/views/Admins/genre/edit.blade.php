@extends('layouts.appAdmin')

@section('content')
    <form method="post" action="{{ route('genre.update', $genres->genreID) }}">
        @csrf
        @method('PUT')

        <div>
            <label for="name">Tên thể loại: </label>
            <input type="text" name="name" id="name" value="{{ $genres->name }}">
        </div>

        <div>
            <button type="submit" class="btn btn-primary">
                Cập nhật
            </button>
        </div>
    </form>
@endsection
