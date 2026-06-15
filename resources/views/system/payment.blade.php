=@extends('layouts.app')

@section('content')
<style>
    body {
        background: #020817;
        font-family: Arial, sans-serif;
    }

    /* ─── Layout ─────────────────────────────────────── */
    .invoice-wrapper {
        min-height: 100vh;
        padding: 12px 24px;
    }

    .invoice-container {
        max-width: 1280px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 390px;
        gap: 24px;
    }

    .left-content {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* ─── Card ───────────────────────────────────────── */
    .card {
        background: #111827;
        border: 1px solid #1e293b;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    }

    .card-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 24px;
        color: #fff;
    }

    /* ─── Movie info grid ────────────────────────────── */
    .movie-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 32px 64px;
    }

    .label {
        font-size: 14px;
        color: #94a3b8;
        margin-bottom: 4px;
    }

    .value {
        font-size: 22px;
        font-weight: 700;
        color: #ffffff;
    }

    .value.uppercase {
        text-transform: uppercase;
    }

    .value.highlight {
        color: #fb923c;
    }

    /* ─── Table ──────────────────────────────────────── */
    .table-wrapper {
        border: 1px solid #475569;
        border-radius: 18px;
        overflow: hidden;
    }

    .payment-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .payment-table th,
    .payment-table td {
        padding: 16px 20px;
        text-align: left;
    }

    .payment-table thead tr {
        border-bottom: 1px solid #475569;
    }

    .payment-table th {
        font-weight: 700;
        color: #94a3b8;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .payment-table tbody tr {
        border-bottom: 1px solid #1e293b;
        transition: background 0.2s ease;
    }

    .payment-table tbody tr:last-child {
        border-bottom: none;
    }

    .payment-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.02);
    }

    .payment-table td {
        font-weight: 600;
        color: #fff;
        vertical-align: middle;
    }

    .payment-table tfoot tr {
        border-top: 1px solid #475569;
        background: rgba(255, 255, 255, 0.02);
    }

    .payment-table tfoot td {
        font-weight: 700;
        color: #fff;
        font-size: 15px;
        padding: 18px 20px;
    }

    /* ─── Seat codes ─────────────────────────────────── */
    .seat-codes {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .seat-code-chip {
        display: inline-block;
        padding: 2px 8px;
        background: rgba(148, 163, 184, 0.1);
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        color: #cbd5e1;
        letter-spacing: 0.5px;
    }

    /* ─── Seat type badges ───────────────────────────── */
    .seat-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .seat-type-badge .badge-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .badge-normal {
        background: rgba(99, 102, 241, 0.12);
        color: #818cf8;
        border: 1px solid rgba(99, 102, 241, 0.3);
    }
    .badge-normal .badge-dot { background: #818cf8; }

    .badge-vip {
        background: rgba(251, 146, 60, 0.12);
        color: #fb923c;
        border: 1px solid rgba(251, 146, 60, 0.3);
    }
    .badge-vip .badge-dot { background: #fb923c; }

    .badge-couple {
        background: rgba(236, 72, 153, 0.12);
        color: #f472b6;
        border: 1px solid rgba(236, 72, 153, 0.3);
    }
    .badge-couple .badge-dot { background: #f472b6; }

    .badge-default {
        background: rgba(148, 163, 184, 0.1);
        color: #94a3b8;
        border: 1px solid rgba(148, 163, 184, 0.2);
    }
    .badge-default .badge-dot { background: #94a3b8; }

    /* ─── Price cells ────────────────────────────────── */
    .price-unit     { color: #94a3b8; font-weight: 600; font-size: 14px; }
    .price-subtotal { color: #fb923c; font-weight: 700; font-size: 15px; }
    .price-total-cell { color: #fb923c; font-weight: 800; font-size: 18px; }

    /* ─── Sidebar ─────────────────────────────────────── */
    .sidebar {
        position: sticky;
        top: 16px;
        height: fit-content;
    }

    /* ─── Payment Methods ─────────────────────────────── */
    .payment-methods {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 28px;
    }

    .payment-option input[type="radio"] { display: none; }

    .payment-card {
        position: relative;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        border: 1px solid rgba(71, 85, 105, 0.7);
        border-radius: 18px;
        background: linear-gradient(90deg, #171d29, #1c2431);
        cursor: pointer;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .payment-card:hover {
        border-color: rgba(248, 113, 113, 0.7);
        box-shadow: 0 0 30px rgba(239, 68, 68, 0.08);
    }

    .payment-card::after {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 18px;
        background: radial-gradient(circle at right, rgba(239, 68, 68, 0.12), transparent 55%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .payment-option input:checked + .payment-card {
        border-color: #ef4444;
        background: linear-gradient(90deg, #24181f, #2a1d24);
        box-shadow: 0 0 40px rgba(239, 68, 68, 0.15);
    }

    .payment-option input:checked + .payment-card::after { opacity: 1; }

    .payment-radio {
        position: relative;
        z-index: 2;
        width: 24px;
        height: 24px;
        border: 2px solid #475569;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .payment-option input:checked + .payment-card .payment-radio {
        border-color: #ef4444;
        background: rgba(239, 68, 68, 0.12);
    }

    .payment-check {
        width: 6px;
        height: 12px;
        border-right: 2px solid #ef4444;
        border-bottom: 2px solid #ef4444;
        transform: rotate(45deg) scale(0);
        opacity: 0;
        transition: all 0.3s ease;
        margin-top: -2px;
    }

    .payment-option input:checked + .payment-card .payment-check {
        transform: rotate(45deg) scale(1);
        opacity: 1;
    }

    .payment-logo {
        position: relative;
        z-index: 2;
        font-size: 14px;
        font-weight: 800;
        white-space: nowrap;
    }

    .logo-vnpay .blue { color: #3b82f6; }
    .logo-vnpay .red  { color: #ef4444; }
    .logo-momo        { color: #ec4899; text-transform: lowercase; }
    .logo-vietqr .red  { color: #ef4444; }
    .logo-vietqr .cyan { color: #22d3ee; }
    .logo-visa        { color: #d1d5db; }

    .payment-name {
        position: relative;
        z-index: 2;
        font-size: 16px;
        font-weight: 600;
        color: #ffffff;
    }

    /* ─── Cost summary ───────────────────────────────── */
    .cost-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 14px;
        color: #fff;
    }

    .cost-divider {
        border: none;
        border-top: 1px dashed #334155;
        margin: 12px 0;
    }

    .cost-type-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        font-size: 13px;
        color: #cbd5e1;
    }

    .cost-type-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .cost-type-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .dot-normal  { background: #818cf8; }
    .dot-vip     { background: #fb923c; }
    .dot-couple  { background: #f472b6; }
    .dot-default { background: #94a3b8; }

    .cost-type-name { font-weight: 600; font-size: 13px; }
    .cost-type-qty  { color: #64748b; font-size: 12px; margin-left: 2px; }
    .cost-type-amount { font-weight: 700; color: #e2e8f0; font-size: 13px; }

    .cost-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 4px;
        padding-top: 4px;
    }

    .cost-total-label  { font-size: 14px; font-weight: 700; color: #fff; }
    .cost-total-amount { font-size: 22px; font-weight: 800; color: #fb923c; }

    /* ─── Buttons ────────────────────────────────────── */
    .btn-submit {
        width: 100%;
        padding: 14px 32px;
        border: none;
        border-radius: 999px;
        background: linear-gradient(90deg, #ff4458, #ff7a67);
        color: #ffffff;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 0 35px rgba(239, 68, 68, 0.25);
        transition: all 0.3s ease;
        margin-top: 4px;
    }

    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 0 45px rgba(239, 68, 68, 0.35);
    }

    .btn-back {
        width: 100%;
        margin-top: 16px;
        background: none;
        border: none;
        color: #ffffff;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: color 0.3s ease;
        padding: 8px;
    }

    .btn-back:hover { color: #f87171; }

    /* ─── Responsive ─────────────────────────────────── */
    @media (max-width: 1280px) {
        .invoice-container { grid-template-columns: 1fr; }
        .sidebar { position: static; }
    }

    @media (max-width: 768px) {
        .invoice-wrapper { padding: 12px; }
        .card { padding: 20px; }
        .card-title { font-size: 24px; }
        .movie-grid { grid-template-columns: 1fr; gap: 24px; }
        .value { font-size: 18px; }
        .col-unit-price { display: none; }
    }
</style>

<div class="invoice-wrapper">
    <div class="invoice-container">

        {{-- ── LEFT CONTENT ─────────────────────────── --}}
        <div class="left-content">

            {{-- THÔNG TIN PHIM --}}
            <div class="card">
                <h2 class="card-title">Thông tin phim</h2>

                <div class="movie-grid">
                    <div>
                        <div class="label">Phim</div>
                        <div class="value uppercase">{{ $invoice['movie'] }}</div>
                    </div>

                    <div>
                        <div class="label">Ghế</div>
                        <div class="value">{{ implode(', ', $invoice['seats']) }}</div>
                    </div>

                    <div>
                        <div class="label">Ngày giờ chiếu</div>
                        <div class="value highlight">
                            {{ $invoice['time'] }} - {{ $invoice['date'] }}
                        </div>
                    </div>

                    <div>
                        <div class="label">Phòng chiếu</div>
                        <div class="value">{{ $invoice['room'] }}</div>
                    </div>

                    <div>
                        <div class="label">Định dạng phòng</div>
                        <div class="value">{{ $invoice['format'] }}</div>
                    </div>
                </div>
            </div>

            {{-- THÔNG TIN THANH TOÁN --}}
            <div class="card">
                <h2 class="card-title">Thông tin thanh toán</h2>

                <div class="table-wrapper">
                    <table class="payment-table">
                        <thead>
                            <tr>
                                <th>Loại ghế</th>
                                <th>Ghế</th>
                                <th class="col-unit-price">Đơn giá</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>

                        <tbody>
                            {{-- seat_details đã được đảm bảo tồn tại từ controller --}}
                            @foreach($invoice['seat_details'] as $detail)
                                @php
                                    $typeLower = mb_strtolower($detail['type_name']);

                                    if (str_contains($typeLower, 'vip')) {
                                        $badgeClass = 'badge-vip';
                                        $dotClass   = 'dot-vip';
                                    } elseif (str_contains($typeLower, 'couple') || str_contains($typeLower, 'đôi')) {
                                        $badgeClass = 'badge-couple';
                                        $dotClass   = 'dot-couple';
                                    } elseif (str_contains($typeLower, 'normal') || str_contains($typeLower, 'thường')) {
                                        $badgeClass = 'badge-normal';
                                        $dotClass   = 'dot-normal';
                                    } else {
                                        $badgeClass = 'badge-default';
                                        $dotClass   = 'dot-default';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <span class="seat-type-badge {{ $badgeClass }}">
                                            <span class="badge-dot"></span>
                                            {{ $detail['type_name'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="seat-codes">
                                            @foreach($detail['codes'] as $code)
                                                <span class="seat-code-chip">{{ $code }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="col-unit-price price-unit">
                                        {{ number_format($detail['price']) }}đ
                                    </td>
                                    <td style="color:#94a3b8; font-weight:700;">
                                        {{ $detail['quantity'] }}
                                    </td>
                                    <td class="price-subtotal">
                                        {{ number_format($detail['subtotal']) }}đ
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="4">Tổng cộng</td>
                                <td class="price-total-cell">{{ number_format($invoice['total']) }}đ</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── RIGHT SIDEBAR ────────────────────────── --}}
        <div class="card sidebar">
            <h2 class="card-title">Phương thức thanh toán</h2>

            <form action="{{ route('system.payment') }}" method="POST">
                @csrf

                {{-- PAYMENT METHODS --}}
                <div class="payment-methods">
                    @foreach($paymentMethods as $index => $method)
                        <label class="payment-option">
                            <input
                                type="radio"
                                name="payment_method"
                                value="{{ $method->paymentID }}"
                                {{
                                    old('payment_method', session('selected_payment_method')) == $method->paymentID
                                    ? 'checked'
                                    : (old('payment_method', session('selected_payment_method')) === null && $index === 0
                                        ? 'checked'
                                        : '')
                                }}
                                required
                            >

                            <div class="payment-card">
                                <div class="payment-radio">
                                    <div class="payment-check"></div>
                                </div>

                                <div class="payment-logo
                                    @if(stripos($method->name, 'VNPAY') !== false) logo-vnpay
                                    @elseif(stripos($method->name, 'MoMo') !== false) logo-momo
                                    @elseif(stripos($method->name, 'VietQR') !== false) logo-vietqr
                                    @elseif(stripos($method->name, 'Visa') !== false) logo-visa
                                    @endif
                                ">
                                    @if(stripos($method->name, 'VNPAY') !== false)
                                        <span class="blue">VN</span><span class="red">PAY</span>
                                    @elseif(stripos($method->name, 'MoMo') !== false)
                                        momo
                                    @elseif(stripos($method->name, 'VietQR') !== false)
                                        <span class="red">Viet</span><span class="cyan">QR</span>
                                    @elseif(stripos($method->name, 'Viettel') !== false)
                                        viettel money
                                    @else
                                        {{ strtoupper($method->name) }}
                                    @endif
                                </div>

                                <div class="payment-name">{{ $method->name }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>

                {{-- CHI PHÍ BREAKDOWN --}}
                <div class="mb-6">
                    <h3 class="cost-title">Chi phí</h3>

                    @foreach($invoice['seat_details'] as $detail)
                        @php
                            $typeLower = mb_strtolower($detail['type_name']);

                            if (str_contains($typeLower, 'vip')) {
                                $dotClass = 'dot-vip';
                            } elseif (str_contains($typeLower, 'couple') || str_contains($typeLower, 'đôi')) {
                                $dotClass = 'dot-couple';
                            } elseif (str_contains($typeLower, 'normal') || str_contains($typeLower, 'thường')) {
                                $dotClass = 'dot-normal';
                            } else {
                                $dotClass = 'dot-default';
                            }
                        @endphp
                        <div class="cost-type-row">
                            <div class="cost-type-left">
                                <span class="cost-type-dot {{ $dotClass }}"></span>
                                <span class="cost-type-name">{{ $detail['type_name'] }}</span>
                                <span class="cost-type-qty">&times;{{ $detail['quantity'] }}</span>
                            </div>
                            <span class="cost-type-amount">{{ number_format($detail['subtotal']) }}đ</span>
                        </div>
                    @endforeach

                    <hr class="cost-divider">

                    <div class="cost-total">
                        <span class="cost-total-label">Tổng cộng</span>
                        <span class="cost-total-amount">{{ number_format($invoice['total']) }}đ</span>
                    </div>
                </div>

                {{-- BUTTON THANH TOÁN --}}
                <button type="submit" class="btn-submit">Thanh toán</button>

                {{-- QUAY LẠI --}}
                <button type="button" onclick="history.back()" class="btn-back">
                    ← Quay lại
                </button>
            </form>
        </div>

    </div>
</div>
@endsection