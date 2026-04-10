@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold">Quản lý định dạng màn hình</h4>

        <a href="{{ route('screenType.create') }}" 
           class="btn btn-dark">
            + Thêm định dạng
        </a>
    </div>

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-hover mb-0 align-middle">

                <thead class="table-light">
                    <tr>
                        <th width="10%">ID</th>
                        <th>Tên định dạng</th>
                        <th class="text-end" width="25%">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($screenTypes as $screenType)
                        <tr>

                            <!-- ID -->
                            <td class="text-muted">
                                {{ $screenType->screenTypeID }}
                            </td>

                            <!-- Name -->
                            <td class="fw-medium">
                                <span class="badge bg-light text-dark">
                                    {{ $screenType->name }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="text-end">

                                <a href="{{ route('screenType.edit', $screenType->screenTypeID) }}"
                                   class="btn btn-sm btn-outline-dark me-2">
                                    Sửa
                                </a>

                                <form action="{{ route('screenType.destroy', $screenType->screenTypeID) }}" 
                                      method="POST" 
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger">
                                        Xóa
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @endforeach

                    @if ($screenTypes -> isEmpty())
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">
                            Chưa có dữ liệu định dạng màn hình
                        </td>
                    </tr>
                    @endif
                </tbody>

            </table>

        </div>
    </div>

</div>
@endsection