@extends('layouts.appAdmin')

@section('content')
    <form method="post" action="{{ route('paymentMethod.store') }}">
        @csrf

        <div>
            <label for="name">Tên phương thức: </label>
            <input type="text" name="name" id="name" placeholder="Nhập tên phương thức">
        </div>

        <div>
            <button type="submit" class="btn btn-primary">
                Thêm phương thức
            </button>
        </div>
    </form>
@endsection
