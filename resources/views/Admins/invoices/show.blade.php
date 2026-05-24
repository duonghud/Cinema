@extends('layouts.appAdmin')

@section('title', 'Chi tiết hóa đơn')
@section('page-title', 'Chi tiết hóa đơn')

@push('css')
<style>
    .invoice-detail-page {
        max-width: 1120px;
        margin: 0 auto;
    }

    .invoice-hero {
        border: 0;
        border-radius: 24px;
        padding: 28px;
        color: #fff;
        background:
            radial-gradient(circle at top right, rgba(255, 255, 255, 0.24), transparent 34%),
            linear-gradient(135deg, #151a2d 0%, #28375f 55%, #4f46e5 100%);
        box-shadow: 0 22px 55px rgba(26, 35, 74, 0.22);
    }

    .invoice-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.14);
        color: rgba(255, 255, 255, 0.94);
        font-size: 0.92rem;
    }

    .invoice-stat {
        height: 100%;
        border: 0;
        border-radius: 22px;
        background: #fff;
        box-shadow: 0 16px 38px rgba(17, 24, 39, 0.08);
    }

    .invoice-stat .label,
    .invoice-section .section-label {
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #8b95a7;
    }

    .invoice-stat .value {
        color: #111827;
        font-size: 1.35rem;
        font-weight: 700;
    }

    .invoice-section {
        border: 0;
        border-radius: 22px;
        background: #fff;
        box-shadow: 0 16px 38px rgba(17, 24, 39, 0.08);
    }

    .detail-item {
        padding: 16px 0;
        border-bottom: 1px solid #edf1f7;
    }

    .detail-item:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }

    .detail-item:first-child {
        padding-top: 0;
    }

    .detail-item .detail-label {
        color: #7c8798;
        font-size: 0.92rem;
        margin-bottom: 6px;
    }

    .detail-item .detail-value {
        color: #111827;
        font-size: 1rem;
        font-weight: 600;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 999px;
        font-weight: 700;
    }

    .status-pill.status-paid {
        color: #166534;
        background: #dcfce7;
    }

    .status-pill.status-pending {
        color: #9a3412;
        background: #ffedd5;
    }

    .ticket-summary {
        padding: 18px 20px;
        border-radius: 18px;
        background: linear-gradient(180deg, #f8faff 0%, #eef3ff 100%);
        border: 1px solid #e2e8f0;
    }

    .ticket-summary strong {
        color: #111827;
    }

    @media (max-width: 767.98px) {
        .invoice-hero {
            padding: 22px;
        }
    }
</style>
@endpush

@section('content')
@php
    $tickets = $invoice->tickets ?? collect();
    $firstTicket = $tickets->first();
    $seatLabels = $tickets
        ->map(function ($ticket) {
            $seat = $ticket->seat;

            if (!$seat) {
                return null;
            }

            return trim(($seat->rowSeat ?? '') . ($seat->colSeat ?? ''));
        })
        ->filter()
        ->unique()
        ->values();

    $showtimes = $tickets
        ->map(function ($ticket) {
            $showTime = $ticket->showTime;

            if (!$showTime) {
                return null;
            }

            return trim(($showTime->showDate ?? '') . ' ' . ($showTime->startTime ?? ''));
        })
        ->filter()
        ->unique()
        ->values();

    $status = strtolower((string) ($firstTicket->status ?? $invoice->status ?? 'paid'));
    $statusClass = $status === 'booked' || $status === 'paid' ? 'status-paid' : 'status-pending';
    $statusText = $status === 'booked' || $status === 'paid' ? 'Đã thanh toán' : 'Chưa hoàn tất';
@endphp

<div class="container-fluid mt-4">
    <div class="invoice-detail-page">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h4 class="fw-bold mb-1">Chi tiết hóa đơn #{{ $invoice->invoiceID }}</h4>
                <div class="text-muted">Theo dõi nhanh thông tin giao dịch và dữ liệu đặt chỗ trong một màn hình.</div>
            </div>

            <a href="{{ route('invoices.index') }}" class="btn btn-outline-dark rounded-pill px-4">
                <i class="bi bi-arrow-left me-2"></i>Quay lại
            </a>
        </div>

        <div class="invoice-hero mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-4">
                <div>
                    <div class="invoice-chip mb-3">
                        <i class="bi bi-receipt-cutoff"></i>
                        Mã hóa đơn #{{ $invoice->invoiceID }}
                    </div>
                    <h2 class="fw-bold mb-2">Tổng tiền {{ number_format($invoice->totalAmount ?? 0, 0, ',', '.') }} đ</h2>
                    <p class="mb-0 text-white-50">
                        Tạo lúc {{ \Carbon\Carbon::parse($invoice->createDate)->format('d/m/Y H:i') }}
                    </p>
                </div>

                <div class="text-md-end">
                    <div class="status-pill {{ $statusClass }}">
                        <i class="bi bi-check-circle-fill"></i>
                        {{ $statusText }}
                    </div>
                    <div class="mt-3 text-white-50">
                        Thanh toán qua <strong class="text-white">{{ $invoice->paymentMethod->name ?? 'Chưa cập nhật' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-4">
                <div class="invoice-stat card p-4">
                    <div class="label mb-2">Khách hàng</div>
                    <div class="value mb-2">{{ $invoice->customer->fullName ?? 'Khách vãng lai' }}</div>
                    <div class="text-muted small">{{ $invoice->customer->email ?? 'Không có email' }}</div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="invoice-stat card p-4">
                    <div class="label mb-2">Nhân viên phụ trách</div>
                    <div class="value mb-2">{{ $invoice->admin->fullName ?? 'Chưa cập nhật' }}</div>
                    <div class="text-muted small">Ghi nhận và xử lý giao dịch</div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="invoice-stat card p-4">
                    <div class="label mb-2">Số lượng vé</div>
                    <div class="value mb-2">{{ $tickets->count() }}</div>
                    <div class="text-muted small">Ghế: {{ $seatLabels->isNotEmpty() ? $seatLabels->implode(', ') : 'Chưa có dữ liệu' }}</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="invoice-section card p-4 h-100">
                    <div class="section-label mb-3">Thông tin giao dịch</div>

                    <div class="detail-item">
                        <div class="detail-label">Phim</div>
                        <div class="detail-value">{{ optional(optional($firstTicket)->showTime)->movie->movieTitle ?? 'Chưa có thông tin phim' }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Suất chiếu</div>
                        <div class="detail-value">{{ $showtimes->isNotEmpty() ? $showtimes->implode(' | ') : 'Chưa có dữ liệu suất chiếu' }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Ghế đã đặt</div>
                        <div class="detail-value">{{ $seatLabels->isNotEmpty() ? $seatLabels->implode(', ') : 'Chưa có dữ liệu ghế' }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Phương thức thanh toán</div>
                        <div class="detail-value">{{ $invoice->paymentMethod->name ?? 'Chưa cập nhật' }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Ngày tạo hóa đơn</div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($invoice->createDate)->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="invoice-section card p-4 h-100">
                    <div class="section-label mb-3">Tóm tắt đặt vé</div>

                    <div class="ticket-summary mb-3">
                        <div class="mb-2 text-muted small">Thông tin nổi bật</div>
                        <div class="fw-semibold mb-1">
                            {{ optional(optional($firstTicket)->showTime)->movie->movieTitle ?? 'Chưa có thông tin phim' }}
                        </div>
                        <div class="text-muted small">
                            {{ $showtimes->isNotEmpty() ? $showtimes->implode(' | ') : 'Chưa có dữ liệu suất chiếu' }}
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Mã giao dịch</div>
                        <div class="detail-value">INV-{{ $invoice->invoiceID }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Tổng thanh toán</div>
                        <div class="detail-value text-danger">{{ number_format($invoice->totalAmount ?? 0, 0, ',', '.') }} đ</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Trạng thái</div>
                        <div class="detail-value">{{ $statusText }}</div>
                    </div>

                    {{--<div class="pt-4">
                        <a href="{{ route('invoices.edit', $invoice->invoiceID) }}" class="btn btn-dark rounded-pill px-4">
                            <i class="bi bi-pencil-square me-2"></i>Chỉnh sửa hóa đơn
                        </a>
                    </div>--}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
