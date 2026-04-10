<style>
    .navbar {
        transition: all .35s ease;
        background: #0B0D13;
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
</style>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">

        <a href="{{ route('home') }}" class="navbar-brand">VAI cinema</a>

        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <ul class="navbar-nav mx-auto">
            <li class="nav-item"><a href="{{ route('home') }}" class="nav-link ">Trang chủ</a></li>
            <li class="nav-item"><a href="{{ route('system.movie')}}" class="nav-link">Lịch chiếu</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Giá vé</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Tin tức</a></li>
            <li class="nav-item"><a href="{{ route('contact') }}" class="nav-link">Liên hệ</a></li>
            <li class="nav-item"><a href="" class="nav-link">Giới thiệu</a></li>
        </ul>

        <div class="d-flex gap-2">

            @if(session('customer'))

            <div class="dropdown">

                <a class="btn nav-btn dropdown-toggle d-flex align-items-center gap-2"
                    data-bs-toggle="dropdown">
                    <!-- Avatar/Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                        viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8 16A8 8 0 1 1 8 0a8 8 0 0 1 0 16m.847-8.145a2.502 2.502 0 1 0-1.694 0C5.471 8.261 4 9.775 4 11c0 .395.145.995 1 .995h6c.855 0 1-.6 1-.995c0-1.224-1.47-2.74-3.153-3.145" />
                    </svg>

                    <!-- Font Awesome icon -->
                    <i class="fa fa-user-circle"></i>

                    <!-- Tên khách hàng -->
                    <span class="fw-semibold">
                        {{ session('customer')->fullName }}
                    </span>
                </a>


                <ul class="dropdown-menu dropdown-menu-end">

                    <li>
                        <a class="dropdown-item"
                            href="#">
                            Thông tin cá nhân
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item text-danger"
                            href="{{ route('home') }}}">
                            Đăng xuất
                        </a>
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