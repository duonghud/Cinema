@extends('layouts.loginAdmin')

@section('title', 'Đăng nhập Admin')

@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center g-4">
            <div class="col-lg-5 d-none d-lg-block">
                <div class="pe-lg-4">
                    <span class="badge rounded-pill px-3 py-2 mb-3" style="background: rgba(245, 1, 143, 0.18); color: #f9a8d4;">
                        CineMax Admin Portal
                    </span>
                    <h1 class="fw-bold mb-3 text-white" style="font-size: 3rem; line-height: 1.1;">
                        Điều hành rạp chiếu bằng một cái nhìn.
                    </h1>
                    <p class="mb-4 text-light" style="max-width: 520px;">
                        Đăng nhập để truy cập dashboard, quản lý phim, lịch chiếu, vé và hệ thống vận hành của rạp.
                    </p>
                </div>
            </div>

            <div class="col-12 col-md-8 col-lg-5 col-xl-4">
                <div class="card border-0 shadow-lg" style="background: rgba(15, 23, 42, 0.86); backdrop-filter: blur(12px); border-radius: 24px;">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                                 style="width: 72px; height: 72px; background: linear-gradient(135deg, #f5018f, #eeccf3); color: #111827; font-size: 1.75rem;">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>
                            <h2 class="fw-bold mb-2 text-white">Đăng nhập quản trị</h2>
                            <p class="mb-0 text-secondary">Sử dụng tài khoản admin để tiếp tục.</p>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">{{ $errors->first() }}</div>
                        @endif

                        <form action="{{ route('admin.login.post') }}" method="POST" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label text-light">Email</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text border-0" style="background: #1f2937; color: #9ca3af;">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input
                                        type="email"
                                        class="form-control border-0 text-white @error('email') is-invalid @enderror"
                                        id="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        placeholder="admin@cinemax.com"
                                        style="background: #1f2937; box-shadow: none;"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label text-light">Mật khẩu</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text border-0" style="background: #1f2937; color: #9ca3af;">
                                        <i class="bi bi-key"></i>
                                    </span>
                                    <input
                                        type="password"
                                        class="form-control border-0 text-white @error('password') is-invalid @enderror"
                                        id="password"
                                        name="password"
                                        placeholder="Nhập mật khẩu"
                                        style="background: #1f2937; box-shadow: none;"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-lg fw-semibold text-white"
                                        style="background: linear-gradient(135deg, #f5018f, #db2777); border: none; border-radius: 14px;">
                                    Đăng nhập
                                </button>
                            </div>
                        </form>

                        <div class="mt-4 pt-3 border-top border-secondary-subtle text-center text-secondary small">
                            Chỉ dành cho nhân sự quản trị được cấp quyền.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection