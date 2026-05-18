@extends('layouts.app')

@section('title', 'Lịch sử đặt vé')

@section('content')
<style>
    .history-page {
        background:
            radial-gradient(circle at top left, rgba(217, 63, 64, 0.25), transparent 35%),
            linear-gradient(180deg, #0b0d13 0%, #111827 45%, #0b0d13 100%);
        min-height: 100vh;
        color: #fff;
    }

    .glass-panel {
        background: rgba(12, 16, 24, 0.82);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(18px);
        border-radius: 28px;
    }

    .history-card {
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        transition: all .3s ease;
    }

    .history-card:hover {
        transform: translateY(-4px);
        border-color: rgba(248, 113, 113, 0.35);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
    }

    .info-label {
        color: #9ca3af;
        font-size: .85rem;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-icon {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        margin: 0 auto 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(248, 113, 113, 0.15);
        color: #fca5a5;
        font-size: 2.5rem;
    }

    .pagination .page-link {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #fff;
    }

    .pagination .page-item.active .page-link {
        background: #dc3545;
        border-color: #dc3545;
    }

    .pagination .page-link:hover {
        background: rgba(220, 53, 69, 0.15);
        color: #fff;
    }
</style>

<section class="history-page py-5 px-3">
    <div class="container">
        <div class="glass-panel p-4 p-md-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h1 class="fw-bold mb-0">
                        <i class="bi bi-clock-history me-2 text-danger"></i>
                        Lịch sử đặt vé
                    </h1>
                </div>

                <a href="{{ route('customer.profile') }}"
                   class="btn btn-outline-light rounded-pill px-4">
                    <i class="bi bi-arrow-left me-2"></i>Quay lại hồ sơ
                </a>
            </div>

            @if($invoices->count() > 0)
                <div class="row g-4">
                    @foreach($invoices as $invoice)
                        <div class="col-12">
                            <div class="history-card p-4">
                                <div class="row g-4 align-items-center">
                                    <div class="col-lg-3">
                                        <div class="info-label mb-2">Mã hóa đơn</div>
                                        <h4 class="fw-bold text-danger mb-0">
                                            #{{ $invoice->invoiceID }}
                                        </h4>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="info-label mb-2">Ngày đặt</div>
                                        <div class="fw-semibold">
                                            {{ \Carbon\Carbon::parse($invoice->created_at)->format('d/m/Y H:i') }}
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="info-label mb-2">Tổng tiền</div>
                                        <div class="fw-bold text-warning fs-5">
                                            {{ number_format($invoice->totalAmount ?? 0, 0, ',', '.') }} đ
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="info-label mb-2">Trạng thái</div>
                                        @php
                                            $status = strtolower($invoice->status ?? 'paid');
                                        @endphp

                                        @if($status === 'paid')
                                            <span class="badge bg-success px-3 py-2">
                                                Đã thanh toán
                                            </span>
                                        @elseif($status === 'pending')
                                            <span class="badge bg-warning text-dark px-3 py-2">
                                                Chờ thanh toán
                                            </span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2">
                                                {{ $invoice->status }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <hr class="border-secondary my-4">

                                <div class="d-flex flex-wrap gap-3">
                                    <a href="{{ route('invoice') }}?id={{ $invoice->invoiceID }}"
                                       class="btn btn-outline-light rounded-pill px-4">
                                        <i class="bi bi-receipt me-2"></i>Xem hóa đơn
                                    </a>

                                    <a href="{{ route('home') }}"
                                       class="btn btn-danger rounded-pill px-4">
                                        <i class="bi bi-ticket-perforated me-2"></i>Đặt vé lại
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 d-flex justify-content-center">
                    {{ $invoices->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="bi bi-ticket-perforated"></i>
                    </div>

                    <h3 class="fw-bold mb-3">Chưa có lịch sử đặt vé</h3>
                    <p class="text-white-50 mb-4">
                        Bạn chưa thực hiện giao dịch nào. Hãy khám phá các bộ phim đang chiếu và đặt vé ngay.
                    </p>

                    <a href="{{ route('home') }}"
                       class="btn btn-danger rounded-pill px-5 py-3 fw-semibold">
                        <i class="bi bi-film me-2"></i>Đặt vé ngay
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection