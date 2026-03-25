@extends('layouts.appAdmin')

@section('content')
<a href="{{ route('admin.create') }}">
    <button btn btn-primary m-3>
        Thêm nhân viên
    </button>
</a>

<table class="table table-bordered">
    <tr>
        <th>STT</th>
        <th>Họ tên</th>
        <th>Email</th>
        <th>chức vụ</th>
        <th></th>
        <th></th>
    </tr>
    @foreach($admins as $admin)
    <tr>
        <td>{{ $admin->adminID }}</td>
        <td>{{ $admin->fullName }}</td>
        <td>{{ $admin->email }}</td>
        <td>{{ $admin->role }}</td>
        <td>
            <a href="{{ route('admin.edit', $admin->adminID) }}">
                <button>
                    Sửa
                </button>
            </a>
        </td>
        <td>
            <form action="{{ route('admin.destroy', $admin->adminID) }}" method="POST">
                @csrf
                @method('DELETE')

                <button type="submit" onclick="return confirm('Bạn có chắc muốn xóa?')">
                    Xóa
                </button>
            </form>
        </td>
    </tr>
    @endforeach
</table>

<div class="mt-3">
    {{ $admins->links() }}
</div>
@endsection