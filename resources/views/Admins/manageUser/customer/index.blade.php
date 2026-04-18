@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold">Quản lý khách hàng</h4>
    </div>

    @include('admins.partials.page-search', [
        'placeholder' => 'Tìm theo tên, email, số điện thoại hoặc địa chỉ'
    ])

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
                    @forelse($customers as $customer)
                        <tr>
                            <td class="text-muted">{{ $customer->customerID }}</td>
                            <td class="fw-medium">{{ $customer->fullName }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phoneNumber }}</td>
                            <td>{{ $customer->address }}</td>
                            <td class="text-end">
                                <a href="{{ route('customer.edit', $customer->customerID) }}" class="btn btn-sm btn-outline-dark me-2">
                                    Sửa
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Chưa có dữ liệu khách hàng
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $customers->links() }}
    </div>
</div>
@endsection
