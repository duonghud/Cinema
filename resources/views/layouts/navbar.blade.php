<style>
    .navbar {
        transition: all .35s ease;
        background: #10141b;
        padding: 14px 0;
    }

    .navbar.scrolled {
        background: rgba(11, 13, 19, .65);
        backdrop-filter: blur(14px) saturate(180%);
        box-shadow: 0 4px 20px rgba(0, 0, 0, .35);
        padding: 10px 0;

    }

    .navbar-brand {
        font-weight: 700;
        font-size: 1.5rem;
        letter-spacing: 1px;
        color: #fff !important;
    }

    .navbar-nav .nav-link {
        position: relative;
        color: #fff !important;
        font-weight: 500;
        margin: 0 10px;
        transition: .3s;
    }

    .navbar-nav .nav-link:hover {
        color: #D93F40 !important;
    }

    .navbar-nav .nav-link::after {
        content: "";
        position: absolute;
        width: 0%;
        height: 2px;
        left: 50%;
        bottom: -6px;
        background: #D93F40;
        transition: .3s;
        transform: translateX(-50%);
    }

    .navbar-nav .nav-link:hover::after,
    .navbar-nav .nav-link.active::after {
        width: 70%;
    }

    .nav-btn {
        padding: 10px 26px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 15px;
        letter-spacing: .3px;
        transition: all .25s ease;
    }

    .btn-register {
        background: transparent;
        border: 1px solid #ffffff;
        color: #fff;
    }

    .btn-register:hover {
        background: #1E293B;
        color: #ffffff;
        border: 1px solid #ffffff;
        transform: translateY(1px) scale(1.03);
    }

    .btn-login {
        background: linear-gradient(135deg, #86171C, #EC2931);
        color: #fff;
        border: none;
    }

    .btn-login:hover {
        transform: translateY(1px) scale(1.03);
        color: #fff;
    }

    .navbar-toggler {
        border: none;
    }

    .navbar-toggler:focus {
        box-shadow: none;
    }

    .navbar-toggler-icon {
        filter: invert(1);
    }

    .navbar-brand {
        font-family: 'Julee', cursive;
    }

    .dropdown-menu {
        background-color: #ffffff;
        opacity: 80%;
    }

    .customer-dropdown {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: white;
        padding: 8px 14px;
        border-radius: 50px;
        transition: 0.3s;
    }

    .customer-dropdown:hover {
        background: rgba(255, 255, 255, 0.15);
        color: #ffc107;
    }

    .customer-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ffc107, #ff9800);
        color: #111;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
    }

    .customer-name {
        max-width: 140px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">

        <a href="{{ route('home') }}" class="navbar-brand">VAI cinema</a>

        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <ul class="navbar-nav mx-auto">
            <li class="nav-item"><a href="{{ route('home') }}" class="nav-link ">Trang chủ</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Lịch chiếu</a></li>
            <li class="nav-item"><a href="{{ route('ticket.price') }}" class="nav-link">Giá vé</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Tin tức</a></li>
            <li class="nav-item"><a href="{{ route('contact') }}" class="nav-link">Liên hệ</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Giới thiệu</a></li>
        </ul>

        <div class="d-flex gap-2">

            @if(session('customer'))

            <div class="dropdown">

                <a class="btn nav-btn dropdown-toggle customer-dropdown d-flex align-items-center gap-2"
                    data-bs-toggle="dropdown">

                    <div class="customer-avatar">
                        {{ strtoupper(substr(session('customer')->fullName, 0, 2)) }}
                    </div>

                    <span class="fw-semibold customer-name">
                        {{ session('customer')->fullName }}
                    </span>
                </a>


                <ul class="dropdown-menu dropdown-menu-end">

                    <li>
                        <a class="dropdown-item"
                            href="{{ route('system.profile') }}">
                            Thông tin cá nhân
                        </a>
                    </li>

                    <li>
                        <form action="{{ route('customer.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                Đăng xuất
                            </button>
                        </form>

                    </li>

                </ul>

            </div>

            @else

            <a href="{{ route('customer.register.form') }}"
                class="btn nav-btn btn-register">
                Đăng ký
            </a>

            <a href="{{ route('auth.customerLogin') }}"
                class="btn nav-btn btn-login">
                Đăng nhập
            </a>

            @endif

        </div>
    </div>
</nav>




<script>
    window.addEventListener("scroll", () => {
        const nav = document.querySelector(".navbar");
        nav.classList.toggle("scrolled", window.scrollY > 50);
    });
</script>