@extends('layouts.appAdmin')

@section('title', 'Chi tiết hóa đơn đồ ăn')
@section('page-title', 'Chi tiết hóa đơn đồ ăn')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h4 class="fw-semibold mb-1">Hóa đơn #{{ $invoice->foodInvoiceID }}</h4>
                <p class="text-muted mb-0">Xem chi tiết món ăn, số lượng và tổng thanh toán của hóa đơn.</p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('foodInvoice.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại
                </a>
                <a href="{{ route('foodInvoice.edit', $invoice->foodInvoiceID) }}" class="btn btn-dark">
                    <i class="bi bi-pencil-square me-1"></i> Sửa hóa đơn
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Danh sách món ăn</h5>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>Món ăn</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Đơn giá</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($detailRows as $detail)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $detail['foodName'] }}</div>
                                        </td>
                                        <td class="text-center">{{ $detail['quantity'] }}</td>
                                        <td class="text-end">{{ number_format($detail['unitPrice'], 0, ',', '.') }} ₫</td>
                                        <td class="text-end fw-semibold">{{ number_format($detail['subtotal'], 0, ',', '.') }} ₫</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            Hóa đơn này chưa có món ăn nào.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Tổng cộng</th>
                                    <th class="text-end text-danger">
                                        {{ number_format($displayTotal, 0, ',', '.') }} ₫
                                    </th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Thông tin hóa đơn</h5>

                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Mã hóa đơn</span>
                            <strong>#{{ $invoice->foodInvoiceID }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Khách hàng</span>
                            <strong>{{ $invoice->customer->fullName ?? 'Khách vãng lai' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Phương thức thanh toán</span>
                            <strong>{{ $invoice->payment->name ?? 'N/A' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Thời gian đặt</span>
                            <strong>{{ $formattedOrderDate }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted">Tổng thanh toán</span>
                            <strong class="text-danger">{{ number_format($invoice->total, 0, ',', '.') }} ₫</strong>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Tóm tắt</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Số món</span>
                            <strong>{{ $detailCount }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Tổng số lượng</span>
                            <strong>{{ $totalQuantity }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection