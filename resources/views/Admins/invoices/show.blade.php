{{-- resources/views/admins/invoices/show.blade.php --}}
@extends('layouts.appAdmin')

@section('content')
<div class="container-fluid mt-4">
    {{-- Tiêu đề --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                Chi tiết hóa đơn #{{ $invoice->invoiceID }}
            </h4>
            <small class="text-muted">
                Thông tin chi tiết của hóa đơn
            </small>
        </div>

        <a href="{{ route('invoices.index') }}"
           class="btn btn-outline-secondary">
            ← Quay lại
        </a>
    </div>

    <div class="row">
        {{-- Thông tin hóa đơn --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-dark text-white">
                    Thông tin hóa đơn
                </div>

                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="40%">Mã hóa đơn:</th>
                            <td>#{{ $invoice->invoiceID }}</td>
                        </tr>

                        <tr>
                            <th>Khách hàng:</th>
                            <td>
                                {{ $invoice->customer->fullName ?? 'Khách vãng lai' }}
                            </td>
                        </tr>

                        <tr>
                            <th>Admin:</th>
                            <td>
                                {{ $invoice->admin->fullName ?? '---' }}
                            </td>
                        </tr>

                        <tr>
                            <th>Thanh toán:</th>
                            <td>
                                <span class="badge bg-success">
                                    {{ $invoice->paymentMethod->name ?? '---' }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <th>Ngày tạo:</th>
                            <td>
                                {{ \Carbon\Carbon::parse($invoice->createDate)->format('d/m/Y H:i') }}
                            </td>
                        </tr>

                        <tr>
                            <th>Tổng tiền:</th>
                            <td class="fw-bold text-danger fs-5">
                                {{ number_format($invoice->totalAmount, 0, ',', '.') }}đ
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Danh sách chi tiết hóa đơn --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-primary text-white">
                    Danh sách sản phẩm / vé
                </div>

                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Tên sản phẩm</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Đơn giá</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($invoice->invoiceDetails as $index => $detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>

                                    <td>
                                        {{ $detail->product->name
                                            ?? $detail->ticket->movie->movieName
                                            ?? '---' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $detail->quantity }}
                                    </td>

                                    <td class="text-end">
                                        {{ number_format($detail->unitPrice, 0, ',', '.') }}đ
                                    </td>

                                    <td class="text-end fw-bold text-danger">
                                        {{ number_format($detail->quantity * $detail->unitPrice, 0, ',', '.') }}đ
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5"
                                        class="text-center text-muted py-4">
                                        Không có chi tiết hóa đơn
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                        @if($invoice->invoiceDetails->count() > 0)
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">
                                        Tổng cộng:
                                    </th>
                                    <th class="text-end text-danger fs-6">
                                        {{ number_format($invoice->totalAmount, 0, ',', '.') }}đ
                                    </th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection