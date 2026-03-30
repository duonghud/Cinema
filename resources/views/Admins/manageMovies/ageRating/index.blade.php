@extends('layouts.appAdmin')

@section('content')

<div class="container mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-semibold mb-0">Danh sách kiểm duyệt độ tuổi</h4>
            <small class="text-muted">Quản lý phân loại phim theo độ tuổi</small>
        </div>

        <a href="{{ route('ageRating.create') }}" class="btn btn-dark">
            + Thêm kiểm duyệt
        </a>
    </div>

    <!-- Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th width="80">ID</th>
                        <th width="120">Mã</th>
                        <th>Mô tả</th>
                        <th width="180" class="text-end">Hành động</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($ageRatings as $item)
                    <tr>

                        <!-- ID -->
                        <td class="text-muted">
                            {{ $item->ageRatingID }}
                        </td>

                        <!-- Code -->
                        <td>
                            <span class="badge bg-warning text-dark px-3 py-2">
                                {{ $item->code }}
                            </span>
                        </td>

                        <!-- Description -->
                        <td>
                            <span class="text-secondary">
                                {{ $item->description }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="text-end">

                            <a href="{{ route('ageRating.edit',$item) }}"
                                class="btn btn-sm btn-outline-dark me-2">
                                Sửa
                            </a>

                            <form action="{{ route('ageRating.destroy',$item) }}"
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
                    @if ($ageRatings -> isEmpty())
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            Chưa có dữ liệu kiểm duyệt
                        </td>
                    </tr>
                    @endif                    
                </tbody>

            </table>

        </div>
    </div>

</div>

@endsection