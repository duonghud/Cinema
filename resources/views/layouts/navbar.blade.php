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
</style>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">

        <a href="" class="navbar-brand">VAI cinema</a>

        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a href="#" class="nav-link ">Trang chủ</a></li>
                <li class="nav-item"><a href="" class="nav-link">Lịch chiếu</a></li>
                <li class="nav-item"><a href="" class="nav-link">Khuyến mãi</a></li>
                <li class="nav-item"><a href="" class="nav-link">Giá vé</a></li>
                <li class="nav-item"><a href="" class="nav-link">Tin tức</a></li>
                <li class="nav-item"><a href="layout/contact" class="nav-link">Liên hệ</a></li>
                <li class="nav-item"><a href="" class="nav-link">Giới thiệu</a></li>
            </ul>

            <div class="d-flex gap-2">
                <a href="#" class="btn nav-btn btn-register">Đăng ký</a>
                <a class="btn nav-btn btn-login" data-bs-toggle="modal" data-bs-target="#loginModal">Đăng nhập</a>
            </div>
    </div>
</nav>


<!-- LOGIN MODAL -->
<div class="modal fade" id="loginModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3">

            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Đăng nhập</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" placeholder="Nhập email">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" placeholder="Nhập mật khẩu">
                    </div>

                    <button type="submit" class="btn w-100 btn-login mt-2">
                        Đăng nhập
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>


<script>
    window.addEventListener("scroll", () => {
        const nav = document.querySelector(".navbar");
        nav.classList.toggle("scrolled", window.scrollY > 50);
    });
</script>
