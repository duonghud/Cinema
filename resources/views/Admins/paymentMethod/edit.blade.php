@extends('layouts.appAdmin')

@section('content')
    <form method="post" action="{{ route('paymentMethod.update', $paymentMethods->paymentID) }}">
        @csrf
        @method('PUT')

        <div>
            <label for="name">Tên phương thức: </label>
            <input type="text" name="name" id="name" value="{{ $paymentMethods->name }}">
        </div>

        <div>
            <button type="submit" class="btn btn-primary">
                Cập nhật
            </button>
        </div>
    </form>
@endsection
