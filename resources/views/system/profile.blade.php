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
    /* ── Page shell ─────────────────────────────────────────────── */
    .profile-page {
        background:
            radial-gradient(circle at top left, rgba(217, 63, 64, 0.28), transparent 34%),
            radial-gradient(circle at top right, rgba(245, 158, 11, 0.14), transparent 26%),
            linear-gradient(180deg, #0b0d13 0%, #111827 42%, #0b0d13 100%);
    }

    .profile-shell {
        max-width: 1240px;
    }

    /* ── Glass panels ───────────────────────────────────────────── */
    .glass-panel {
        background: rgba(12, 16, 24, 0.82);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(18px);
    }

    /* ── Avatar ─────────────────────────────────────────────────── */
    .avatar-orb {
        width: 72px;
        height: 72px;
        border-radius: 20px;
        background: linear-gradient(135deg, #ffe082, #ff7043);
        color: #1f2937;
        font-size: 1.5rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.3);
    }

    /* ── Chip ────────────────────────────────────────────────────── */
    .profile-chip {
        border: 1px solid rgba(255, 255, 255, 0.14);
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
    }

    /* ── Form inputs ─────────────────────────────────────────────── */
    .profile-input {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #fff;
        border-radius: 18px;
        padding: 0.85rem 1rem;
        transition: border-color .2s, background .2s;
    }

    .profile-input:focus {
        background: rgba(255, 255, 255, 0.06);
        border-color: rgba(248, 113, 113, 0.55);
        box-shadow: 0 0 0 0.18rem rgba(248, 113, 113, 0.18);
        color: #fff;
    }

    .profile-input[readonly] {
        cursor: default;
        opacity: 0.7;
    }

    .profile-input::placeholder {
        color: #6b7280;
    }

    /* ── Sidebar list ────────────────────────────────────────────── */
    .sidebar-list li+li {
        border-top: 1px solid rgba(255, 255, 255, 0.08);
    }

    /* ── Edit/View mode toggle ───────────────────────────────────── */
    #edit-section {
        display: none;
    }

    #edit-section.active {
        display: block;
    }

    #view-section.hidden-view {
        display: none;
    }

    /* ── Alert strip ─────────────────────────────────────────────── */
    .alert-strip {
        border-radius: 18px;
        font-size: .9rem;
    }

    /* ── Password strength bar ───────────────────────────────────── */
    .strength-bar {
        height: 4px;
        border-radius: 99px;
        background: rgba(255, 255, 255, 0.08);
        overflow: hidden;
    }

    .strength-fill {
        height: 100%;
        border-radius: 99px;
        transition: width .3s, background .3s;
    }

    /* ── Card radius ─────────────────────────────────────────────── */
    .sidebar-card,
    .form-card {
        border-radius: 28px;
    }

    .form-card {
        padding: 2rem 2.25rem;
    }

    @media (max-width: 767.98px) {
        .form-card {
            padding: 1.5rem 1.25rem;
        }
    }
</style>

<section class="profile-page py-5 px-3 px-md-4 px-xl-0 text-white min-vh-100">
    <div class="container profile-shell">

        {{-- ── Flash messages ───────────────────────────────────────── --}}
        @if(session('success'))
        <div class="alert alert-success alert-strip alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-strip alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- ── Header strip ─────────────────────────────────────────── --}}
        <div class="glass-panel form-card d-flex align-items-center gap-4 mb-4" style="border-radius:28px;">
            <div class="avatar-orb">{{ $initials ?: 'CG' }}</div>
            <div class="flex-grow-1 min-w-0">
                <p class="text-uppercas small text-white-50 mb-1">VAI Cinema · Customer</p>
                <h1 class="h3 fw-bold mb-1 text-truncate">{{ $fullName }}</h1>
                <span class="badge profile-chip rounded-pill px-3 py-1">
                    <i class="bi bi-envelope me-1"></i>{{ $email }}
                </span>
            </div>
            {{-- Toggle edit button --}}
            <button id="btn-toggle-edit" class="btn btn-danger rounded-pill px-4 py-2 fw-semibold d-none d-md-flex align-items-center gap-2 flex-shrink-0">
                <i class="bi bi-pencil-square"></i> Chỉnh sửa
            </button>
        </div>

        <div class="row g-4">

            {{-- ── Main form panel ───────────────────────────────────── --}}
            <div class="col-xl-8">
                <div class="glass-panel form-card">

                    {{-- Section label --}}
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <p class="text-uppercase small text-white-50 mb-1">Thông tin cá nhân</p>
                            <h2 class="h4 fw-bold mb-0" id="panel-title">Hồ sơ khách hàng</h2>
                        </div>
                        <span id="mode-badge" class="badge rounded-pill profile-chip px-3 py-2">
                            <i class="bi bi-lock-fill me-2"></i>Chế độ xem
                        </span>
                    </div>

                    {{-- ── VIEW mode ─────────────────────────────────── --}}
                    <div id="view-section">
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
                            <div class="col-12">
                                <label class="form-label text-white-50">Địa chỉ</label>
                                <textarea class="form-control profile-input" rows="3" readonly>{{ $address }}</textarea>
                            </div>
                        </div>

                        {{-- Mobile edit button --}}
                        <div class="d-md-none mt-4">
                            <button id="btn-toggle-edit-mobile" class="btn btn-danger rounded-pill w-100 py-3 fw-semibold">
                                <i class="bi bi-pencil-square me-2"></i>Chỉnh sửa hồ sơ
                            </button>
                        </div>
                    </div>

                    {{-- ── EDIT mode ─────────────────────────────────── --}}
                    <div id="edit-section">
                        <form method="POST" action="{{ route('customer.profile.update') }}" id="profile-form">
                            @csrf

                            {{-- Validation errors --}}
                            @if($errors->any())
                            <div class="alert alert-danger alert-strip mb-4">
                                <ul class="mb-0 ps-3">
                                    @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label text-white-50">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" name="fullName"
                                        class="form-control profile-input @error('fullName') is-invalid @enderror"
                                        value="{{ old('fullName', $fullName) }}"
                                        placeholder="Nhập họ và tên">
                                    @error('fullName')
                                    <div class="invalid-feedback ps-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-white-50">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email"
                                        class="form-control profile-input @error('email') is-invalid @enderror"
                                        value="{{ old('email', $email) }}"
                                        placeholder="Nhập email">
                                    @error('email')
                                    <div class="invalid-feedback ps-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-white-50">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="text" name="phoneNumber"
                                        class="form-control profile-input @error('phoneNumber') is-invalid @enderror"
                                        value="{{ old('phoneNumber', $phone) }}"
                                        placeholder="Nhập số điện thoại">
                                    @error('phoneNumber')
                                    <div class="invalid-feedback ps-1">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="col-12">
                                    <label class="form-label text-white-50">Địa chỉ <span class="text-danger">*</span></label>
                                    <textarea name="address" rows="3"
                                        class="form-control profile-input @error('address') is-invalid @enderror"
                                        placeholder="Nhập địa chỉ">{{ old('address', $address) }}</textarea>
                                    @error('address')
                                    <div class="invalid-feedback ps-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- ── Đổi mật khẩu (tuỳ chọn) ──────── --}}
                                <!-- <div class="col-12">
                                    <hr style="border-color: rgba(255,255,255,0.08);">
                                    <p class="text-uppercase small text-white-50 mb-3">
                                        Đổi mật khẩu <span class="text-white-50 fw-normal">(bỏ trống nếu không muốn đổi)</span>
                                    </p>
                                </div> -->

                                <!-- <div class="col-md-6">
                                    <label class="form-label text-white-50">Mật khẩu mới</label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="new-password"
                                            class="form-control profile-input @error('password') is-invalid @enderror"
                                            placeholder="Tối thiểu 6 ký tự"
                                            autocomplete="new-password">
                                        <button type="button" class="btn profile-input border-start-0 rounded-end-4 px-3 toggle-pw" data-target="new-password">
                                            <i class="bi bi-eye text-white-50"></i>
                                        </button>
                                    </div>
                                    {{-- Strength bar --}}
                                    <div class="strength-bar mt-2">
                                        <div class="strength-fill" id="strength-fill" style="width:0%"></div>
                                    </div>
                                    <small id="strength-label" class="text-white-50"></small>
                                    @error('password')
                                    <div class="text-danger small ps-1 mt-1">{{ $message }}</div>
                                    @enderror
                                </div> -->

                                <!-- <div class="col-md-6">
                                    <label class="form-label text-white-50">Xác nhận mật khẩu mới</label>
                                    <div class="input-group">
                                        <input type="password" name="password_confirmation" id="confirm-password"
                                            class="form-control profile-input"
                                            placeholder="Nhập lại mật khẩu mới"
                                            autocomplete="new-password">
                                        <button type="button" class="btn profile-input border-start-0 rounded-end-4 px-3 toggle-pw" data-target="confirm-password">
                                            <i class="bi bi-eye text-white-50"></i>
                                        </button>
                                    </div>
                                </div> -->
                            </div>

                            {{-- Action buttons --}}
                            <div class="d-flex flex-wrap gap-3 mt-4 pt-2">
                                <button type="submit" class="btn btn-danger rounded-pill px-5 py-2 fw-semibold">
                                    <i class="bi bi-check-lg me-2"></i>Lưu thay đổi
                                </button>
                                <button type="button" id="btn-cancel-edit" class="btn btn-outline-light rounded-pill px-4 py-2 fw-semibold">
                                    Huỷ
                                </button>
                            </div>
                        </form>
                    </div>

                </div>{{-- /form-card --}}
            </div>{{-- /col-xl-8 --}}

            {{-- ── Sidebar ────────────────────────────────────────────── --}}
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
                            <strong class="text-white text-end" style="word-break:break-all;">{{ $email }}</strong>
                        </li>
                        <li class="py-3 d-flex justify-content-between gap-3">
                            <span class="text-white-50">SĐT liên hệ</span>
                            <strong class="text-white">{{ $phone }}</strong>
                        </li>
                    </ul>
                </div>

                <div class="glass-panel sidebar-card p-4 mb-4">
                    <p class="text-uppercase small text-white-50 mb-3">Gợi ý tiếp theo</p>
                    <div class="d-grid gap-3">
                        <a href="{{ route('home') }}" class="btn btn-light rounded-pill py-3 fw-semibold">
                            <i class="bi bi-film me-2"></i>Khám phá phim đang chiếu
                        </a>
                        <a href="{{ route('ticket.price') }}" class="btn btn-outline-light rounded-pill py-3 fw-semibold">
                            <i class="bi bi-tags me-2"></i>Xem bảng giá vé
                        </a>
                    </div>
                </div>

                <div class="glass-panel sidebar-card p-4">
                    <p class="text-uppercase small text-white-50 mb-3">Lịch sử đặt vé</p>
                    <p class="text-white-50 mb-4">
                        Xem lại toàn bộ các vé đã đặt, thông tin suất chiếu, ghế ngồi và trạng thái thanh toán.
                    </p>
                    <div class="d-grid">
                        <a href="{{ route('booking.history') }}" class="btn btn-danger rounded-pill py-3 fw-semibold">
                            <i class="bi bi-clock-history me-2"></i>Xem lịch sử đặt vé
                        </a>
                    </div>
                </div>

            </div>{{-- /col-xl-4 --}}
        </div>{{-- /row --}}

    </div>{{-- /container --}}
</section>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const viewSection  = document.getElementById('view-section');
    const editSection  = document.getElementById('edit-section');
    const modeBadge    = document.getElementById('mode-badge');
    const panelTitle   = document.getElementById('panel-title');
    const btnEdit      = document.getElementById('btn-toggle-edit');
    const btnEditMob   = document.getElementById('btn-toggle-edit-mobile');
    const btnCancel    = document.getElementById('btn-cancel-edit');

    function enterEditMode() {
        viewSection.classList.add('hidden-view');
        editSection.classList.add('active');
        modeBadge.innerHTML = '<i class="bi bi-pencil-fill me-2"></i>Chế độ chỉnh sửa';
        panelTitle.textContent = 'Cập nhật hồ sơ';
    }

    function exitEditMode() {
        editSection.classList.remove('active');
        viewSection.classList.remove('hidden-view');
        modeBadge.innerHTML = '<i class="bi bi-lock-fill me-2"></i>Chế độ xem';
        panelTitle.textContent = 'Hồ sơ khách hàng';
    }

    const hasErrors = document.querySelectorAll('#edit-section .is-invalid, #edit-section .text-danger').length > 0
                   || {{ $errors->any() ? 'true' : 'false' }};

    if (hasErrors) { enterEditMode(); }

    btnEdit?.addEventListener('click', enterEditMode);
    btnEditMob?.addEventListener('click', enterEditMode);
    btnCancel?.addEventListener('click', exitEditMode);

    // ── Password visibility toggle ──────────────────────────────────────
    document.querySelectorAll('.toggle-pw').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            const icon  = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash text-white-50';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye text-white-50';
            }
        });
    });

})();
</script>
@endpush