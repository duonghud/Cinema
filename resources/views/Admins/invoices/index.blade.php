@extends('layouts.appAdmin')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Quản lý hóa đơn</h4>
            <small class="text-muted">Danh sách tất cả hóa đơn trong hệ thống</small>
        </div>

        <a href="{{ route('invoices.create') }}" class="btn btn-dark shadow-sm">
            + Tạo hóa đơn
        </a>
    </div>

    @include('admins.partials.page-search', [
        'placeholder' => 'Tìm theo khách hàng, admin, thanh toán hoặc ngày tạo'
    ])

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#ID</th>
                        <th>Khách hàng</th>
                        <th>Admin</th>
                        <th>Thanh toán</th>
                        <th>Tổng tiền</th>
                        <th>Ngày</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($invoices as $inv)
                        <tr>
                            <td class="text-muted">#{{ $inv->invoiceID }}</td>
                            <td class="fw-medium">{{ $inv->customer->fullName }}</td>
                            <td>
                                <span class="badge bg-dark">{{ $inv->admin->fullName }}</span>
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $inv->payment->name }}</span>
                            </td>
                            <td class="fw-bold text-danger">{{ number_format($inv->totalAmount, 0, ',', '.') }}đ</td>
                            <td class="text-muted">{{ \Carbon\Carbon::parse($inv->createDate)->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('invoices.edit', $inv->invoiceID) }}" class="btn btn-sm btn-outline-dark me-2">
                                    Sửa
                                </a>

                                <form action="{{ route('invoices.destroy', $inv->invoiceID) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa hóa đơn này?')">
                                        Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Không có hóa đơn nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $invoices->links() }}
    </div>
</div>
@endsection
