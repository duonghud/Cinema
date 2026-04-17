<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'CineMax Admin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f5f6fa;
            font-family: system-ui;
        }

        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: linear-gradient(180deg, #F5018F, #EECCF3);
            color: #fff;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        .sidebar>div:first-child {
            flex: 1;
            overflow-y: auto;
        }

        .logo {
            padding: 20px;
            font-size: 22px;
            font-weight: bold;
        }

        .sidebar nav a,
        .submenu a {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #fff;
            padding: 12px 20px;
        }

        .sidebar nav a.active {
            background: rgba(255, 255, 255, 0.2);
            border-left: 4px solid #fff;
        }

        .sidebar nav a:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .submenu a {
            padding-left: 45px;
        }

        .sidebar nav a i {
            font-size: 18px;
        }

        .sidebar-user {
            padding: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .content {
            margin-left: 260px;
            padding: 24px;
        }

        .topbar {
            background: #fff;
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        @media (max-width:991px) {
            .sidebar {
                left: -260px;
            }

            .sidebar.active {
                left: 0;
            }

            .content {
                margin-left: 0;
            }
        }

        .modern-toast-wrapper {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
        }

        .modern-toast {
            min-width: 260px;
            max-width: 320px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            margin-top: 10px;
            border-radius: 14px;

            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);

            border: 1px solid rgba(230, 230, 230, 0.8);

            box-shadow:
                0 8px 24px rgba(0, 0, 0, 0.08),
                0 2px 8px rgba(0, 0, 0, 0.04);

            color: #222;
            animation: slideInRight .4s ease;
        }

        .toast-success {
            border-left: 4px solid #acebce;
        }

        .toast-error {
            border-left: 4px solid #dc3545;
        }

        .toast-icon {
            font-size: 20px;
            flex-shrink: 0;
        }

        .toast-success .toast-icon {
            color: #198754;
        }

        .toast-error .toast-icon {
            color: #dc3545;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 1px;
        }

        .toast-text {
            font-size: 13px;
            color: #555;
        }

        .toast-close {
            background: transparent;
            border: none;
            color: #999;
            padding: 0;
            font-size: 13px;
            transition: .2s;
        }

        .toast-close:hover {
            color: #222;
            transform: scale(1.1);
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @media (max-width:576px) {
            .modern-toast {
                min-width: 90vw;
                max-width: 90vw;
            }

            .modern-toast-wrapper {
                right: 5%;
                top: 15px;
            }
        }
    </style>

    @stack('css')
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div>

            <div class="logo">
                VAI CINEMA
                <div style="font-size:13px;opacity:0.7">Trang quản trị viên</div>
            </div>

            <nav>

                <!-- DASHBOARD -->
                <a href="{{ route('dashboard.index') }}" class="{{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>

                <!-- USER -->
                <a data-bs-toggle="collapse" href="#userMenu">
                    <i class="bi bi-people-fill"></i> Quản lý người dùng
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>

                <div class="collapse submenu {{ request()->routeIs('admin.*','customer.*') ? 'show' : '' }}" id="userMenu">
                    <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">
                        <span class="mt-2"></span><i class="bi bi-shield-lock"></i> Admin
                    </a>
                    <a href="{{ route('customer.index') }}" class="{{ request()->routeIs('customer.*') ? 'active' : '' }}">
                        <span class="mt-2"></span><i class="bi bi-person"></i> Khách hàng
                    </a>
                </div>

                <!-- MOVIE -->
                <a data-bs-toggle="collapse" href="#movieMenu">
                    <i class="bi bi-film"></i> Quản lý phim
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>

                <div class="collapse submenu {{ request()->routeIs('movies.*','genre.*','studio.*','ageRating.*') ? 'show' : '' }}" id="movieMenu">
                    <a href="{{ route('admin.movies.index') }}" class="{{ request()->routeIs('movies.*') ? 'active' : '' }}">
                        <span class="mt-2"></span><i class="bi bi-camera-reels"></i> Phim
                    </a>
                    <a href="{{ route('genre.index') }}" class="{{ request()->routeIs('genre.*') ? 'active' : '' }}">
                        <span class="mt-2"></span><i class="bi bi-tags"></i> Thể loại
                    </a>
                    <a href="{{ route('studio.index') }}" class="{{ request()->routeIs('studio.*') ? 'active' : '' }}">
                        <span class="mt-2"></span><i class="bi bi-building"></i> Hãng sản xuất
                    </a>
                    <a href="{{ route('ageRating.index') }}" class="{{ request()->routeIs('ageRating.*') ? 'active' : '' }}">
                        <span class="mt-2"></span><i class="bi bi-shield-check"></i> Kiểm duyệt
                    </a>
                </div>

                <!-- CINEMA -->
                <a data-bs-toggle="collapse" href="#cinemaMenu">
                    <i class="bi bi-easel"></i> Rạp chiếu
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>

                <div class="collapse submenu {{ request()->routeIs('screeningRoom.*','seat.*','seatType.*','screenType.*') ? 'show' : '' }}" id="cinemaMenu">

                    <a href="{{ route('screeningRoom.index') }}" class="{{ request()->routeIs('screeningRoom.*') ? 'active' : '' }}">
                        <span class="mt-2"></span><i class="bi bi-tv"></i> Phòng chiếu
                    </a>

                    <!-- FIX ICON -->
                    <a href="{{ route('screenType.index') }}" class="{{ request()->routeIs('screenType.*') ? 'active' : '' }}">
                        <span class="mt-2"></span><i class="bi bi-aspect-ratio"></i> Loại phòng chiếu
                    </a>

                    <a href="{{ route('seat.index') }}" class="{{ request()->routeIs('seat.*') ? 'active' : '' }}">
                        <span class="mt-2"></span><i class="bi bi-grid"></i> Ghế
                    </a>

                    <a href="{{ route('seatType.index') }}" class="{{ request()->routeIs('seatType.*') ? 'active' : '' }}">
                        <span class="mt-2"></span><i class="bi bi-star"></i> Loại ghế
                    </a>

                </div>
                <!-- INVOICE (NEW) -->
                <a data-bs-toggle="collapse" href="#invoiceMenu">
                    <i class="bi bi-receipt-cutoff"></i> Hóa đơn
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>

                <div class="collapse submenu {{ request()->routeIs('invoices.*','foodInvoice.*') ? 'show' : '' }}" id="invoiceMenu">

                    <a href="{{ route('invoices.index') }}" class="{{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                        <span class="mt-2"></span><i class="bi bi-ticket-detailed"></i> Hóa đơn vé
                    </a>

                    <a href="{{ route('foodInvoice.index') }}" class="{{ request()->routeIs('foodInvoice.*') ? 'active' : '' }}">
                        <span class="mt-2"></span><i class="bi bi-cup-straw"></i> Hóa đơn đồ ăn
                    </a>

                </div>


                <!-- FOOD -->

                <a href="{{ route('food.index') }}" class="{{ request()->routeIs('food.*') ? 'active' : '' }}">
                    <i class="bi bi-egg-fried"></i> Đồ ăn
                </a>


                <!-- PAYMENT -->
                <a href="{{ route('paymentMethod.index') }}" class="{{ request()->routeIs('paymentMethod.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card"></i> Thanh toán
                </a>

                <!-- TICKET -->
                <a href="{{ route('ticket.index') }}" class="{{ request()->routeIs('ticket.*') ? 'active' : '' }}">
                    <i class="bi bi-ticket-perforated"></i> Vé
                </a>

                <!-- SHOWTIME -->
                <a href="{{ route('showTime.index') }}" class="{{ request()->routeIs('showTime.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event"></i> Lịch chiếu
                </a>

            </nav>
        </div>


        <!--Success thông báo lỗi-->
        <div class="modern-toast-wrapper">
            @if(session('success'))
            <div class="modern-toast toast-success">
                <div class="toast-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-text">{{ session('success') }}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="modern-toast toast-error">
                <div class="toast-icon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">Lỗi</div>
                    <div class="toast-text">{{ session('error') }}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            @endif
        </div>

        <!-- USER -->
        <div class="sidebar-user">
            <div class="d-flex align-items-center mb-2">
                <div class="rounded-circle bg-light text-dark fw-bold d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                    {{ strtoupper(substr(session('admin_auth.fullName', 'A'), 0, 1)) }}
                </div>
                <div class="ms-2">
                    <div class="fw-semibold">{{ session('admin_auth.fullName', 'Admin') }}</div>
                    <small>{{ session('admin_auth.email', 'admin@cinema.com') }}</small>
                </div>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button class="btn btn-light w-100 btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                </button>
            </form>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="content">
        <div class="topbar d-flex justify-content-between align-items-center">
            <h5>@yield('page-title','Dashboard')</h5>
            <span>{{ now()->format('d/m/Y') }}</span>
        </div>

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active')
        }

        setTimeout(() => {
            document.querySelectorAll('.modern-toast').forEach(toast => {
                toast.style.transition = '.4s';
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(40px)';
                setTimeout(() => toast.remove(), 400);
            });
        }, 4000);
    </script>

    @stack('js')
</body>

</html>