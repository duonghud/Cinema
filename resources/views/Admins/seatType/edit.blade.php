@extends('layouts.appAdmin')

@section('content')
    <form method="post" action="{{ route('seatType.update', $seatTypes->seatTypeID) }}">
        @csrf
        @method('PUT')

        <div>
            <label for="seatTypeName">Tên phương thức: </label>
            <input type="text" name="seatTypeName" id="seatTypeName" value="{{ $seatTypes->seatTypeName }}">
        </div>

        <div>
            <button type="submit" class="btn btn-primary">
                Cập nhật
            </button>
        </div>
    </form>
@endsection
