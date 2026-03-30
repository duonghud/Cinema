@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold mb-0">Quản lý nhà sản xuất</h4>

        <a href="{{ route('studio.create') }}" class="btn btn-dark">
            + Thêm nhà sản xuất
        </a>
    </div>

    <!-- Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">

            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:80px">ID</th>
                        <th>Tên nhà sản xuất</th>
                        <th class="text-end" style="width:180px">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($studios as $studio)
                    <tr>
                        <td class="fw-semibold">{{ $studio->studioID }}</td>
                        <td>{{ $studio->name }}</td>

                        <!-- Actions -->
                        <td class="text-end">

                            <a href="{{ route('studio.edit', $studio->studioID) }}"
                                class="btn btn-sm btn-outline-dark me-2">
                                Sửa
                            </a>

                            <form action="{{ route('studio.destroy', $studio->studioID) }}" method="POST" class="d-inline">
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
                    @if ($studios -> isEmpty())
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            Chưa có dữ liệu hãng sản xuất
                        </td>
                    </tr>
                    @endif
            </table>

        </div>
    </div>

</div>
@endsection