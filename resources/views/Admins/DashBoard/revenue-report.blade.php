@extends('layouts.appAdmin')
@section('title', $reportTitle)
@section('page-title', $reportTitle)

@section('content')
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
        color: #fff; position: relative; overflow: hidden;
    }
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

    /* ── Chart tabs ── */
    .chart-tab-btn {
        background: var(--surface); border: 1.5px solid var(--border);
        border-radius: 8px; padding: 6px 16px; font-weight: 600;
        font-size: .83rem; color: var(--muted); cursor: pointer; transition: all .18s;
    }
    .chart-tab-btn.active { background: var(--primary); color: #fff; border-color: var(--primary); }
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

    /* ── Invoice Modal ── */
    .invoice-modal-backdrop {
        display: none; position: fixed; inset: 0;
        background: rgba(15,23,42,.5); z-index: 1050;
        align-items: center; justify-content: center;
        backdrop-filter: blur(2px);
    }
    .invoice-modal-backdrop.open { display: flex; }
    .invoice-modal {
        background: #fff; border-radius: 18px;
        width: min(860px, 96vw); max-height: 90vh;
        display: flex; flex-direction: column;
        box-shadow: 0 32px 80px rgba(0,0,0,.2);
        animation: modalIn .2s ease;
    }
    @keyframes modalIn {
        from { opacity:0; transform: translateY(20px) scale(.97); }
        to   { opacity:1; transform: none; }
    }
    .invoice-modal-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 18px 24px 16px; border-bottom: 1px solid var(--border);
        background: var(--surface); border-radius: 18px 18px 0 0;
    }
    .invoice-modal-header h5 { margin: 0; font-weight: 700; font-size: 1rem; color: var(--text); }
    .invoice-modal-close {
        background: #e2e8f0; border: none; border-radius: 50%;
        width: 32px; height: 32px; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; color: var(--muted); transition: all .15s;
    }
    .invoice-modal-close:hover { background: var(--danger); color: #fff; }
    .invoice-modal-body   { flex: 1; overflow-y: auto; padding: 20px 24px; }
    .invoice-modal-footer {
        padding: 12px 24px; border-top: 1px solid var(--border);
        display: flex; justify-content: flex-end;
        background: var(--surface); border-radius: 0 0 18px 18px;
    }

    /* ── Stat boxes inside modal ── */
    .invoice-stat-row { display: flex; gap: 10px; margin-bottom: 16px; flex-wrap: wrap; }
    .invoice-stat {
        flex: 1; min-width: 130px; border-radius: 10px; padding: 12px 16px;
        border-left: 4px solid var(--primary); background: #eef2ff;
    }
    .invoice-stat.food  { border-color: var(--accent);  background: #ecfeff; }
    .invoice-stat.total { border-color: var(--success); background: #ecfdf5; }
    .invoice-stat .s-label { font-size: .75rem; color: var(--muted); margin-bottom: 2px; }
    .invoice-stat .s-val   { font-weight: 700; font-size: 1rem; color: var(--text); }

    /* ── Highlight cards (top/bottom) ── */
    .highlight-section { margin-bottom: 16px; }
    .highlight-section .hs-title {
        font-size: .82rem; font-weight: 600; color: var(--muted);
        text-transform: uppercase; letter-spacing: .04em; margin-bottom: 8px;
    }
    .highlight-card {
        flex: 1; min-width: 190px; border-radius: 10px; padding: 12px 14px;
    }
    .highlight-card .hc-badge {
        font-size: .7rem; font-weight: 600; letter-spacing: .03em;
        margin-bottom: 5px; display: block;
    }
    .highlight-card .hc-name  { font-weight: 600; font-size: .88rem; color: var(--text); margin-bottom: 3px; }
    .highlight-card .hc-rev   { font-weight: 700; font-size: .95rem; }
    .highlight-card .hc-sub   { font-size: .75rem; color: var(--muted); }

    /* ── Modal table ── */
    .modal-table thead th {
        background: var(--surface); color: var(--muted);
        font-size: .75rem; text-transform: uppercase;
        letter-spacing: .05em; border-bottom: 2px solid var(--border);
        padding: 10px 12px;
    }
    .modal-table tbody td { padding: 10px 12px; font-size: .86rem; border-color: var(--border); }
    .modal-table tbody tr:hover { background: #f8fafc; }

    /* ── Loading ── */
    .inv-loading {
        display: flex; flex-direction: column; align-items: center;
        justify-content: center; padding: 44px 0; color: var(--muted); gap: 12px;
    }
    .inv-spinner {
        width: 36px; height: 36px;
        border: 3px solid var(--border); border-top-color: var(--primary);
        border-radius: 50%; animation: spin .7s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    #barChart, #lineChart { cursor: pointer; }
</style>

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
            <div class="summary-box ticket">
                <div class="s-icon"><i class="bi bi-ticket-perforated-fill"></i></div>
                <div class="s-label">Doanh thu vé</div>
                <div class="s-val">{{ number_format($summary['ticketRevenue'], 0, ',', '.') }} đ</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-box food">
                <div class="s-icon"><i class="bi bi-fork-knife"></i></div>
                <div class="s-label">Doanh thu đồ ăn</div>
                <div class="s-val">{{ number_format($summary['foodRevenue'], 0, ',', '.') }} đ</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-box total">
                <div class="s-icon"><i class="bi bi-bank"></i></div>
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
                        {{ $filterKey === 'year' ? $filterValue : \Carbon\Carbon::parse($filterValue . '-01')->format('m/Y') }}
                    </span>
                </h5>
                <div class="d-flex gap-2">
                    <button class="chart-tab-btn active" onclick="switchChart('bar',  this)">Cột</button>
                    <button class="chart-tab-btn"        onclick="switchChart('line', this)">Đường</button>
                    <button class="chart-tab-btn"        onclick="switchChart('pie',  this)">Tròn</button>
                </div>
            </div>

            {{-- Legend --}}
            <div class="mb-3 d-flex flex-wrap gap-3" id="chart-legend" style="font-size:.85rem;color:var(--text)">
                <span><span class="legend-dot" style="background:#6366f1"></span>Doanh thu vé</span>
                <span><span class="legend-dot" style="background:#06b6d4"></span>Doanh thu đồ ăn</span>
                <span><span class="legend-dot" style="background:#10b981"></span>Tổng doanh thu</span>
            </div>

            {{-- Bar --}}
            <div class="chart-panel active" id="panel-bar">
                <canvas id="barChart" style="max-height:360px"></canvas>
            </div>

            {{-- Line --}}
            <div class="chart-panel" id="panel-line">
                <canvas id="lineChart" style="max-height:360px"></canvas>
            </div>

            {{-- Pie --}}
            <div class="chart-panel" id="panel-pie">
                <div class="row align-items-center">
                    <div class="col-md-5 mx-auto" style="max-height:340px">
                        <canvas id="pieChart"></canvas>
                    </div>
                    <div class="col-md-6 d-flex flex-column justify-content-center ps-md-4">
                        <h6 class="fw-bold mb-3" style="color:var(--muted)">Tỷ lệ trong kỳ</h6>
                        <div class="d-flex align-items-center gap-2 mb-3 p-3 rounded-3" style="background:#eef2ff">
                            <span class="legend-dot" style="background:#6366f1;width:16px;height:16px;border-radius:4px;flex-shrink:0"></span>
                            <div>
                                <div style="font-size:.78rem;color:var(--muted)">Doanh thu vé</div>
                                <div class="fw-bold" style="color:#6366f1">{{ number_format($summary['ticketRevenue'], 0, ',', '.') }} đ</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 p-3 rounded-3" style="background:#ecfeff">
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
                        {{ $filterKey === 'year' ? $filterValue : \Carbon\Carbon::parse($filterValue . '-01')->format('m/Y') }}
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
                            <th>Doanh thu vé</th>
                            <th>Doanh thu đồ ăn</th>
                            <th>Tổng doanh thu</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse($rows as $row)
                            <tr data-row style="cursor:pointer"
                                onclick="openInvoiceModal('{{ $row['period'] ?? $row['label'] }}', '{{ $row['label'] }}')">
                                <td class="fw-semibold">{{ $row['label'] }}</td>
                                <td style="color:#6366f1;font-weight:600">
                                    {{ number_format($row['ticketRevenue'], 0, ',', '.') }} đ
                                </td>
                                <td style="color:#06b6d4;font-weight:600">
                                    {{ number_format($row['foodRevenue'], 0, ',', '.') }} đ
                                </td>
                                <td style="color:#10b981;font-weight:700">
                                    {{ number_format($row['totalRevenue'], 0, ',', '.') }} đ
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

{{-- ── Invoice Modal ── --}}
<div class="invoice-modal-backdrop" id="invoiceBackdrop" onclick="closeInvoiceModal(event)">
    <div class="invoice-modal" id="invoiceModal">
        <div class="invoice-modal-header">
            <h5 id="invoiceModalTitle">Chi tiết hóa đơn</h5>
            <button class="invoice-modal-close" onclick="closeInvoiceModal(null, true)">✕</button>
        </div>
        <div class="invoice-modal-body" id="invoiceModalBody">
            <div class="inv-loading">
                <div class="inv-spinner"></div>
                <span>Đang tải dữ liệu...</span>
            </div>
        </div>
        <div class="invoice-modal-footer">
            <button class="btn btn-sm fw-semibold px-4" onclick="closeInvoiceModal(null, true)"
                    style="background:var(--surface);border:1.5px solid var(--border);border-radius:8px;color:var(--text)">
                Đóng
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
// ── Data từ Laravel ────────────────────────────────────────────────
const labels     = @json($rows->pluck('label'));
const ticketData = @json($rows->pluck('ticketRevenue'));
const foodData   = @json($rows->pluck('foodRevenue'));
const totalData  = @json($rows->pluck('totalRevenue'));
const periods    = @json($rows->pluck('period')->toArray());

const summaryTicket = {{ $summary['ticketRevenue'] }};
const summaryFood   = {{ $summary['foodRevenue'] }};

// ── Màu sắc ─────────────────────────────────────────────────────
const C = {
    ticket : '#6366f1', ticketA : 'rgba(99,102,241,0.15)',
    food   : '#06b6d4', foodA   : 'rgba(6,182,212,0.12)',
    total  : '#10b981', totalA  : 'rgba(16,185,129,0.10)',
};

// ── Format số ───────────────────────────────────────────────────
const fmt = v => new Intl.NumberFormat('vi-VN').format(v) + ' đ';

const tooltipPlugin = {
    callbacks: {
        label     : ctx => ' ' + fmt(ctx.parsed.y ?? ctx.parsed),
        afterLabel: ()  => '  ← Nhấn để xem hóa đơn',
    }
};
const axisDefaults = {
    grid  : { color: 'rgba(0,0,0,0.04)' },
    ticks : {
        color: '#64748b',
        callback: v => new Intl.NumberFormat('vi-VN', { notation:'compact' }).format(v) + ' đ'
    }
};

// ── Click handler ────────────────────────────────────────────────
function makeClickHandler(chart) {
    return function(evt) {
        const pts = chart.getElementsAtEventForMode(evt, 'index', { intersect:false }, true);
        if (!pts.length) return;
        const idx = pts[0].index;
        openInvoiceModal(periods[idx] ?? labels[idx], labels[idx]);
    };
}

// ── Bar chart ────────────────────────────────────────────────────
const barChart = new Chart(document.getElementById('barChart'), {
    type : 'bar',
    data : {
        labels,
        datasets: [
            { label:'Doanh thu vé',    data:ticketData, backgroundColor:'rgba(99,102,241,0.82)',  borderRadius:6, borderSkipped:false },
            { label:'Doanh thu đồ ăn', data:foodData,   backgroundColor:'rgba(6,182,212,0.82)',   borderRadius:6, borderSkipped:false },
            { label:'Tổng doanh thu',  data:totalData,  backgroundColor:'rgba(16,185,129,0.82)',  borderRadius:6, borderSkipped:false },
        ]
    },
    options: {
        responsive: true,
        plugins: { legend:{ display:false }, tooltip: tooltipPlugin },
        scales : { y: axisDefaults, x:{ grid:{ display:false }, ticks:{ color:'#64748b' } } }
    }
});
document.getElementById('barChart').addEventListener('click', makeClickHandler(barChart));

// ── Line chart ───────────────────────────────────────────────────
const lineChart = new Chart(document.getElementById('lineChart'), {
    type : 'line',
    data : {
        labels,
        datasets: [
            { label:'Doanh thu vé',    data:ticketData, borderColor:C.ticket, backgroundColor:C.ticketA, tension:.4, fill:true, pointBackgroundColor:C.ticket, pointRadius:5, pointHoverRadius:8 },
            { label:'Doanh thu đồ ăn', data:foodData,   borderColor:C.food,   backgroundColor:C.foodA,   tension:.4, fill:true, pointBackgroundColor:C.food,   pointRadius:5, pointHoverRadius:8 },
            { label:'Tổng doanh thu',  data:totalData,  borderColor:C.total,  backgroundColor:C.totalA,  tension:.4, fill:true, pointBackgroundColor:C.total,  pointRadius:5, pointHoverRadius:8, borderWidth:2.5 },
        ]
    },
    options: {
        responsive: true,
        plugins: { legend:{ display:false }, tooltip: tooltipPlugin },
        scales : { y: axisDefaults, x:{ grid:{ display:false }, ticks:{ color:'#64748b' } } }
    }
});
document.getElementById('lineChart').addEventListener('click', makeClickHandler(lineChart));

// ── Pie chart ────────────────────────────────────────────────────
new Chart(document.getElementById('pieChart'), {
    type : 'doughnut',
    data : {
        labels: ['Doanh thu vé','Doanh thu đồ ăn'],
        datasets:[{ data:[summaryTicket, summaryFood], backgroundColor:[C.ticket,C.food], borderWidth:3, hoverOffset:14 }]
    },
    options: {
        responsive:true, cutout:'64%',
        plugins:{ legend:{ display:false }, tooltip:{ callbacks:{ label: ctx => ' ' + fmt(ctx.parsed) } } }
    }
});

// ── Tab switcher ─────────────────────────────────────────────────
function switchChart(type, btn) {
    document.querySelectorAll('.chart-panel')   .forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.chart-tab-btn') .forEach(b => b.classList.remove('active'));
    document.getElementById('panel-' + type).classList.add('active');
    btn.classList.add('active');
    document.getElementById('chart-legend').style.display = type === 'pie' ? 'none' : '';
}

// ── Invoice Modal ─────────────────────────────────────────────────
function openInvoiceModal(period, label) {
    const backdrop = document.getElementById('invoiceBackdrop');
    const body     = document.getElementById('invoiceModalBody');
    document.getElementById('invoiceModalTitle').textContent = `Hóa đơn: ${label}`;
    body.innerHTML = `<div class="inv-loading"><div class="inv-spinner"></div><span>Đang tải dữ liệu...</span></div>`;
    backdrop.classList.add('open');

    // Route: /admins/reports/invoices-by-period  (prefix 'admins' + path 'reports/invoices-by-period')
    fetch(`/admins/reports/invoices-by-period?period=${encodeURIComponent(period)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
    .then(data => renderInvoices(body, data))
    .catch(err => {
        body.innerHTML = `
            <div class="text-center py-5">
                <div style="font-size:2.4rem">⚠️</div>
                <div class="mt-2 fw-semibold" style="color:#ef4444">Không thể tải dữ liệu hóa đơn.</div>
                <small style="color:#94a3b8">${err.message}</small>
            </div>`;
    });
}

function renderInvoices(container, data) {
    const invoices = data.invoices ?? [];

    // ── Tổng hợp ──
    let html = `
        <div class="invoice-stat-row">
            <div class="invoice-stat">
                <div class="s-label">🎟️ Doanh thu vé</div>
                <div class="s-val" style="color:#6366f1">${fmt(data.ticketRevenue ?? 0)}</div>
            </div>
            <div class="invoice-stat food">
                <div class="s-label">🍿 Doanh thu đồ ăn</div>
                <div class="s-val" style="color:#06b6d4">${fmt(data.foodRevenue ?? 0)}</div>
            </div>
            <div class="invoice-stat total">
                <div class="s-label">💰 Tổng doanh thu</div>
                <div class="s-val" style="color:#10b981">${fmt(data.totalRevenue ?? 0)}</div>
            </div>
        </div>`;

    // ── Top / Bottom phim ──
    const hasMovieData = data.topMovie || data.bottomMovie;
    if (hasMovieData) {
        html += `
        <div class="highlight-section">
            <div class="hs-title">🎬 Phim theo doanh thu vé</div>
            <div class="d-flex gap-2 flex-wrap">`;

        if (data.topMovie) {
            html += `
                <div class="highlight-card" style="background:#eef2ff;border-left:4px solid #6366f1">
                    <span class="hc-badge" style="color:#6366f1">🏆 DOANH THU CAO NHẤT</span>
                    <div class="hc-name">${data.topMovie.movieName}</div>
                    <div class="hc-rev" style="color:#6366f1">${fmt(data.topMovie.revenue)}</div>
                    <div class="hc-sub">${data.topMovie.count} vé đã bán</div>
                </div>`;
        }
        if (data.bottomMovie) {
            html += `
                <div class="highlight-card" style="background:#fff7ed;border-left:4px solid #f59e0b">
                    <span class="hc-badge" style="color:#f59e0b">📉 DOANH THU THẤP NHẤT</span>
                    <div class="hc-name">${data.bottomMovie.movieName}</div>
                    <div class="hc-rev" style="color:#f59e0b">${fmt(data.bottomMovie.revenue)}</div>
                    <div class="hc-sub">${data.bottomMovie.count} vé đã bán</div>
                </div>`;
        }

        html += `</div></div>`;
    }

    // ── Top / Bottom đồ ăn ──
    const hasFoodData = data.topFood || data.bottomFood;
    if (hasFoodData) {
        html += `
        <div class="highlight-section">
            <div class="hs-title">🍿 Món ăn theo doanh thu</div>
            <div class="d-flex gap-2 flex-wrap">`;

        if (data.topFood) {
            html += `
                <div class="highlight-card" style="background:#ecfeff;border-left:4px solid #06b6d4">
                    <span class="hc-badge" style="color:#06b6d4">🏆 DOANH THU CAO NHẤT</span>
                    <div class="hc-name">${data.topFood.foodName}</div>
                    <div class="hc-rev" style="color:#06b6d4">${fmt(data.topFood.revenue)}</div>
                    <div class="hc-sub">${data.topFood.quantity} phần đã bán</div>
                </div>`;
        }
        if (data.bottomFood) {
            html += `
                <div class="highlight-card" style="background:#fdf4ff;border-left:4px solid #a855f7">
                    <span class="hc-badge" style="color:#a855f7">📉 DOANH THU THẤP NHẤT</span>
                    <div class="hc-name">${data.bottomFood.foodName}</div>
                    <div class="hc-rev" style="color:#a855f7">${fmt(data.bottomFood.revenue)}</div>
                    <div class="hc-sub">${data.bottomFood.quantity} phần đã bán</div>
                </div>`;
        }

        html += `</div></div>`;
    }

    // ── Bảng hóa đơn ──
    if (!invoices.length) {
        html += `
            <div class="text-center py-5" style="color:#94a3b8">
                <div style="font-size:2rem">📭</div>
                <div class="mt-2">Không có hóa đơn nào trong kỳ này.</div>
            </div>`;
    } else {
        html += `
        <div style="font-size:.8rem;font-weight:600;color:var(--muted);text-transform:uppercase;
                    letter-spacing:.04em;margin-bottom:8px">
            📋 Danh sách hóa đơn (${invoices.length})
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 modal-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Mã HĐ</th>
                        <th>Khách hàng</th>
                        <th>Thanh toán</th>
                        <th>Ngày</th>
                        <th>Tổng tiền</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    ${invoices.map((inv, i) => {
                        const isFood   = inv.type === 'food';
                        const idColor  = isFood ? '#06b6d4' : '#6366f1';
                        const idLabel  = isFood ? inv.invoiceID : `INV-${inv.invoiceID}`;
                        const detailHref = isFood
                            ? '#'   // food invoice không có trang detail riêng trong ví dụ này
                            : `/admins/invoices/${inv.invoiceID}`;
                        return `
                        <tr>
                            <td style="color:#94a3b8">${i + 1}</td>
                            <td class="fw-semibold text-nowrap" style="color:${idColor}">
                                ${idLabel}
                                ${isFood ? '<span class="badge ms-1" style="background:#ecfeff;color:#06b6d4;border:1px solid #a5f3fc;font-size:.65rem;border-radius:4px">Đồ ăn</span>' : ''}
                            </td>
                            <td style="color:#1e293b">${inv.customer ?? 'Khách vãng lai'}</td>
                            <td>
                                <span class="badge fw-semibold"
                                      style="background:#ecfdf5;color:#10b981;border:1px solid #a7f3d0;border-radius:6px;padding:4px 10px">
                                    ${inv.paymentMethod ?? '---'}
                                </span>
                            </td>
                            <td class="text-nowrap" style="color:#64748b">${inv.createDate ?? ''}</td>
                            <td class="fw-bold text-nowrap" style="color:#ef4444">${fmt(inv.totalAmount ?? 0)}</td>
                            <td>
                                ${!isFood ? `<a href="${detailHref}" class="btn btn-sm fw-semibold" target="_blank"
                                   style="background:#eef2ff;color:#6366f1;border:1px solid #c7d2fe;border-radius:7px">
                                    Chi tiết
                                </a>` : '<span style="color:#94a3b8;font-size:.8rem">—</span>'}
                            </td>
                        </tr>`;
                    }).join('')}
                </tbody>
            </table>
        </div>`;
    }

    container.innerHTML = html;
}

function closeInvoiceModal(evt, force = false) {
    if (!force && evt && document.getElementById('invoiceModal').contains(evt.target)) return;
    document.getElementById('invoiceBackdrop').classList.remove('open');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeInvoiceModal(null, true); });

// ── Pagination ───────────────────────────────────────────────────
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
        controls.appendChild(btn('‹', curPage <= 1, () => { curPage--; render(); }));

        pageRange(curPage, pages).forEach(p => {
            if (p === '…') {
                const dot = document.createElement('span');
                dot.textContent = '…';
                dot.style.cssText = 'padding:0 6px;color:#94a3b8;font-size:.85rem';
                controls.appendChild(dot);
            } else {
                const b = btn(p, false, () => { curPage = p; render(); });
                if (p === curPage) b.classList.add('active');
                controls.appendChild(b);
            }
        });

        controls.appendChild(btn('›', curPage >= pages, () => { curPage++; render(); }));
    }

    function btn(label, disabled, onClick) {
        const b = document.createElement('button');
        b.className = 'page-btn'; b.textContent = label; b.disabled = disabled;
        b.addEventListener('click', onClick);
        return b;
    }

    function pageRange(cur, total) {
        if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);
        if (cur <= 4)          return [1, 2, 3, 4, 5, '…', total];
        if (cur >= total - 3)  return [1, '…', total-4, total-3, total-2, total-1, total];
        return [1, '…', cur-1, cur, cur+1, '…', total];
    }

    window.changePageSize = val => { pageSize = parseInt(val); curPage = 1; render(); };
    render();
})();
</script>
@endsection