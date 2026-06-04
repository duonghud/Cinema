@extends('layouts.appAdmin')
@section('title', $reportTitle)
@section('page-title', $reportTitle)

@section('content')

{{-- ── Load Iconify Web Component ── --}}
<script src="https://cdn.jsdelivr.net/npm/iconify-icon@2.1.0/dist/iconify-icon.min.js"></script>

<style>
    :root {
        --primary:      #6366f1;
        --primary-dark: #4f46e5;
        --accent:       #06b6d4;
        --success:      #10b981;
        --warning:      #f59e0b;
        --danger:       #ef4444;
        --surface:      #f8fafc;
        --border:       #e2e8f0;
        --text:         #1e293b;
        --muted:        #64748b;
    }

    .report-card { border: 0; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,.06); }

    /* ── Summary boxes ── */
    .summary-box {
        border-radius: 14px; padding: 22px 20px; height: 100%;
        color: #fff; position: relative; overflow: hidden; cursor: pointer;
        transition: transform .18s, box-shadow .18s;
    }
    .summary-box:hover { transform: translateY(-3px); box-shadow: 0 8px 32px rgba(0,0,0,.18); }
    .summary-box::after {
        content: ''; position: absolute; width: 80px; height: 80px;
        border-radius: 50%; background: rgba(255,255,255,.12);
        right: -16px; bottom: -16px;
    }
    .summary-box.ticket { background: linear-gradient(135deg,#6366f1,#818cf8); }
    .summary-box.food   { background: linear-gradient(135deg,#06b6d4,#38bdf8); }
    .summary-box.total  { background: linear-gradient(135deg,#10b981,#34d399); }
    .summary-box .s-icon  { font-size: 1.6rem; margin-bottom: 8px; }
    .summary-box .s-label { font-size: .8rem; opacity: .85; margin-bottom: 4px; }
    .summary-box .s-val   { font-size: 1.55rem; font-weight: 700; letter-spacing: -.5px; }
    .summary-box .s-hint  {
        font-size: .72rem; opacity: .75; margin-top: 8px;
        display: flex; align-items: center; gap: 4px;
    }

    /* ── Highlight row (top/bottom) on page ── */
    .highlight-row { display: flex; gap: 12px; flex-wrap: wrap; }
    .hl-card {
        flex: 1; min-width: 200px; border-radius: 12px; padding: 14px 16px;
        display: flex; align-items: flex-start; gap: 12px;
    }
    .hl-card .hl-icon  { font-size: 1.5rem; flex-shrink: 0; }
    .hl-card .hl-badge { font-size: .68rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; margin-bottom: 3px; display: block; }
    .hl-card .hl-name  { font-weight: 700; font-size: .92rem; color: var(--text); margin-bottom: 2px; }
    .hl-card .hl-rev   { font-weight: 700; font-size: 1rem; }
    .hl-card .hl-sub   { font-size: .75rem; color: var(--muted); margin-top: 1px; }
    .hl-section-title  {
        font-size: .78rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .06em; color: var(--muted); margin-bottom: 10px;
        display: flex; align-items: center; gap: 6px;
    }

    /* ── Chart tabs ── */
    .chart-tab-btn {
        background: var(--surface); border: 1.5px solid var(--border);
        border-radius: 8px; padding: 6px 16px; font-weight: 600;
        font-size: .83rem; color: var(--muted); cursor: pointer; transition: all .18s;
    }
    .chart-tab-btn.active  { background: var(--primary); color: #fff; border-color: var(--primary); }
    .chart-tab-btn:hover:not(.active) { border-color: var(--primary); color: var(--primary); background: #eef2ff; }

    .chart-panel { display: none; }
    .chart-panel.active { display: block; }
    .legend-dot { display: inline-block; width: 11px; height: 11px; border-radius: 3px; margin-right: 5px; }

    /* ── Data table ── */
    #revenueTable thead th {
        background: var(--surface); color: var(--muted); font-size: .78rem;
        text-transform: uppercase; letter-spacing: .05em;
        border-bottom: 2px solid var(--border); padding: 12px 14px;
    }
    #revenueTable tbody tr { transition: background .12s; }
    #revenueTable tbody tr:hover { background: #eef2ff; }
    #revenueTable tbody td { padding: 11px 14px; font-size: .9rem; color: var(--text); border-color: var(--border); }

    /* ── Pagination ── */
    .pagination-wrap {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 10px;
        padding-top: 14px; border-top: 1px solid var(--border); margin-top: 4px;
    }
    .pagination-info { font-size: .82rem; color: var(--muted); }
    .pagination-controls { display: flex; align-items: center; gap: 4px; }
    .page-btn {
        min-width: 32px; height: 32px; padding: 0 8px;
        border: 1.5px solid var(--border); background: #fff;
        border-radius: 7px; font-size: .82rem; font-weight: 600;
        color: var(--text); cursor: pointer; transition: all .14s;
        display: inline-flex; align-items: center; justify-content: center;
    }
    .page-btn:hover:not(:disabled) { border-color: var(--primary); color: var(--primary); background: #eef2ff; }
    .page-btn.active  { background: var(--primary); border-color: var(--primary); color: #fff; }
    .page-btn:disabled { opacity: .35; cursor: not-allowed; }
    .page-size-select {
        height: 32px; padding: 0 8px;
        border: 1.5px solid var(--border); border-radius: 7px;
        font-size: .82rem; font-weight: 600; color: var(--text);
        background: #fff; cursor: pointer;
    }

    #barChart, #lineChart { cursor: pointer; }

    /* ── Iconify sizing helpers ── */
    iconify-icon { display: inline-flex; vertical-align: middle; }
    .s-icon iconify-icon { font-size: 1.6rem; }
    .s-hint iconify-icon { font-size: .7rem; }
</style>

@php
    $filterDisplay = $filterKey === 'year'
        ? (string) $filterValue
        : \Carbon\Carbon::createFromFormat('Y-m', $filterValue)->format('m/Y');

    $firstPeriod = $rows->isNotEmpty() ? ($rows->first()['period'] ?? '') : '';

    $summaryPeriod = $filterKey === 'year' ? (string) $filterValue : $firstPeriod;
@endphp

<div class="container-fluid px-0">

    {{-- ── Filter card ── --}}
    <div class="card report-card mb-4">
        <div class="card-body py-3">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <h4 class="fw-bold mb-1" style="color:var(--text)">{{ $reportTitle }}</h4>
                    <p class="mb-0" style="color:var(--muted);font-size:.875rem">{{ $reportDescription }}</p>
                </div>
                <form method="GET" class="d-flex align-items-center gap-2">
                    <label for="filterValue" class="fw-semibold mb-0"
                           style="color:var(--text);white-space:nowrap">{{ $filterLabel }}</label>
                    @if($filterKey === 'year')
                        <select name="{{ $filterKey }}" id="filterValue"
                                class="form-select" style="border-color:var(--border)">
                            @foreach($filterOptions as $option)
                                <option value="{{ $option }}"
                                    {{ (string)$filterValue === (string)$option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <input type="month" name="{{ $filterKey }}" id="filterValue"
                               value="{{ $filterValue }}"
                               class="form-control" style="border-color:var(--border)">
                    @endif
                    <button type="submit" class="btn text-white fw-semibold px-4"
                            style="background:var(--primary);border:none;border-radius:8px">Lọc</button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Summary boxes ── --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="summary-box ticket"
                 onclick="openModal('{{ $summaryPeriod }}', 'ticket', 'Doanh thu vé – {{ $filterDisplay }}')">
                <div class="s-icon">
                    <iconify-icon icon="mdi:ticket-outline"></iconify-icon>
                </div>
                <div class="s-label">Doanh thu vé</div>
                <div class="s-val">{{ number_format($summary['ticketRevenue'], 0, ',', '.') }} đ</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-box food"
                 onclick="openModal('{{ $summaryPeriod }}', 'food', 'Doanh thu đồ ăn – {{ $filterDisplay }}')">
                <div class="s-icon">
                    <iconify-icon icon="mdi:food-outline"></iconify-icon>
                </div>
                <div class="s-label">Doanh thu đồ ăn</div>
                <div class="s-val">{{ number_format($summary['foodRevenue'], 0, ',', '.') }} đ</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-box total"
                 onclick="openModal('{{ $summaryPeriod }}', 'all', 'Tổng doanh thu – {{ $filterDisplay }}')">
                <div class="s-icon">
                    <iconify-icon icon="mdi:bank-outline"></iconify-icon>
                </div>
                <div class="s-label">Tổng doanh thu</div>
                <div class="s-val">{{ number_format($summary['totalRevenue'], 0, ',', '.') }} đ</div>
            </div>
        </div>
    </div>
    

    {{-- ── Chart card ── --}}
    <div class="card report-card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <h5 class="fw-bold mb-0" style="color:var(--text)">
                    Biểu đồ doanh thu
                    <span class="badge ms-2 align-middle"
                          style="background:var(--primary);font-size:.75rem;border-radius:6px;padding:4px 10px;font-weight:600">
                        {{ $filterDisplay }}
                    </span>
                </h5>
                <div class="d-flex gap-2">
                    <button class="chart-tab-btn active" onclick="switchChart('bar',  this)">Cột</button>
                    <button class="chart-tab-btn"        onclick="switchChart('line', this)">Đường</button>
                </div>
            </div>
            <div class="mb-3 d-flex flex-wrap gap-3" id="chart-legend" style="font-size:.85rem;color:var(--text)">
                <span><span class="legend-dot" style="background:#6366f1"></span>Doanh thu vé</span>
                <span><span class="legend-dot" style="background:#06b6d4"></span>Doanh thu đồ ăn</span>
                <span><span class="legend-dot" style="background:#10b981"></span>Tổng doanh thu</span>
            </div>
            <div class="chart-panel active" id="panel-bar">
                <canvas id="barChart" style="max-height:360px"></canvas>
            </div>
            <div class="chart-panel" id="panel-line">
                <canvas id="lineChart" style="max-height:360px"></canvas>
            </div>
            <div class="chart-panel" id="panel-pie">
                <div class="row align-items-center">
                    <div class="col-md-5 mx-auto" style="max-height:340px">
                        <canvas id="pieChart"></canvas>
                    </div>
                    <div class="col-md-6 d-flex flex-column justify-content-center ps-md-4">
                        <h6 class="fw-bold mb-3" style="color:var(--muted)">Tỷ lệ trong kỳ</h6>
                        <div class="d-flex align-items-center gap-2 mb-3 p-3 rounded-3"
                             style="background:#eef2ff;cursor:pointer"
                             onclick="openModal('{{ $summaryPeriod }}', 'ticket', 'Doanh thu vé')">
                            <span class="legend-dot" style="background:#6366f1;width:16px;height:16px;border-radius:4px;flex-shrink:0"></span>
                            <div>
                                <div style="font-size:.78rem;color:var(--muted)">Doanh thu vé</div>
                                <div class="fw-bold" style="color:#6366f1">{{ number_format($summary['ticketRevenue'], 0, ',', '.') }} đ</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 p-3 rounded-3"
                             style="background:#ecfeff;cursor:pointer"
                             onclick="openModal('{{ $summaryPeriod }}', 'food', 'Doanh thu đồ ăn')">
                            <span class="legend-dot" style="background:#06b6d4;width:16px;height:16px;border-radius:4px;flex-shrink:0"></span>
                            <div>
                                <div style="font-size:.78rem;color:var(--muted)">Doanh thu đồ ăn</div>
                                <div class="fw-bold" style="color:#06b6d4">{{ number_format($summary['foodRevenue'], 0, ',', '.') }} đ</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Data table ── --}}
    <div class="card report-card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <h5 class="fw-bold mb-0" style="color:var(--text)">
                    Chi tiết doanh thu
                    <span class="badge ms-2 align-middle"
                          style="background:var(--primary);font-size:.75rem;border-radius:6px;padding:4px 10px;font-weight:600">
                        {{ $filterDisplay }}
                    </span>
                </h5>
                @if($filterKey !== 'year')
                <div class="d-flex align-items-center gap-2">
                    <span style="font-size:.82rem;color:var(--muted)">Hiển thị:</span>
                    <select class="page-size-select" id="pageSizeSelect" onchange="changePageSize(this.value)">
                        <option value="7">7 hàng</option>
                        <option value="10" selected>10 hàng</option>
                        <option value="15">15 hàng</option>
                        <option value="31">Tất cả</option>
                    </select>
                </div>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="revenueTable">
                    <thead>
                        <tr>
                            <th>Mốc thời gian</th>
                            <th>
                                Doanh thu vé
                                <small style="color:var(--primary);font-size:.7rem;font-weight:400">(nhấn hàng)</small>
                            </th>
                            <th>Doanh thu đồ ăn</th>
                            <th>Tổng doanh thu</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse($rows as $row)
                            @php
                                $rowPeriod = $row['period'] ?? $row['label'];
                            @endphp
                            <tr data-row data-period="{{ $rowPeriod }}" data-label="{{ $row['label'] }}">
                                <td class="fw-semibold">{{ $row['label'] }}</td>
                                <td>
                                    <span style="color:#6366f1;font-weight:600;cursor:pointer"
                                          onclick="event.stopPropagation();openModal('{{ $rowPeriod }}','ticket','Hóa đơn vé – {{ $row['label'] }}')">
                                        {{ number_format($row['ticketRevenue'], 0, ',', '.') }} đ
                                        <iconify-icon icon="mdi:arrow-top-right" style="font-size:.7rem;opacity:.6"></iconify-icon>
                                    </span>
                                </td>
                                <td>
                                    <span style="color:#06b6d4;font-weight:600;cursor:pointer"
                                          onclick="event.stopPropagation();openModal('{{ $rowPeriod }}','food','Hóa đơn đồ ăn – {{ $row['label'] }}')">
                                        {{ number_format($row['foodRevenue'], 0, ',', '.') }} đ
                                        <iconify-icon icon="mdi:arrow-top-right" style="font-size:.7rem;opacity:.6"></iconify-icon>
                                    </span>
                                </td>
                                <td>
                                    <span style="color:#10b981;font-weight:700;cursor:pointer"
                                          onclick="event.stopPropagation();openModal('{{ $rowPeriod }}','all','Tất cả hóa đơn – {{ $row['label'] }}')">
                                        {{ number_format($row['totalRevenue'], 0, ',', '.') }} đ
                                        <iconify-icon icon="mdi:arrow-top-right" style="font-size:.7rem;opacity:.6"></iconify-icon>
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5" style="color:var(--muted)">
                                    Chưa có dữ liệu doanh thu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($filterKey !== 'year')
            <div class="pagination-wrap" id="paginationWrap">
                <div class="pagination-info"     id="paginationInfo"></div>
                <div class="pagination-controls" id="paginationControls"></div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ── Nhúng dữ liệu phim ── --}}
<script>
const _pageMovieData = {
    topMovie    : @json($topMovie    ?? null),
    bottomMovie : @json($bottomMovie ?? null),
    movieList   : @json($movieList   ?? []),
};
</script>

{{-- ── Invoice Modal ── --}}
@include('Admins.DashBoard.revenue.modal')

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
// ── Data từ Laravel ──────────────────────────────────────────────
const labels     = @json($rows->pluck('label'));
const ticketData = @json($rows->pluck('ticketRevenue'));
const foodData   = @json($rows->pluck('foodRevenue'));
const totalData  = @json($rows->pluck('totalRevenue'));
const periods    = @json($rows->pluck('period')->toArray());

const summaryTicket = {{ $summary['ticketRevenue'] }};
const summaryFood   = {{ $summary['foodRevenue'] }};

// ── Chart helpers ─────────────────────────────────────────────────
const C = {
    ticket : '#6366f1', ticketA : 'rgba(99,102,241,0.15)',
    food   : '#06b6d4', foodA   : 'rgba(6,182,212,0.12)',
    total  : '#10b981', totalA  : 'rgba(16,185,129,0.10)',
};
const tooltipPlugin = {
    callbacks: {
        label    : ctx => ' ' + fmt(ctx.parsed.y ?? ctx.parsed),
        afterLabel: ()  => '  ← Nhấn để xem hóa đơn',
    }
};
const axisDefaults = {
    grid : { color: 'rgba(0,0,0,0.04)' },
    ticks: {
        color: '#64748b',
        callback: v => new Intl.NumberFormat('vi-VN', { notation: 'compact' }).format(v) + ' đ'
    }
};

function makeClickHandler(chart) {
    return function (evt) {
        const pts = chart.getElementsAtEventForMode(evt, 'index', { intersect: false }, true);
        if (!pts.length) return;
        const idx   = pts[0].index;
        const dsIdx = pts[0].datasetIndex;
        const typeMap = ['ticket', 'food', 'all'];
        const type    = typeMap[dsIdx] ?? 'all';
        openModal(periods[idx] ?? labels[idx], type, labels[idx]);
    };
}

const barChart = new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            { label: 'Doanh thu vé',    data: ticketData, backgroundColor: 'rgba(99,102,241,0.82)',  borderRadius: 6, borderSkipped: false },
            { label: 'Doanh thu đồ ăn', data: foodData,   backgroundColor: 'rgba(6,182,212,0.82)',   borderRadius: 6, borderSkipped: false },
            
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false }, tooltip: tooltipPlugin },
        scales : { y: axisDefaults, x: { grid: { display: false }, ticks: { color: '#64748b' } } }
    }
});
document.getElementById('barChart').addEventListener('click', makeClickHandler(barChart));

const lineChart = new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [
            { label: 'Doanh thu vé',    data: ticketData, borderColor: C.ticket, backgroundColor: C.ticketA, tension: .4, fill: true, pointBackgroundColor: C.ticket, pointRadius: 5, pointHoverRadius: 8 },
            { label: 'Doanh thu đồ ăn', data: foodData,   borderColor: C.food,   backgroundColor: C.foodA,   tension: .4, fill: true, pointBackgroundColor: C.food,   pointRadius: 5, pointHoverRadius: 8 },
           
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false }, tooltip: tooltipPlugin },
        scales : { y: axisDefaults, x: { grid: { display: false }, ticks: { color: '#64748b' } } }
    }
});
document.getElementById('lineChart').addEventListener('click', makeClickHandler(lineChart));

// ── Pagination ────────────────────────────────────────────────────
(function () {
    const wrap = document.getElementById('paginationWrap');
    if (!wrap) return;

    const tbody    = document.getElementById('tableBody');
    const info     = document.getElementById('paginationInfo');
    const controls = document.getElementById('paginationControls');
    const sizeSel  = document.getElementById('pageSizeSelect');

    let allRows  = Array.from(tbody.querySelectorAll('tr[data-row]'));
    let pageSize = parseInt(sizeSel?.value || 10);
    let curPage  = 1;

    function totalPages() { return Math.max(1, Math.ceil(allRows.length / pageSize)); }

    function render() {
        const total = allRows.length;
        const pages = totalPages();
        if (curPage > pages) curPage = pages;
        const start = (curPage - 1) * pageSize;
        const end   = Math.min(start + pageSize, total);

        allRows.forEach((tr, i) => { tr.style.display = (i >= start && i < end) ? '' : 'none'; });
        info.textContent = total > 0 ? `Hiển thị ${start + 1}–${end} / ${total} hàng` : '';

        controls.innerHTML = '';
        controls.appendChild(mkBtn('‹', curPage <= 1, () => { curPage--; render(); }));
        pageRange(curPage, pages).forEach(p => {
            if (p === '…') {
                const dot = document.createElement('span');
                dot.textContent = '…';
                dot.style.cssText = 'padding:0 6px;color:#94a3b8;font-size:.85rem';
                controls.appendChild(dot);
            } else {
                const b = mkBtn(p, false, () => { curPage = p; render(); });
                if (p === curPage) b.classList.add('active');
                controls.appendChild(b);
            }
        });
        controls.appendChild(mkBtn('›', curPage >= pages, () => { curPage++; render(); }));
    }

    function mkBtn(label, disabled, onClick) {
        const b = document.createElement('button');
        b.className = 'page-btn'; b.textContent = label; b.disabled = disabled;
        b.addEventListener('click', onClick);
        return b;
    }

    function pageRange(cur, total) {
        if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);
        if (cur <= 4)         return [1, 2, 3, 4, 5, '…', total];
        if (cur >= total - 3) return [1, '…', total - 4, total - 3, total - 2, total - 1, total];
        return [1, '…', cur - 1, cur, cur + 1, '…', total];
    }

    window.changePageSize = val => { pageSize = parseInt(val); curPage = 1; render(); };
    render();
})();
</script>
@endsection