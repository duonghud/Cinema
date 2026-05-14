@extends('layouts.app')

@section('content')
<style>
    body {
        background: #020817;
        font-family: Arial, sans-serif;
    }

    /* Layout */
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

    /* Card */
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
        color: #fff
    }

    /* Movie info */
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

    /* Table */
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
        padding: 18px 20px;
        text-align: left;
    }

    .payment-table thead tr {
        border-bottom: 1px solid #475569;
    }

    .payment-table th {
        font-weight: 700;
        color: #fff
    }

    .payment-table td {
        font-weight: 600;
        color: #fff
    }

    /* Sidebar */
    .sidebar {
        position: sticky;
        top: 16px;
        height: fit-content;
    }

    /* Payment Methods */
    .payment-methods {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 28px;
    }

    .payment-option input[type="radio"] {
        display: none;
    }

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
        background: radial-gradient(circle at right,
                rgba(239, 68, 68, 0.12),
                transparent 55%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .payment-option input:checked + .payment-card {
        border-color: #ef4444;
        background: linear-gradient(90deg, #24181f, #2a1d24);
        box-shadow: 0 0 40px rgba(239, 68, 68, 0.15);
    }

    .payment-option input:checked + .payment-card::after {
        opacity: 1;
    }

    /* Radio circle */
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

    /* Check mark */
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

    /* Logo */
    .payment-logo {
        position: relative;
        z-index: 2;
        font-size: 14px;
        font-weight: 800;
        white-space: nowrap;
    }

    .logo-vnpay .blue {
        color: #3b82f6;
    }

    .logo-vnpay .red {
        color: #ef4444;
    }

    .logo-momo {
        color: #ec4899;
        text-transform: lowercase;
    }

    .logo-vietqr .red {
        color: #ef4444;
    }

    .logo-vietqr .cyan {
        color: #22d3ee;
    }

    .logo-viettel {
        color: #d1d5db;
    }

    .payment-name {
        position: relative;
        z-index: 2;
        font-size: 16px;
        font-weight: 600;
        color: #ffffff;
    }

    /* Cost */
    .cost-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 16px;
        color: #fff
    }

    .cost-row {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        margin-bottom: 6px;
        color: #fff
    }

    .cost-row span:last-child {
        font-weight: 700;
    }

    .cost-total {
        border-top: 1px dashed #64748b;
        margin-top: 12px;
        padding-top: 12px;
        font-size: 14px;
        font-weight: 700;
        display: flex;
        justify-content: space-between;
        color: #fff
    }

    /* Buttons */
    .btn-submit {
        width: 100%;
        padding: 8px 32px;
        border: none;
        border-radius: 999px;
        background: linear-gradient(90deg, #ff4458, #ff7a67);
        color: #ffffff;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 0 35px rgba(239, 68, 68, 0.25);
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 0 45px rgba(239, 68, 68, 0.35);
    }

    .btn-back {
        width: 100%;
        margin-top: 20px;
        background: none;
        border: none;
        color: #ffffff;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .btn-back:hover {
        color: #f87171;
    }

    /* Responsive */
    @media (max-width: 1280px) {
        .invoice-container {
            grid-template-columns: 1fr;
        }

        .sidebar {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .invoice-wrapper {
            padding: 12px;
        }

        .card {
            padding: 20px;
        }

        .card-title {
            font-size: 24px;
        }

        .movie-grid {
            grid-template-columns: 1fr;
            gap: 24px;
        }

        .value {
            font-size: 18px;
        }

        .cost-total {
            font-size: 24px;
        }
    }
</style>

<div class="invoice-wrapper">
    <div class="invoice-container">

        {{-- LEFT CONTENT --}}
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
                        <div class="label">Định dạng</div>
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
                                <th>Danh mục</th>
                                <th>Số lượng</th>
                                <th>Tổng tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Ghế ({{ implode(', ', $invoice['seats']) }})</td>
                                <td>{{ count($invoice['seats']) }}</td>
                                <td>{{ number_format($invoice['total']) }}đ</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- RIGHT SIDEBAR --}}
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
                                    @if(stripos($method->name, 'VNPAY') !== false)
                                        logo-vnpay
                                    @elseif(stripos($method->name, 'MoMo') !== false)
                                        logo-momo
                                    @elseif(stripos($method->name, 'VietQR') !== false)
                                        logo-vietqr
                                    @elseif(stripos($method->name, 'Viettel') !== false)
                                        logo-viettel
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

                                <div class="payment-name">
                                    {{ $method->name }}
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

                {{-- CHI PHÍ --}}
                <div class="mb-6">
                    <h3 class="cost-title">Chi phí</h3>

                    <div class="cost-row">
                        <span>Thanh toán</span>
                        <span>{{ number_format($invoice['total']) }}đ</span>
                    </div>

                    <div class="cost-row">
                        <span>Phí</span>
                        <span>0đ</span>
                    </div>

                    <div class="cost-total">
                        <span>Tổng cộng</span>
                        <span>{{ number_format($invoice['total']) }}đ</span>
                    </div>
                </div>

                {{-- BUTTON THANH TOÁN --}}
                <button type="submit" class="btn-submit">
                    Thanh toán
                </button>

                {{-- QUAY LẠI --}}
                <button
                    type="button"
                    onclick="history.back()"
                    class="btn-back">
                    Quay lại
                </button>
            </form>
        </div>
    </div>
</div>
@endsection