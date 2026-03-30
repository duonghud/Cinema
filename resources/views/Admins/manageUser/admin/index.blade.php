@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold">Quản lý nhân viên</h4>

        <a href="{{ route('admin.create') }}"
            class="btn btn-dark">
            + Thêm nhân viên
        </a>
    </div>

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-hover mb-0 align-middle">

                <thead class="table-light">
                    <tr>
                        <th width="8%">ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Chức vụ</th>
                        <th class="text-end" width="25%">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($admins as $admin)
                    <tr>

                        <!-- ID -->
                        <td class="text-muted">
                            {{ $admin->adminID }}
                        </td>

                        <!-- Name -->
                        <td class="fw-medium">
                            {{ $admin->fullName }}
                        </td>

                        <!-- Email -->
                        <td>
                            {{ $admin->email }}
                        </td>

                        <!-- Role -->
                        <td>
                            @if($admin->role == 'admin')
                            <span class="badge bg-danger">Quản trị</span>
                            @elseif($admin->role == 'ticket_staff')
                            <span class="badge bg-primary">Bán vé</span>
                            @elseif($admin->role == 'food_staff')
                            <span class="badge bg-warning text-dark">Đồ ăn</span>
                            @else
                            <span class="badge bg-secondary">Khác</span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="text-end">

                            <a href="{{ route('admin.edit', $admin->adminID) }}"
                                class="btn btn-sm btn-outline-dark me-2">
                                Sửa
                            </a>

                            <form action="{{ route('admin.destroy', $admin->adminID) }}"
                                method="POST"
                                class="d-inline">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Bạn có chắc muốn xóa?')">
                                    Xóa
                                </button>
                            </form>

                        </td>
                    </tr>
                    @endforeach

                    @if($admins->isEmpty())
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            <i>Chưa có dữ liệu quản trị viên</i>
                        </td>
                    </tr>
                    @endif
                </tbody>

            </table>

        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $admins->links() }}
    </div>

</div>
@endsection