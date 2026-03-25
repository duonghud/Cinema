@extends('layouts.appAdmin')

@section('content')


<a href="{{ route('ageRating.create') }}" class="btn btn-success mb-3">
    Thêm kiểm duyệt
</a>

<table class="table table-bordered" width="600">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tên</th>
            <th>Mô tả</th>
            <th>Hành động</th>
        </tr>
    </thead>

    <tbody>

        @forelse($ageRatings as $item)

        <tr>
            <td>{{ $item->ageRatingID }}</td>
            <td>{{ $item->code }}</td>
            <td>{{ $item->description }}</td>

            <td>

                <a href="{{ route('ageRating.edit',$item) }}"
                    class="btn btn-warning btn-sm">
                    Sửa
                </a>

                <form action="{{ route('ageRating.destroy',$item) }}"
                    method="POST"
                    style="display:inline-block">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                        class="btn btn-danger btn-sm"
                        onclick="return confirm('Bạn có chắc muốn xóa?')">
                        Xóa
                    </button>

                </form>

            </td>
        </tr>

        @empty

        <tr>
            <td colspan="4" class="text-center">
                Không có dữ liệu
            </td>
        </tr>

        @endforelse

    </tbody>

</table>

@endsection