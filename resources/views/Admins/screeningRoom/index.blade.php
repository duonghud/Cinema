@extends('layouts.appAdmin')

@section('content')
<div class="container mt-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-semibold text-dark">Danh sách phòng chiếu</h3>

        <a href="{{ route('screeningRoom.create') }}" 
           class="btn btn-dark px-4">
            + Thêm phòng
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white border rounded-3 shadow-sm">
        <table class="table mb-0 align-middle">
            <thead class="border-bottom">
                <tr class="text-muted small">
                    <th>ID</th>
                    <th>Tên phòng</th>
                    <th>Sức chứa</th>
                    <th>Loại màn</th>
                    <th class="text-end">Hành động</th>
                </tr>
            </thead>

            <tbody>
            @foreach($room as $r)
                <tr class="border-bottom">
                    <td class="text-muted">{{ $r->roomID }}</td>

                    <td class="fw-medium">
                        {{ $r->roomName }}
                    </td>

                    <td>
                        {{ $r->capacity }}
                    </td>

                    <td>
                        @if($r->screenType)
                            <span class="text-secondary">
                                {{ $r->screenType->name }}
                            </span>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>

                    <td class="text-end">
                        <a href="{{ route('screeningRoom.edit',$r->roomID) }}" 
                           class="btn btn-sm btn-outline-dark me-2">
                            Sửa
                        </a>

                        <form action="{{ route('screeningRoom.destroy',$r->roomID) }}" 
                              method="POST" 
                              class="d-inline">

                            @csrf
                            @method('DELETE')

                            <button class="btn btn-sm btn-outline-danger">
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
@endsection