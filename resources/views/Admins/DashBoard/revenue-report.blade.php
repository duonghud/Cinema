@extends('layouts.appAdmin')
@section('title', $reportTitle)
@section('page-title', $reportTitle)

@section('content')
<style>
    .report-card {
        border: 0;
        border-radius: 20px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
    }

    .summary-box {
        border-radius: 18px;
        background: linear-gradient(135deg, #111827, #ec4899 130%);
        color: #fff;
        padding: 20px;
        height: 100%;
    }
</style>

<div class="container-fluid px-0">
    <div class="card report-card mb-4">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <h4 class="fw-bold mb-1">{{ $reportTitle }}</h4>
                    <p class="text-muted mb-0">{{ $reportDescription }}</p>
                </div>

                <form method="GET" class="d-flex align-items-center gap-2">
                    <label for="filterValue" class="fw-semibold mb-0">{{ $filterLabel }}</label>

                    @if($filterKey === 'year')
                        <select name="{{ $filterKey }}" id="filterValue" class="form-select">
                            @foreach($filterOptions as $option)
                                <option value="{{ $option }}" {{ (string) $filterValue === (string) $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <input type="month" name="{{ $filterKey }}" id="filterValue" value="{{ $filterValue }}" class="form-control">
                    @endif

                    <button type="submit" class="btn btn-dark">Lọc</button>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="summary-box">
                <div class="small text-white-50">Doanh thu vé</div>
                <div class="fs-3 fw-bold">{{ number_format($summary['ticketRevenue'], 0, ',', '.') }} đ</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-box">
                <div class="small text-white-50">Doanh thu đồ ăn</div>
                <div class="fs-3 fw-bold">{{ number_format($summary['foodRevenue'], 0, ',', '.') }} đ</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-box">
                <div class="small text-white-50">Tổng doanh thu</div>
                <div class="fs-3 fw-bold">{{ number_format($summary['totalRevenue'], 0, ',', '.') }} đ</div>
            </div>
        </div>
    </div>

    <div class="card report-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mốc thời gian</th>
                            <th>Doanh thu vé</th>
                            <th>Doanh thu đồ ăn</th>
                            <th>Tổng doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            <tr>
                                <td class="fw-semibold">{{ $row['label'] }}</td>
                                <td>{{ number_format($row['ticketRevenue'], 0, ',', '.') }} đ</td>
                                <td>{{ number_format($row['foodRevenue'], 0, ',', '.') }} đ</td>
                                <td class="fw-bold text-success">{{ number_format($row['totalRevenue'], 0, ',', '.') }} đ</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Chưa có dữ liệu doanh thu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
