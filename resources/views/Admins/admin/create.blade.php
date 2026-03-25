@extends('layouts.appAdmin')

@section('content')

<h2>Thêm nhân viên</h2>

{{-- Hiển thị lỗi --}}
@if ($errors->any())
<div style="color:red">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('admin.store') }}">
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
        
    

    <div>
        <label for="role">Chức vụ</label>
        <select name="role" id="role">
            <option value="">-- Chọn chức vụ --</option>
            <option value="admin">Quản trị viên</option>
            <option value="ticket_staff">Nhân viên bán vé</option>
            <option value="food_staff">Nhân viên bán đồ ăn</option>
        </select>
    </div>

    <div>
        <button type="submit" class="btn btn-primary">
            Thêm nhân viên
        </button>
    </div>

</form>

@endsection
