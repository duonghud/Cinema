@extends('layouts.appAdmin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<style>
    .dashboard-card {
        border: 0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 14px 30px rgba(31, 41, 55, 0.08);
    }

    .dashboard-card .icon-box {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }

    .hero-panel {
        border-radius: 24px;
        background: linear-gradient(135deg, #111827, #1f2937 55%, #ec4899 120%);
        color: #fff;
        padding: 28px;
        box-shadow: 0 20px 40px rgba(17, 24, 39, 0.16);
    }

    .hero-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        font-size: 0.95rem;
    }

    .quick-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        border: 1px solid #eef2f7;
        border-radius: 16px;
        text-decoration: none;
        color: #111827;
        background: #fff;
        transition: 0.2s ease;
    }

    .quick-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
        color: #ec4899;
    }

    .section-card {
        border: 0;
        border-radius: 20px;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.06);
    }

    .movie-poster {
        width: 52px;
        height: 72px;
        object-fit: cover;
        border-radius: 12px;
        background: #e5e7eb;
    }

    .movie-poster-placeholder {
        width: 52px;
        height: 72px;
        border-radius: 12px;
        background: linear-gradient(135deg, #f3f4f6, #d1d5db);
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }
</style>

<div class="container-fluid px-0">
    <div class="hero-panel mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <span class="hero-chip mb-3">
                    CineMax Admin
                </span>
                <h3 class="fw-bold mb-2">Bảng điều khiển hệ thống rạp chiếu phim</h3>
                <p class="mb-0 text-white-50">
                    Theo dõi nhanh phim, khách hàng, lịch chiếu và doanh thu trong ngày từ một màn hình tổng quan.
                </p>
            </div>
            <div class="col-lg-4">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="bg-white bg-opacity-10 rounded-4 p-3">
                            <div class="text-white-50 small">Vé đã bán</div>
                            <div class="fs-3 fw-bold">{{ number_format($stats['soldTickets']) }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white bg-opacity-10 rounded-4 p-3">
                            <div class="text-white-50 small">Suất chiếu hôm nay</div>
                            <div class="fs-3 fw-bold">{{ number_format($stats['todayShowTimes']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="text-muted small">Tổng số phim</div>
                            <h3 class="fw-bold mb-0">{{ number_format($stats['totalMovies']) }}</h3>
                        </div>
                        <div class="icon-box bg-primary-subtle text-primary">
                            <i class="bi bi-film"></i>
                        </div>
                    </div>
                    <a href="{{ route('movies.index') }}" class="text-decoration-none">Xem danh sách phim</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="text-muted small">Khách hàng</div>
                            <h3 class="fw-bold mb-0">{{ number_format($stats['totalCustomers']) }}</h3>
                        </div>
                        <div class="icon-box bg-success-subtle text-success">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                    <a href="{{ route('customer.index') }}" class="text-decoration-none">Quản lý khách hàng</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="text-muted small">Phòng chiếu</div>
                            <h3 class="fw-bold mb-0">{{ number_format($stats['totalRooms']) }}</h3>
                        </div>
                        <div class="icon-box bg-warning-subtle text-warning">
                            <i class="bi bi-door-open"></i>
                        </div>
                    </div>
                    <a href="{{ route('screeningRoom.index') }}" class="text-decoration-none">Xem phòng chiếu</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="text-muted small">Nhân sự quản trị</div>
                            <h3 class="fw-bold mb-0">{{ number_format($stats['totalAdmins']) }}</h3>
                        </div>
                        <div class="icon-box bg-danger-subtle text-danger">
                            <i class="bi bi-person-badge"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.index') }}" class="text-decoration-none">Xem danh sách admin</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card section-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-semibold mb-0">Doanh thu hôm nay</h5>
                        <i class="bi bi-graph-up-arrow text-success"></i>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Vé xem phim</div>
                        <div class="fs-4 fw-bold text-dark">{{ number_format($stats['todayTicketRevenue'], 0, ',', '.') }} đ</div>
                    </div>
                    <div>
                        <div class="text-muted small">Đồ ăn & nước uống</div>
                        <div class="fs-4 fw-bold text-dark">{{ number_format($stats['todayFoodRevenue'], 0, ',', '.') }} đ</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card section-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-semibold mb-0">Truy cập nhanh</h5>
                        <span class="text-muted small">Đi đến tác vụ thường dùng</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('showTime.index') }}" class="quick-link">
                                <span><i class="bi bi-calendar-event me-2"></i>Lịch chiếu</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('ticket.index') }}" class="quick-link">
                                <span><i class="bi bi-ticket-perforated me-2"></i>Vé</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('food.index') }}" class="quick-link">
                                <span><i class="bi bi-cup-straw me-2"></i>Đồ ăn</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('paymentMethod.index') }}" class="quick-link">
                                <span><i class="bi bi-credit-card me-2"></i>Thanh toán</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-7">
            <div class="card section-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-semibold mb-0">Suất chiếu sắp tới</h5>
                        <a href="{{ route('showTime.index') }}" class="small text-decoration-none">Xem tất cả</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Phim</th>
                                    <th>Phòng</th>
                                    <th>Ngày</th>
                                    <th>Giờ chiếu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcomingShowTimes as $showTime)
                                    <tr>
                                        <td class="fw-medium">{{ $showTime->movie->movieTitle ?? 'Chưa có phim' }}</td>
                                        <td>{{ $showTime->room->roomName ?? 'Chưa có phòng' }}</td>
                                        <td>{{ \Illuminate\Support\Carbon::parse($showTime->showDate)->format('d/m/Y') }}</td>
                                        <td>{{ \Illuminate\Support\Carbon::parse($showTime->startTime)->format('H:i') }} - {{ \Illuminate\Support\Carbon::parse($showTime->endTime)->format('H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Chưa có suất chiếu sắp tới.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card section-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-semibold mb-0">Phim mới cập nhật</h5>
                        <a href="{{ route('movies.index') }}" class="small text-decoration-none">Quản lý phim</a>
                    </div>

                    @forelse($latestMovies as $movie)
                        <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                            @if($movie->poster)
                                <img
                                    src="{{ asset('posters/' . $movie->poster) }}"
                                    alt="{{ $movie->movieTitle }}"
                                    class="movie-poster">
                            @else
                                <div class="movie-poster-placeholder">
                                    <i class="bi bi-film"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $movie->movieTitle }}</div>
                                <div class="text-muted small">
                                    {{ optional($movie->releaseDate)->format('d/m/Y') ?? 'Chưa có ngày phát hành' }}
                                </div>
                                <div class="mt-1">
                                    <span class="badge bg-light text-dark">{{ $movie->studio->name ?? 'Chưa có hãng' }}</span>
                                    <span class="badge bg-secondary">{{ $movie->ageRating->code ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">Chưa có dữ liệu phim để hiển thị.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
