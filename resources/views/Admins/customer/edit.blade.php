@extends('layouts.appAdmin')

@section('content')
    <form method="post" action="{{ route('customer.update', $customers->customerID) }}">
        @csrf
        @method('PUT')

        <div>
            <label for="fullName">Họ tên: </label>
            <input type="text" name="fullName" id="fullName" value="{{ $customers->fullName }}">
        </div>

        <div>
            <label for="email">Email: </label>
            <input type="text" name="email" id="email" value="{{ $customers->email }}">
        </div>

        <div>
            <label>Password mới (nếu muốn đổi):</label>
            <input type="password" name="password">
        </div>

        <div>
            <label for="phoneNumber">Số điện thoại: </label>
            <input type="text" name="phoneNumber" id="phoneNumber" value="{{ $customers->phoneNumber }}">
        </div>


        <div>
            <label for="address">Địa chỉ: </label>
            <input type="text" name="address" id="address" value="{{ $customers->address }}">
        </div>

        <div>
            <button type="submit" class="btn btn-primary">
                Cập nhật
            </button>
        </div>
    </form>
@endsection
