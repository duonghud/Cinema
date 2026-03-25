@extends('layouts.appAdmin')

@section('content')
<h2>Thêm khách hàng</h2>

{{-- Hiển thị lỗi validate --}}
@if ($errors->any())
<div style="color:red; margin-bottom:15px;">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('customer.store') }}">
    @csrf

    <div>
        <label for="fullName">Họ tên:</label>
        <input 
            type="text" 
            name="fullName" 
            id="fullName" 
            placeholder="Nhập họ tên"
            value="{{ old('fullName') }}"
        >
    </div>

    <div>
        <label for="email">Email:</label>
        <input 
            type="email" 
            name="email" 
            id="email" 
            placeholder="Nhập email"
            value="{{ old('email') }}"
        >
    </div>

    <div>
        <label for="password">Mật khẩu:</label>
        <input 
            type="password" 
            name="password" 
            id="password" 
            placeholder="Nhập mật khẩu"
        >
    </div>

    <div>
        <label for="phoneNumber">Số điện thoại:</label>
        <input 
            type="text" 
            name="phoneNumber" 
            id="phoneNumber" 
            placeholder="Nhập số điện thoại"
            value="{{ old('phoneNumber') }}"
        >
    </div>

    <div>
        <label for="address">Địa chỉ:</label>
        <input 
            type="text" 
            name="address" 
            id="address" 
            placeholder="Nhập địa chỉ"
            value="{{ old('address') }}"
        >
    </div>

    <div>
        <button type="submit" class="btn btn-primary">
            Thêm khách hàng
        </button>
    </div>

</form>

@endsection
>>>>>>> 62dbd1183a0333b3d2f39924f33e517ace5ff3b2
