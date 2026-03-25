@extends('layouts.appAdmin')

@section('content')
    <form method="post" action="{{ route('admin.update', $admins->adminID) }}">
        @csrf
        @method('PUT')

        <div>
            <label>Họ tên:</label>
            <input type="text" name="fullName" value="{{ $admins->fullName }}">
        </div>

        <div>
            <label>Email:</label>
            <input type="email" name="email" value="{{ $admins->email }}">
        </div>

        <div>
            <label for="role">Chức vụ</label>
            <select name="role" id="role">

                <option value="Quản trị viên"
                    {{ old('role', $admins->role) == 'Quản trị viên' ? 'selected' : '' }}>
                    Quản trị viên
                </option>

                <option value="Nhân viên bán vé"
                    {{ old('role', $admins->role) == 'Nhân viên bán vé' ? 'selected' : '' }}>
                    Nhân viên bán vé
                </option>

                <option value="Nhân viên bán đồ ăn"
                    {{ old('role', $admins->role) == 'Nhân viên bán đồ ăn' ? 'selected' : '' }}>
                    Nhân viên bán đồ ăn
                </option>

            </select>
        </div>

        <div>
            <label>Password mới (nếu muốn đổi):</label>
            <input type="password" name="password">
        </div>

        <button type="submit" class="btn btn-primary">
            Cập nhật
        </button>
    </form>
@endsection
