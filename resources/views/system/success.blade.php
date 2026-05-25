{{-- resources/views/payment/success.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
    body {
        background: #020817;
        font-family: Arial, sans-serif;
    }

    /* Layout */
    .success-wrapper {
        min-height: 100vh;
        padding: 12px 24px;
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }

    .success-container {
        width: 100%;
        max-width: 860px;
        margin: 0 auto;
    }

    /* Card */
    .success-card {
        position: relative;
        background: #111827;
        border: 1px solid #1e293b;
        border-radius: 20px;
        padding: 42px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        overflow: hidden;
    }

    /* Glow background */
    .success-card::before {
        content: "";
        position: absolute;
        top: -120px;
        left: 50%;
        transform: translateX(-50%);
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: radial-gradient(circle,
                rgba(34, 197, 94, 0.16),
                transparent 70%);
        pointer-events: none;
    }

    /* Confetti */
    .confetti {
        position: absolute;
        inset: 0;
        pointer-events: none;
        opacity: 0.8;
    }

    .confetti span {
        position: absolute;
        width: 8px;
        height: 16px;
        border-radius: 999px;
    }

    .confetti span:nth-child(odd) {
        background: #22c55e;
    }

    .confetti span:nth-child(even) {
        background: #f59e0b;
    }

    .confetti span:nth-child(3n) {
        background: #3b82f6;
    }


    /* Success Icon */
    .success-icon-wrapper {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: center;
        margin-bottom: 24px;
        color: #22c55e;
    }

    .success-icon {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #22c55e, #16a34a);
        display: flex;
        justify-content: center;
        align-items: center;
        color: #ffffff;
        font-size: 58px;
        font-weight: 900;
        box-shadow:
            0 0 0 14px rgba(34, 197, 94, 0.10),
            0 0 0 28px rgba(34, 197, 94, 0.05),
            0 18px 40px rgba(34, 197, 94, 0.30);
    }

    /* Text */
    .success-title {
        position: relative;
        z-index: 2;
        text-align: center;
        font-size: 40px;
        font-weight: 800;
        color: #22c55e;
        margin-bottom: 10px;
    }

    .success-subtitle {
        position: relative;
        z-index: 2;
        text-align: center;
        font-size: 18px;
        color: #94a3b8;
        margin-bottom: 32px;
    }

    .divider {
        height: 1px;
        background: linear-gradient(to right, transparent, #334155, transparent);
        margin-bottom: 32px;
        position: relative;
        z-index: 2;
    }

    /* Info Box */
    .info-box {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
    }

    .info-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: rgba(34, 197, 94, 0.12);
        color: #22c55e;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .info-box h5 {
        margin: 0 0 4px;
        font-size: 24px;
        font-weight: 700;
        color: #ffffff;
    }

    .info-box p {
        margin: 0;
        font-size: 15px;
        color: #94a3b8;
    }

    /* Transaction Box */
    .transaction-box {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 24px 28px;
        border: 1px solid rgba(34, 197, 94, 0.35);
        border-radius: 18px;
        background: linear-gradient(90deg, #171d29, #1c2431);
        margin-bottom: 20px;
    }

    .transaction-box span {
        font-size: 15px;
        font-weight: 600;
        color: #94a3b8;
    }

    .transaction-box strong {
        font-size: 32px;
        font-weight: 800;
        color: #22c55e;
        letter-spacing: 1px;
    }

    /* Status */
    .status-box {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 18px 22px;
        border-radius: 16px;
        background: rgba(34, 197, 94, 0.10);
        border: 1px solid rgba(34, 197, 94, 0.25);
        color: #bbf7d0;
        margin-bottom: 24px;
    }

    .status-box i {
        font-size: 22px;
        color: #22c55e;
    }

    /* Note */
    .note {
        position: relative;
        z-index: 2;
        text-align: center;
        color: #94a3b8;
        font-size: 14px;
        margin-bottom: 30px;
    }

    /* Buttons */
    .action-buttons {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .action-buttons a {
        text-decoration: none;
        padding: 12px 28px;
        border-radius: 999px;
        font-size: 15px;
        font-weight: 700;
        transition: all 0.3s ease;
    }

    .btn-home {
        background: linear-gradient(90deg, #ff4458, #ff7a67);
        color: #ffffff;
        box-shadow: 0 0 35px rgba(239, 68, 68, 0.25);
    }

    .btn-home:hover {
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 0 45px rgba(239, 68, 68, 0.35);
    }

    .btn-book {
        border: 1px solid #475569;
        background: transparent;
        color: #ffffff;
    }

    .btn-book:hover {
        color: #f87171;
        border-color: #f87171;
        background: rgba(248, 113, 113, 0.05);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .success-wrapper {
            padding: 12px;
        }

        .success-card {
            padding: 28px 20px;
        }

        .success-icon {
            width: 96px;
            height: 96px;
            font-size: 44px;
        }

        .success-title {
            font-size: 28px;
        }

        .success-subtitle {
            font-size: 15px;
        }

        .info-box h5 {
            font-size: 20px;
        }

        .transaction-box {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }

        .transaction-box strong {
            font-size: 24px;
        }

        .action-buttons {
            flex-direction: column;
        }

        .action-buttons a {
            width: 100%;
            text-align: center;
        }

    }
</style>

<div class="success-wrapper">
    <div class="success-container">
        <div class="success-card">

            {{-- Confetti --}}
            <div class="confetti">
                @for ($i = 1; $i <= 15; $i++)
                    <span></span>
                    @endfor
            </div>

            {{-- Success Icon --}}
            <div class="success-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 15 15">
                    <path d="M0 0h15v15H0z" fill="none" />
                    <path fill="none" stroke="currentColor" d="M4 7.5L7 10l4-5m-3.5 9.5a7 7 0 1 1 0-14a7 7 0 0 1 0 14Z" />
                </svg>
            </div>

            {{-- Title --}}
            <h1 class="success-title">Thanh toán thành công!</h1>
            <p class="success-subtitle">
                Giao dịch của bạn đã được xử lý thành công.
            </p>

            <div class="divider"></div>

            {{-- Info --}}
            <div class="info-box">
                <div class="info-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2m-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8z" />
                    </svg>
                </div>
                <div>
                    <h5>Cảm ơn bạn đã đặt vé tại VAI Cinema</h5>
                    <p>Vé xem phim và thông tin chi tiết đã được ghi nhận trong hệ thống.</p>
                </div>
            </div>

            <div class="transaction-box">
                <span>Mã giao dịch</span>
                <strong>INV-{{ $invoiceID }}</strong>
            </div>
            {{-- Status --}}
            <div class="status-box">
                <i class="fas fa-check-circle"></i>
                <span>
                    <strong>Trạng thái:</strong> Đã thanh toán thành công!.
                </span>
            </div>

            {{-- Note --}}
            <p class="note">
                <i class="fas fa-info-circle"></i>
                Vui lòng lưu lại mã giao dịch để tra cứu khi cần thiết.
            </p>

            {{-- Buttons --}}
            <div class="action-buttons">
                <a href="{{ url('/') }}" class="btn-home">
                    <i class="fas fa-home me-2"></i>
                    Về trang chủ
                </a>

                <a href="{{ route('show') }}" class="btn-book">
                    <i class="fas fa-film me-2"></i>
                    Tiếp tục đặt vé
                </a>
            </div>
        </div>
    </div>
</div>
@endsection