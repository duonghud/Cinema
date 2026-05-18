@extends('layouts.app')

@section('title', 'Hồ sơ khách hàng')

@section('content')
@php
$customer = $customer ?? session('customer');
$fullName = $customer->fullName ?? 'Khách xem phim';
$email = $customer->email ?? 'you@example.com';
$phone = $customer->phoneNumber ?? 'Chưa cập nhật';
$address = $customer->address ?? 'Chưa cập nhật';
$initials = collect(explode(' ', trim($fullName)))
->filter()
->take(2)
->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
->implode('');
@endphp

<style>
    .profile-page {
        background:
            radial-gradient(circle at top left, rgba(217, 63, 64, 0.28), transparent 34%),
            radial-gradient(circle at top right, rgba(245, 158, 11, 0.14), transparent 26%),
            linear-gradient(180deg, #0b0d13 0%, #111827 42%, #0b0d13 100%);
    }

    .profile-shell {
        max-width: 1240px;
    }

    .glass-panel {
        background: rgba(12, 16, 24, 0.82);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(18px);
    }

    .hero-banner {
        position: relative;
        overflow: hidden;
        border-radius: 32px;
        background:
            linear-gradient(135deg, rgba(217, 63, 64, 0.95), rgba(124, 33, 39, 0.92)),
            linear-gradient(135deg, #111827, #1f2937);
    }

    .hero-banner::before {
        content: "";
        position: absolute;
        inset: auto -8% -55% auto;
        width: 380px;
        height: 380px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.08);
    }

    .hero-banner::after {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(120deg, transparent 0%, rgba(255, 255, 255, 0.08) 48%, transparent 100%);
        transform: translateX(-100%);
        animation: profileShine 5.8s linear infinite;
    }

    .avatar-orb {
        width: 108px;
        height: 108px;
        border-radius: 28px;
        background: linear-gradient(135deg, #ffe082, #ff7043);
        color: #1f2937;
        font-size: 2rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 18px 35px rgba(17, 24, 39, 0.3);
    }

    .profile-chip {
        border: 1px solid rgba(255, 255, 255, 0.14);
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
    }

    .metric-card,
    .sidebar-card,
    .form-card {
        border-radius: 28px;
    }

    .metric-card {
        padding: 1.35rem;
        height: 100%;
    }

    .metric-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .metric-label {
        color: #9ca3af;
        letter-spacing: 0.03em;
    }

    .metric-value {
        color: #fff;
        font-size: 1.75rem;
        font-weight: 700;
    }

    .profile-input {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #fff;
        border-radius: 18px;
        padding: 0.95rem 1rem;
    }

    .profile-input:focus {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(248, 113, 113, 0.5);
        box-shadow: 0 0 0 0.18rem rgba(248, 113, 113, 0.18);
        color: #fff;
    }

    .profile-input::placeholder {
        color: #6b7280;
    }

    .sidebar-list li+li {
        border-top: 1px solid rgba(255, 255, 255, 0.08);
    }

    .soft-dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        background: #34d399;
        box-shadow: 0 0 0 6px rgba(52, 211, 153, 0.12);
    }

    @keyframes profileShine {
        to {
            transform: translateX(100%);
        }
    }

    @media (max-width: 767.98px) {
        .hero-banner {
            border-radius: 24px;
        }

        .avatar-orb {
            width: 88px;
            height: 88px;
            border-radius: 24px;
            font-size: 1.5rem;
        }
    }
</style>

<section class="profile-page py-5 px-3 px-md-4 px-xl-0 text-white min-vh-100">
    <div class="container profile-shell">
       {{-- <div class="hero-banner p-4 p-md-5 mb-4">
            <div class="row g-4 align-items-center position-relative" style="z-index: 1;">
                <div class="col-lg-8">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-4">
                        <div class="avatar-orb">
                            {{ $initials ?: 'CG' }}
                        </div>

                        <div>
                            <span class="badge rounded-pill profile-chip px-3 py-2 mb-3">
                                <i class="bi bi-stars me-2"></i>Customer Profile
                            </span>
                            <h1 class="display-6 fw-bold mb-2">{{ $fullName }}</h1>
                            <p class="mb-3 text-white-50 fs-5">
                                Không gian quản lý thông tin cá nhân và trạng thái tài khoản tại VAI Cinema.
                            </p>

                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge rounded-pill bg-light text-dark px-3 py-2">
                                    <i class="bi bi-envelope-fill me-2"></i>{{ $email }}
                                </span>
                                <span class="badge rounded-pill profile-chip px-3 py-2">
                                    <i class="bi bi-telephone-fill me-2"></i>{{ $phone }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="glass-panel rounded-4 p-4">
                        <p class="text-uppercase small text-white-50 mb-2">Trạng thái thành viên</p>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <span class="soft-dot"></span>
                            <strong class="fs-5">Đang hoạt động</strong>
                        </div>
                        <p class="mb-0 text-white-50">
                            Hồ sơ này đang đồng bộ theo phiên đăng nhập hiện tại. Bạn có thể dùng khu vực này để theo dõi dữ liệu cá nhân và lịch sử giao dịch ở các bước tiếp theo.
                        </p>
                    </div>
                </div>
            </div>
        </div>--}}

        {{--<div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="glass-panel metric-card">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="metric-icon" style="background: rgba(248, 113, 113, 0.16); color: #fca5a5;">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <span class="badge rounded-pill text-bg-light">ID</span>
                    </div>
                    <div class="metric-label small mb-2">Mã khách hàng</div>
                    <div class="metric-value">{{ $customer->customerID ?? 'N/A' }}</div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="glass-panel metric-card">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="metric-icon" style="background: rgba(96, 165, 250, 0.16); color: #93c5fd;">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <span class="badge rounded-pill text-bg-primary">Secure</span>
                    </div>
                    <div class="metric-label small mb-2">Bảo mật tài khoản</div>
                    <div class="metric-value">Ổn định</div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="glass-panel metric-card">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="metric-icon" style="background: rgba(52, 211, 153, 0.16); color: #6ee7b7;">
                            <i class="bi bi-ticket-perforated"></i>
                        </div>
                        <span class="badge rounded-pill text-bg-success">Ready</span>
                    </div>
                    <div class="metric-label small mb-2">Kênh đặt vé</div>
                    <div class="metric-value">Online</div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="glass-panel metric-card">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="metric-icon" style="background: rgba(251, 191, 36, 0.16); color: #fcd34d;">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <span class="badge rounded-pill text-bg-warning">Info</span>
                    </div>
                    <div class="metric-label small mb-2">Địa chỉ liên hệ</div>
                    <div class="metric-value fs-4">{{ $address !== 'Chưa cập nhật' ? 'Đã có' : 'Thiếu' }}</div>
                </div>
            </div>
        </div>--}}

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="glass-panel form-card p-4 p-md-5">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                        <div>
                            <p class="text-uppercase small text-white-50 mb-2">Thông tin cá nhân</p>
                            <h2 class="h3 fw-bold mb-0">Customer Profile Overview</h2>
                        </div>

                        <span class="badge rounded-pill profile-chip px-3 py-2">
                            <i class="bi bi-lock-fill me-2"></i>Đang ở chế độ xem
                        </span>
                    </div>

                    <form>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label text-white-50">Họ và tên</label>
                                <input type="text" class="form-control profile-input" value="{{ $fullName }}" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-white-50">Email</label>
                                <input type="email" class="form-control profile-input" value="{{ $email }}" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-white-50">Số điện thoại</label>
                                <input type="text" class="form-control profile-input" value="{{ $phone }}" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-white-50">Loại tài khoản</label>
                                <input type="text" class="form-control profile-input" value="Khách hàng rạp phim" readonly>
                            </div>

                            <div class="col-12">
                                <label class="form-label text-white-50">Địa chỉ</label>
                                <textarea class="form-control profile-input" rows="4" readonly>{{ $address }}</textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="glass-panel sidebar-card p-4 mb-4">
                    <p class="text-uppercase small text-white-50 mb-3">Tài khoản</p>
                    <ul class="list-unstyled mb-0 sidebar-list">
                        <li class="py-3 d-flex justify-content-between gap-3">
                            <span class="text-white-50">Phiên đăng nhập</span>
                            <strong class="text-white">Đã xác thực</strong>
                        </li>
                        <li class="py-3 d-flex justify-content-between gap-3">
                            <span class="text-white-50">Email liên kết</span>
                            <strong class="text-white text-end">{{ $email }}</strong>
                        </li>
                        <li class="py-3 d-flex justify-content-between gap-3">
                            <span class="text-white-50">SĐT liên hệ</span>
                            <strong class="text-white">{{ $phone }}</strong>
                        </li>
                    </ul>
                </div>

                <div class="glass-panel sidebar-card p-4">
                    <p class="text-uppercase small text-white-50 mb-3">Gợi ý tiếp theo</p>
                    <div class="d-grid gap-3">
                        <a href="{{ route('home') }}" class="btn btn-light rounded-pill py-3 fw-semibold">
                            <i class="bi bi-film me-2"></i>Khám phá phim đang chiếu
                        </a>
                        <a href="{{ route('ticket.price') }}" class="btn btn-outline-light rounded-pill py-3 fw-semibold">
                            <i class="bi bi-tags me-2"></i>Xem bảng giá vé
                        </a>
                    </div>
                    <div class="glass-panel sidebar-card p-4 mt-4">
                        <p class="text-uppercase small text-white-50 mb-3">Lịch sử đặt vé</p>
                        <p class="text-white-50 mb-4">
                            Xem lại toàn bộ các vé bạn đã đặt, thông tin suất chiếu, ghế ngồi và trạng thái thanh toán.
                        </p>

                        <div class="d-grid">
                            <a href="{{ route('booking.history') }}"
                                class="btn btn-danger rounded-pill py-3 fw-semibold">
                                <i class="bi bi-clock-history me-2"></i>Xem lịch sử đặt vé
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection