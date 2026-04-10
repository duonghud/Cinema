@extends('layouts.appAdmin')

@section('content')

<div class="container mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold">Quản lý hóa đơn đồ ăn</h4>

        <a href="{{ route('foodInvoice.create') }}" 
           class="btn btn-dark">
            + Tạo hóa đơn
        </a>
    </div>

    <!-- Alert -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-hover mb-0 align-middle">

                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Người mua</th>
                        <th>Thời gian</th>
                        <th>Thanh toán</th>
                        <th>Chi tiết</th>
                        <th>Tổng tiền</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($invoices as $foodInvoice)
                    <tr>

                        <!-- ID -->
                        <td class="text-muted">
                            #{{ $foodInvoice->foodInvoiceID }}
                        </td>

                        <!-- Customer -->
                        <td>
                            {{ $foodInvoice->customer->fullName ?? 'Khách vãng lai' }}
                        </td>

                        <!-- Time -->
                        <td>
                            {{ \Illuminate\Support\Carbon::parse($foodInvoice->orderDate)->format('d/m/Y H:i') }}
                        </td>

                        <!-- Payment -->
                        <td>
                            <span class="badge bg-light text-dark">
                                {{ $foodInvoice->payment->name ?? 'N/A' }}
                            </span>
                        </td>

                        <!-- Detail -->
                        <td>
                            <a href="{{ route('foodInvoice.show', $foodInvoice->foodInvoiceID) }}" 
                               class="btn btn-sm btn-outline-primary">
                                Xem
                            </a>
                        </td>

                        <!-- Total -->
                        <td class="fw-semibold">
                            {{ number_format($foodInvoice->total, 0, ',', '.') }} ₫
                        </td>

                        <!-- Actions -->
                        <td class="text-end">

                            <a href="{{ route('foodInvoice.edit', $foodInvoice->foodInvoiceID) }}" 
                               class="btn btn-sm btn-outline-dark me-2">
                                Sửa
                            </a>

                            <form action="{{ route('foodInvoice.destroy', $foodInvoice->foodInvoiceID) }}" 
                                  method="POST" 
                                  class="d-inline">
                                @csrf
                                @method('DELETE')

                                <button type="submit" 
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Bạn có chắc muốn xóa hóa đơn này?')">
                                    Xóa
                                </button>
                            </form>

                        </td>
                    </tr>
                    @endforeach

                    @if($invoices ->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Chưa có dữ liệu hóa đơn
                        </td>
                    </tr>
                    @endif
                </tbody>

            </table>

        </div>
    </div>

</div>

@endsection
