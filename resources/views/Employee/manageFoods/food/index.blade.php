@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold">Quản lý đồ ăn</h4>

        <a href="{{ route('food.create') }}"
           class="btn btn-dark">
            + Thêm món ăn
        </a>
    </div>

    <!-- Table -->
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
                    @foreach($foods as $food)
                        <tr>

                            <!-- ID -->
                            <td class="text-muted">
                                #{{ $food->foodID }}
                            </td>

                            <!-- Name -->
                            <td class="fw-medium">
                                {{ $food->foodName }}
                            </td>

                            <!-- Size -->
                            <td>
                                {{ $food->size }}
                            </td>

                            <!-- Price -->
                            <td>
                                {{ number_format($food->price, 0, ',', '.') }}đ
                            </td>

                            <!-- Type -->
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ $food->foodType }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="text-end">

                                <a href="{{ route('food.edit', $food->foodID) }}"
                                   class="btn btn-sm btn-outline-dark me-2">
                                    Sửa
                                </a>

                                <form action="{{ route('food.destroy', $food->foodID) }}"
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
                </tbody>

            </table>

        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $foods->links() }}
    </div>

</div>
@endsection
