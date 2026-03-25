@extends('layouts.appAdmin')

@section('content')
    <form method="post" action="{{ route('screenType.update', $screenTypes->screenTypeID) }}">
        @csrf
        @method('PUT')

        <div>
            <label for="name">Tên định dạng: </label>
            <input type="text" name="name" id="name" value="{{ $screenTypes->name }}">
        </div>

        <div>
            <button type="submit" class="btn btn-primary">
                Cập nhật
            </button>
        </div>
    </form>
@endsection
