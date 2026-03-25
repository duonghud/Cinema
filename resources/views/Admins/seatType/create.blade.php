@extends('layouts.appAdmin')

@section('content')
    <form method="post" action="{{ route('seatType.store') }}">
        @csrf

        <div>
            <label for="seatTypeName">Tên kiểu ghế: </label>
            <input type="text" name="seatTypeName" id="seatTypeName" placeholder="Nhập tên kiểu ghế">
        </div>

        <div>
            <button type="submit" class="btn btn-primary">
                Thêm kiểu ghế
            </button>
        </div>
    </form>
@endsection
