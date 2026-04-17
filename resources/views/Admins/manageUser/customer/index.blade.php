@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold">Quản lý khách hàng</h4>

        <!-- <a href="#" 
           class="btn btn-dark">
            + Thêm khách hàng
        </a> -->
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
                        <th>SĐT</th>
                        <th>Địa chỉ</th>
                        <th class="text-end" width="25%">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($customers as $customer)
                        <tr>

                            <!-- ID -->
                            <td class="text-muted">
                                {{ $customer->customerID }}
                            </td>

                            <!-- Name -->
                            <td class="fw-medium">
                                {{ $customer->fullName }}
                            </td>

                            <!-- Email -->
                            <td>
                                {{ $customer->email }}
                            </td>

                            <!-- Phone -->
                            <td>
                                {{ $customer->phoneNumber }}
                            </td>

                            <!-- Address -->
                            <td>
                                {{ $customer->address }}
                            </td>

                            <!-- Actions -->
                            <td class="text-end">

                                <a href="{{ route('customer.edit', $customer->customerID) }}"
                                   class="btn btn-sm btn-outline-dark me-2">
                                    Sửa
                                </a>

                                <!-- <form action="{{ route('customer.destroy', $customer->customerID) }}" 
                                      method="POST" 
                                      class="d-inline"> 
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Bạn có chắc muốn xóa?')">
                                        Xóa
                                    </button>
                                </form> -->

                            </td>
                        </tr>
                    @endforeach

                    @if ($customers -> isEmpty())
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Chưa có dữ liệu khách hàng
                        </td>
                    </tr>
                    @endif
                </tbody>

            </table>

        </div>
    </div>

</div>
@endsection