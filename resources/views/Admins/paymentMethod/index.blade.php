@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold">Quản lý phương thức thanh toán</h4>

        <a href="{{ route('paymentMethod.create') }}" 
           class="btn btn-dark">
            + Thêm phương thức
        </a>
    </div>

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-hover mb-0 align-middle">

                <thead class="table-light">
                    <tr>
                        <th width="10%">ID</th>
                        <th>Tên phương thức</th>
                        <th class="text-end" width="25%">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($paymentMethods as $paymentMethod)
                        <tr>

                            <!-- ID -->
                            <td class="text-muted">
                                {{ $paymentMethod->paymentID }}
                            </td>

                            <!-- Name -->
                            <td class="fw-medium">
                                <span class="badge bg-light text-dark">
                                    {{ $paymentMethod->name }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="text-end">

                                <a href="{{ route('paymentMethod.edit', $paymentMethod->paymentID) }}"
                                   class="btn btn-sm btn-outline-dark me-2">
                                    Sửa
                                </a>

                                <form action="{{ route('paymentMethod.destroy', $paymentMethod->paymentID) }}" 
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

</div>
@endsection