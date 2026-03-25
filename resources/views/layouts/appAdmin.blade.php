<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'CineMax Admin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* BODY */
        body {
            background: #f5f6fa;
            font-family: system-ui;
        }

        /* SIDEBAR */
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

        /* Container menu cuộn */
        .sidebar>div:first-child {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        /* Scrollbar đẹp */
        .sidebar>div:first-child::-webkit-scrollbar {
            width: 8px;
        }

        .sidebar>div:first-child::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .sidebar>div:first-child::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.4);
            border-radius: 10px;
        }

        .sidebar>div:first-child::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.6);
        }

        .sidebar>div:first-child {
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.4) rgba(255, 255, 255, 0.1);
        }

        /* LOGO */
        .logo {
            padding: 20px;
            font-size: 22px;
            font-weight: bold;
        }

        /* MENU LINKS */
        .sidebar nav a,
        .submenu a {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #fff;
            padding: 12px 20px;
            /* đồng bộ padding */
            font-size: 16px;
            /* đồng bộ font */
            line-height: 1.6;
        }

        /* ACTIVE & HOVER */
        .sidebar nav a.active {
            background: rgba(255, 255, 255, 0.2);
            border-left: 4px solid #fff;
        }

        .sidebar nav a:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        /* SUBMENU */
        .submenu a {
            padding-left: 45px;
            /* thụt vào */
            min-height: 40px;
            /* đảm bảo item con không nhỏ hơn menu cha */
        }

        /* SUBMENU collapse */
        .submenu.collapse {
            transition: height 0.3s ease;
            /* animation mềm mại */
        }

        /* ICONS */
        .sidebar nav a i {
            font-size: 18px;
        }

        /* USER cố định dưới đáy */
        .sidebar-user {
            padding: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* CONTENT */
        .content {
            margin-left: 260px;
            padding: 24px;
        }

        /* TOPBAR */
        .topbar {
            background: #fff;
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        /* MOBILE */
        .toggle-btn {
            display: none;
            font-size: 22px;
            cursor: pointer;
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

            .toggle-btn {
                display: block;
            }
        }
    </style>
    @stack('css')
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div> <!-- menu + logo cuộn -->
            <div class="logo">
                CineMax
                <div style="font-size:13px;opacity:0.7">Trang quản trị viên</div>
            </div>
            <nav>
                <!-- DASHBOARD -->
                <a href="{{ route('dashboard.index') }}" class="{{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>

                <!-- USER -->
                <a data-bs-toggle="collapse" href="#userMenu" class="d-flex align-items-center">
                    <i class="bi bi-people-fill me-2"></i> Quản lý người dùng
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <div class="collapse submenu {{ request()->routeIs('admin.*','customer.*') ? 'show' : '' }}" id="userMenu">
                    <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock me-2"></i> Admin
                    </a>
                    <a href="{{ route('customer.index') }}" class="{{ request()->routeIs('customer.*') ? 'active' : '' }}">
                        <i class="bi bi-person me-2"></i> Khách hàng
                    </a>
                </div>

                <!-- MOVIE -->
                <a data-bs-toggle="collapse" href="#movieMenu" class="d-flex align-items-center">
                    <i class="bi bi-film me-2"></i> Quản lý phim
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <div class="collapse submenu {{ request()->routeIs('movies.*','genre.*','studio.*','ageRating.*') ? 'show' : '' }}" id="movieMenu">
                    <a href="{{ route('movies.index') }}" class="{{ request()->routeIs('movies.*') ? 'active' : '' }}">
                        <i class="bi bi-camera-reels me-2"></i> Phim
                    </a>
                    <a href="{{ route('genre.index') }}" class="{{ request()->routeIs('genre.*') ? 'active' : '' }}">
                        <i class="bi bi-tags me-2"></i> Thể loại
                    </a>
                    <a href="{{ route('studio.index') }}" class="{{ request()->routeIs('studio.*') ? 'active' : '' }}">
                        <i class="bi bi-building me-2"></i> Hãng sản xuất
                    </a>
                    <a href="{{ route('ageRating.index') }}" class="{{ request()->routeIs('ageRating.*') ? 'active' : '' }}">
                        <i class="bi bi-shield-check me-2"></i> Kiểm duyệt
                    </a>
                </div>

                <!-- CINEMA -->
                <a data-bs-toggle="collapse" href="#cinemaMenu" class="d-flex align-items-center">
                    <i class="bi bi-building me-2"></i> Rạp chiếu
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <div class="collapse submenu {{ request()->routeIs('screeningRoom.*','seat.*','seatType.*') ? 'show' : '' }}" id="cinemaMenu">
                    <a href="{{ route('screeningRoom.index') }}" class="{{ request()->routeIs('screeningRoom.*') ? 'active' : '' }}">
                        <i class="bi bi-tv me-2"></i> Phòng chiếu
                    </a>
                    <a href="{{ route('screenType.index') }}" class="{{ request()->routeIs('screenType.*') ? 'active' : '' }}">
                        <i class="bi bi-tv me-2"></i> Loại phòng chiếu
                    </a>
                    <a href="{{ route('seat.index') }}" class="{{ request()->routeIs('seat.*') ? 'active' : '' }}">
                        <i class="bi bi-grid me-2"></i> Ghế
                    </a>
                    <a href="{{ route('seatType.index') }}" class="{{ request()->routeIs('seatType.*') ? 'active' : '' }}">
                        <i class="bi bi-star me-2"></i> Loại ghế
                    </a>
                </div>

                <!-- FOOD -->
                <a data-bs-toggle="collapse" href="#foodMenu" class="d-flex align-items-center">
                    <i class="bi bi-cup-straw me-2"></i> Đồ ăn
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <div class="collapse submenu {{ request()->routeIs('food.*','foodInvoice.*') ? 'show' : '' }}" id="foodMenu">
                    <a href="{{ route('food.index') }}" class="{{ request()->routeIs('food.*') ? 'active' : '' }}">
                        <i class="bi bi-egg-fried me-2"></i> Đồ ăn
                    </a>
                    <a href="{{ route('foodInvoice.index') }}" class="{{ request()->routeIs('foodInvoice.*') ? 'active' : '' }}">
                        <i class="bi bi-receipt me-2"></i> Hóa đơn
                    </a>
                </div>

                <!-- PAYMENT -->
                <a href="{{ route('paymentMethod.index') }}" class="{{ request()->routeIs('paymentMethod.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card me-2"></i> Thanh toán
                </a>

                <!-- TICKET -->
                <a href="{{ route('ticket.index') }}" class="{{ request()->routeIs('ticket.*') ? 'active' : '' }}">
                    <i class="bi bi-ticket-perforated me-2"></i> Vé
                </a>

                <!-- SHOWTIME -->
                <a href="{{ route('showTime.index') }}" class="{{ request()->routeIs('showTime.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event me-2"></i> Lịch chiếu
                </a>
            </nav>
        </div>

        <!-- USER cố định dưới đáy -->
        <div class="sidebar-user">
            <div class="d-flex align-items-center mb-2">
                <div class="rounded-circle bg-light text-dark fw-bold d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A',0,1)) }}
                </div>
                <div class="ms-2">
                    <div class="fw-semibold">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <small>{{ auth()->user()->email ?? 'admin@cinema.com' }}</small>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
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
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-list toggle-btn" onclick="toggleSidebar()"></i>
                <h5 class="mb-0">@yield('page-title','Dashboard')</h5>
            </div>
            <span>{{ now()->format('d/m/Y') }}</span>
        </div>
        @yield('content')
    </div>

    <!-- TOAST -->
    @if(session('success') || session('error'))
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
        <div id="liveToast" class="toast align-items-center text-white border-0 {{ session('success') ? 'bg-success' : 'bg-danger' }}" role="alert" data-bs-delay="3000" data-bs-autohide="true">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('success') ?? session('error') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active')
        }
        document.addEventListener("DOMContentLoaded", function() {
            const toastEl = document.getElementById('liveToast');
            if (toastEl) {
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
            }
        });
    </script>
    @stack('js')
</body>

</html>