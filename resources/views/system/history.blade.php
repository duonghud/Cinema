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

    .ticket-count-badge {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        background: rgba(220, 53, 69, 0.15);
        border: 1px solid rgba(220, 53, 69, 0.35);
        color: #fca5a5;
        border-radius: 999px;
        padding: .3rem .85rem;
        font-size: .9rem;
        font-weight: 700;
    }

    /* ── Modal chi tiết vé ── */
    .modal-ticket .modal-content {
        background: #0e1117;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 24px;
        color: #fff;
    }

    .modal-ticket .modal-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        padding: 1.5rem 1.75rem;
    }

    .modal-ticket .modal-footer {
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        padding: 1.25rem 1.75rem;
    }

    .modal-ticket .modal-body {
        padding: 1.75rem;
    }

    .ticket-stub {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        overflow: hidden;
    }

    .ticket-stub-header {
        background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%);
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: .75rem;
    }

    .ticket-stub-body {
        padding: 1.5rem;
    }

    .ticket-divider {
        position: relative;
        margin: 0;
        border: none;
        height: 1px;
        background: repeating-linear-gradient(
            to right,
            rgba(255,255,255,0.15) 0px,
            rgba(255,255,255,0.15) 8px,
            transparent 8px,
            transparent 16px
        );
    }

    /* Notches on the divider */
    .ticket-divider::before,
    .ticket-divider::after {
        content: '';
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #0e1117;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .ticket-divider::before { left: -10px; }
    .ticket-divider::after  { right: -10px; }

    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        padding: .65rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .detail-row:last-child { border-bottom: none; }

    .detail-row .label {
        color: #6b7280;
        font-size: .82rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .detail-row .value {
        font-weight: 600;
        text-align: right;
        word-break: break-word;
    }

    .seat-chip {
        display: inline-block;
        background: rgba(220, 53, 69, 0.18);
        border: 1px solid rgba(220, 53, 69, 0.4);
        color: #fca5a5;
        border-radius: 6px;
        padding: .1rem .5rem;
        font-size: .85rem;
        font-weight: 700;
        margin: 2px;
    }

    .btn-detail {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.14);
        color: #e5e7eb;
        border-radius: 999px;
        padding: .45rem 1.1rem;
        font-size: .9rem;
        font-weight: 600;
        transition: all .22s ease;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-detail:hover {
        background: rgba(220, 53, 69, 0.18);
        border-color: rgba(220, 53, 69, 0.55);
        color: #fca5a5;
    }

    .btn-detail i { font-size: 1rem; }
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
                        @php
                            $status = strtolower($invoice->status ?? 'paid');
                            $firstTicket = $invoice->tickets->first();
                            $movieTitle = optional(optional($firstTicket)->showTime)->movie->movieTitle ?? 'Chưa có thông tin phim';
                            $showDateTime = $firstTicket && $firstTicket->showTime
                                ? \Carbon\Carbon::parse($firstTicket->showTime->showDate . ' ' . $firstTicket->showTime->startTime)->format('d/m/Y H:i')
                                : 'N/A';
                            $seatLabels = $invoice->tickets
                                ->map(function ($ticket) {
                                    $seat = $ticket->seat;
                                    return $seat ? $seat->rowSeat . $seat->colSeat : null;
                                })
                                ->filter()
                                ->values();
                            $ticketCount = $invoice->tickets->count();

                            // Phòng chiếu (bỏ rạp vì chỉ có 1 rạp)
                            $roomName = optional(optional($firstTicket)->showTime)->room->roomName ?? 'N/A';

                            // Nhóm ghế theo loại + giá (quan hệ: seat->seatType)
                            $seatGroups = $invoice->tickets
                                ->filter(fn($t) => $t->seat && $t->seat->seatType)
                                ->groupBy(fn($t) => $t->seat->seatType->seatTypeID)
                                ->map(function ($group) {
                                    $type = $group->first()->seat->seatType;
                                    return [
                                        'name'  => $type->seatTypeName,
                                        'price' => $type->price,
                                        'count' => $group->count(),
                                        'seats' => $group->map(fn($t) => $t->seat->rowSeat . $t->seat->colSeat)->values(),
                                    ];
                                })
                                ->values();
                        @endphp

                        <div class="col-12">
                            <div class="history-card p-4">
                                <div class="row g-4 align-items-center">
                                    <div class="col-lg-3">
                                        <div class="info-label mb-2">Mã hóa đơn</div>
                                        <h4 class="fw-bold text-danger mb-0">
                                           INV-{{ $invoice->invoiceID }}
                                        </h4>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="info-label mb-2">Ngày đặt</div>
                                        <div class="fw-semibold">
                                            {{ \Carbon\Carbon::parse($invoice->createDate)->format('d/m/Y H:i') }}
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
                                        @if($status === 'paid')
                                            <span class="badge bg-success px-3 py-2">Đã thanh toán</span>
                                        @elseif($status === 'pending')
                                            <span class="badge bg-warning text-dark px-3 py-2">Chờ thanh toán</span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2">
                                                {{ $invoice->status }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <hr class="border-secondary my-4">

                                <div class="row g-3 mb-4">
                                    <div class="col-md-3">
                                        <div class="info-label mb-2">Phim</div>
                                        <div class="fw-semibold">{{ $movieTitle }}</div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="info-label mb-2">Suất chiếu</div>
                                        <div class="fw-semibold">{{ $showDateTime }}</div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="info-label mb-2">Ghế</div>
                                        <div class="fw-semibold">{{ $seatLabels->implode(', ') ?: 'N/A' }}</div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="info-label mb-2">Số vé</div>
                                        <span class="ticket-count-badge">
                                            <i class="bi bi-ticket-perforated-fill"></i>
                                            {{ $ticketCount }}
                                        </span>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-3">
                                    {{-- Nút xem chi tiết vé --}}
                                    <button class="btn-detail"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalTicket{{ $invoice->invoiceID }}">
                                        <i class="bi bi-eye"></i>
                                        Xem chi tiết
                                    </button>

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

{{-- ══════════════════════════════════════════════════════
     Modals chi tiết vé — đặt ngoài section để Bootstrap
     có thể mount overlay lên toàn trang đúng cách
     ══════════════════════════════════════════════════════ --}}
@if(isset($invoices) && $invoices->count() > 0)
    @foreach($invoices as $invoice)
        @php
            /* Tái tính các biến cần thiết cho modal */
            $m_status      = strtolower($invoice->status ?? 'paid');
            $m_firstTicket = $invoice->tickets->first();
            $m_movie       = optional(optional($m_firstTicket)->showTime)->movie->movieTitle ?? 'Chưa có thông tin phim';
            $m_showDT      = $m_firstTicket && $m_firstTicket->showTime
                ? \Carbon\Carbon::parse($m_firstTicket->showTime->showDate . ' ' . $m_firstTicket->showTime->startTime)->format('d/m/Y H:i')
                : 'N/A';
            $m_room        = optional(optional($m_firstTicket)->showTime)->room->roomName ?? 'N/A';
            $m_count       = $invoice->tickets->count();

            /* Nhóm ghế theo loại + giá */
            $m_groups = $invoice->tickets
                ->filter(fn($t) => $t->seat && $t->seat->seatType)
                ->groupBy(fn($t) => $t->seat->seatType->seatTypeID)
                ->map(function ($grp) {
                    $type = $grp->first()->seat->seatType;
                    return [
                        'name'  => $type->seatTypeName,
                        'price' => $type->price,
                        'count' => $grp->count(),
                        'seats' => $grp->map(fn($t) => $t->seat->rowSeat . $t->seat->colSeat)->values(),
                    ];
                })
                ->values();

            /* Fallback: danh sách ghế đơn giản nếu không có quan hệ seatType */
            $m_seatLabels = $invoice->tickets
                ->map(fn($t) => $t->seat ? $t->seat->rowSeat . $t->seat->colSeat : null)
                ->filter()->values();
        @endphp

        <div class="modal fade modal-ticket"
             id="modalTicket{{ (int) $invoice->invoiceID }}"
             tabindex="-1"
             aria-labelledby="modalLabel{{ (int) $invoice->invoiceID }}"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md modal-dialog-scrollable">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="modalLabel{{ (int) $invoice->invoiceID }}">
                            <i class="bi bi-ticket-perforated-fill text-danger me-2"></i>
                            Chi tiết vé — INV-{{ $invoice->invoiceID }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="ticket-stub">

                            {{-- Header: tên phim --}}
                            <div class="ticket-stub-header">
                                <i class="bi bi-film text-white fs-4"></i>
                                <div>
                                    <div style="font-size:.75rem;color:rgba(255,255,255,.6);text-transform:uppercase;letter-spacing:.06em;">Phim</div>
                                    <div class="fw-bold fs-6 text-white">{{ $m_movie }}</div>
                                </div>
                            </div>

                            {{-- Thông tin chung --}}
                            <div class="ticket-stub-body">
                                <div class="detail-row">
                                    <span class="label">Mã hóa đơn</span>
                                    <span class="value text-danger fw-bold">INV-{{ $invoice->invoiceID }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Ngày đặt</span>
                                    <span class="value">{{ \Carbon\Carbon::parse($invoice->createDate)->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Suất chiếu</span>
                                    <span class="value">{{ $m_showDT }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Phòng chiếu</span>
                                    <span class="value">{{ $m_room }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Số vé</span>
                                    <span class="value">{{ $m_count }} vé</span>
                                </div>
                            </div>

                            {{-- Bảng ghế & giá --}}
                            <div class="ticket-stub-body" style="padding-top:0;">
                                <div style="color:#6b7280;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.6rem;">
                                    Chi tiết ghế &amp; giá
                                </div>

                                @if($m_groups->isNotEmpty())
                                    <div style="border:1px solid rgba(255,255,255,0.07);border-radius:10px;overflow:hidden;">
                                        @foreach($m_groups as $grp)
                                            <div style="padding:.75rem 1rem;{{ !$loop->last ? 'border-bottom:1px solid rgba(255,255,255,0.06);' : '' }}">
                                                {{-- Dòng: tên loại + đơn giá × số lượng = thành tiền --}}
                                                <div style="display:flex;justify-content:space-between;align-items:baseline;gap:.5rem;flex-wrap:wrap;margin-bottom:.45rem;">
                                                    <span style="font-weight:700;color:#e5e7eb;font-size:.95rem;">
                                                        {{ $grp['name'] }}
                                                    </span>
                                                    <span style="white-space:nowrap;font-size:.88rem;">
                                                        <span style="color:#fbbf24;font-weight:600;">
                                                            {{ number_format($grp['price'], 0, ',', '.') }} đ
                                                        </span>
                                                        <span style="color:#6b7280;"> × {{ $grp['count'] }} = </span>
                                                        <span style="color:#fbbf24;font-weight:700;">
                                                            {{ number_format($grp['price'] * $grp['count'], 0, ',', '.') }} đ
                                                        </span>
                                                    </span>
                                                </div>
                                                {{-- Chip ghế --}}
                                                <div>
                                                    @foreach($grp['seats'] as $s)
                                                        <span class="seat-chip">{{ $s }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    {{-- Fallback khi chưa có quan hệ seatType --}}
                                    <div style="padding:.5rem 0;">
                                        @forelse($m_seatLabels as $s)
                                            <span class="seat-chip">{{ $s }}</span>
                                        @empty
                                            <span style="color:#6b7280;">N/A</span>
                                        @endforelse
                                    </div>
                                @endif
                            </div>

                            <hr class="ticket-divider mx-3">

                            {{-- Tổng tiền & trạng thái --}}
                            <div class="ticket-stub-body pt-3">
                                <div class="detail-row">
                                    <span class="label">Trạng thái</span>
                                    <span class="value">
                                        @if($m_status === 'paid')
                                            <span class="badge bg-success px-3 py-2">Đã thanh toán</span>
                                        @elseif($m_status === 'pending')
                                            <span class="badge bg-warning text-dark px-3 py-2">Chờ thanh toán</span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2">{{ $invoice->status }}</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Tổng tiền</span>
                                    <span class="value text-warning fs-5 fw-bold">
                                        {{ number_format($invoice->totalAmount ?? 0, 0, ',', '.') }} đ
                                    </span>
                                </div>
                            </div>

                        </div>{{-- /ticket-stub --}}
                    </div>

                    <div class="modal-footer gap-2">
                        <button type="button"
                                class="btn btn-outline-secondary rounded-pill px-4"
                                data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i>Đóng
                        </button>
                        <a href="{{ route('home') }}"
                           class="btn btn-danger rounded-pill px-4">
                            <i class="bi bi-ticket-perforated me-2"></i>Đặt vé lại
                        </a>
                    </div>

                </div>
            </div>
        </div>
    @endforeach
@endif

@endsection