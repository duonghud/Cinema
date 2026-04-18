@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold">Quản lý đồ ăn</h4>

        <a href="{{ route('food.create') }}" class="btn btn-dark">
            + Thêm món ăn
        </a>
    </div>

    @include('admins.partials.page-search', [
        'placeholder' => 'Tìm theo tên món, loại, size hoặc giá'
    ])

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="10%">ID</th>
                        <th>Tên đồ ăn</th>
                        <th>Size</th>
                        <th>Giá</th>
                        <th>Loại</th>
                        <th class="text-end" width="25%">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($foods as $food)
                        <tr>
                            <td class="text-muted">{{ $food->foodID }}</td>
                            <td class="fw-medium">{{ $food->foodName }}</td>
                            <td>{{ $food->size }}</td>
                            <td>{{ number_format($food->price, 0, ',', '.') }}đ</td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $food->foodType }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('food.edit', $food->foodID) }}" class="btn btn-sm btn-outline-dark me-2">
                                    Sửa
                                </a>

                                <form action="{{ route('food.destroy', $food->foodID) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">
                                        Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Chưa có dữ liệu đồ ăn
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $foods->links() }}
    </div>
</div>
@endsection
