@extends('layouts.appAdmin')

@section('content')
<style>
    :root {
        --bg-deep:   #f0f4f8;
        --bg-card:   #ffffff;
        --bg-panel:  #f8fafc;
        --border:    #e2e8f0;
        --muted:     #94a3b8;
        --accent:    #3b82f6;
        --text-main: #1e293b;
        --text-sub:  #64748b;
    }

    body { background: var(--bg-deep); color: var(--text-main); }

    .panel-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 20px 24px;
        margin-bottom: 16px;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
    }

    .panel-title {
        font-size: 11px;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--muted);
        margin-bottom: 10px;
        font-weight: 700;
    }

    .ctrl {
        background: var(--bg-panel);
        border: 1px solid var(--border);
        color: var(--text-main);
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14px;
        width: 100%;
        transition: border-color .2s;
    }

    .ctrl:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(59,130,246,.12);
        background: #fff;
    }

    .ctrl option { background: #fff; }

    .form-label {
        font-size: 12px;
        color: var(--text-sub);
        margin-bottom: 6px;
        font-weight: 600;
        display: block;
    }

    .btn-c {
        border-radius: 10px;
        font-weight: 700;
        font-size: 13px;
        padding: 9px 18px;
        cursor: pointer;
        border: none;
        transition: all .15s;
        white-space: nowrap;
    }

    .btn-c:hover:not(:disabled) { transform: translateY(-1px); }

    .btn-c:disabled {
        opacity: .4;
        cursor: not-allowed;
        transform: none !important;
    }

    .bp { background: var(--accent); color: #fff; }
    .bp:hover:not(:disabled) { background: #2563eb; }

    .bg { background: var(--bg-panel); color: var(--text-sub); border: 1px solid var(--border); }
    .bg:hover:not(:disabled) { background: #e2e8f0; color: var(--text-main); }

    .bs { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
    .bs:hover:not(:disabled) { background: #bbf7d0; }

    .bw { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
    .bw:hover:not(:disabled) { background: #fde68a; }

    .bm { background: #e0e7ff; color: #3730a3; border: 1px solid #a5b4fc; }
    .bm:hover:not(:disabled) { background: #c7d2fe; }

    .swap-info {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 14px 18px;
        display: none;
        gap: 16px;
        align-items: center;
        flex-wrap: wrap;
    }
    .swap-info.show { display: flex; }

    .batch-panel {
        background: #eef2ff;
        border: 1px solid #c7d2fe;
        border-radius: 12px;
        padding: 16px 18px;
        display: none;
        flex-direction: column;
        gap: 14px;
        margin-top: 10px;
    }
    .batch-panel.show { display: flex; }

    .batch-top {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .batch-validation {
        font-size: 12px;
        font-weight: 600;
        padding: 8px 14px;
        border-radius: 8px;
        display: none;
    }
    .batch-validation.ok  { background: #f0fdf4; border: 1px solid #86efac; color: #166534; display: block; }
    .batch-validation.err { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; display: block; }

    .seat-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--bg-panel);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 6px 12px;
        font-weight: 700;
        font-size: 14px;
        color: var(--text-main);
    }

    .mode-hint {
        border-radius: 10px;
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 600;
        display: none;
        margin-top: 10px;
    }
    .mode-hint.show { display: block; }
    .hint-swap { background: #f0fdf4; border: 1px dashed #86efac; color: #166534; }

    .add-grid {
        display: grid;
        grid-template-columns: 80px 100px 1fr auto;
        gap: 10px;
        align-items: end;
    }
    @media(max-width:640px) { .add-grid { grid-template-columns: 1fr 1fr; } }

    .stat-pills { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px; }

    .stat-pill {
        background: var(--bg-panel);
        border: 1px solid var(--border);
        border-radius: 999px;
        padding: 5px 14px;
        font-size: 12px;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .stat-pill .dot { width: 8px; height: 8px; border-radius: 50%; }

    .screen-wrap { text-align: center; margin-bottom: 16px; }

    .screen-bar {
        width: 50%;
        height: 5px;
        margin: 0 auto 6px;
        background: linear-gradient(to right, transparent, #f59e0b, transparent);
        border-radius: 999px;
        box-shadow: 0 0 14px rgba(245,158,11,.35);
    }

    .screen-label {
        font-size: 10px;
        letter-spacing: .18em;
        color: #b45309;
        text-transform: uppercase;
        font-weight: 700;
    }

    .seat-grid {
        display: flex;
        flex-direction: column;
        gap: 5px;
        align-items: center;
        overflow-x: auto;
        padding: 14px 8px 24px;
        user-select: none;
    }

    .seat-row { display: flex; gap: 5px; align-items: center; }

    .row-label {
        width: 20px;
        text-align: center;
        font-size: 11px;
        color: var(--muted);
        font-weight: 700;
        flex-shrink: 0;
    }

    .seat, .empty-slot, .disabled-slot {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 8px;
        font-weight: 800;
        user-select: none;
        flex-shrink: 0;
        position: relative;
        transition: transform .1s, box-shadow .1s, opacity .15s;
    }

    .seat { cursor: pointer; border: 2px solid transparent; }
    .seat:hover { transform: translateY(-2px) scale(1.07); z-index: 10; }

    .seat.t-normal { background: #dbeafe; color: #1d4ed8; border-color: #93c5fd; }
    .seat.t-vip    { background: #fef3c7; color: #92400e; border-color: #fcd34d; }
    .seat.t-couple { background: #fce7f3; color: #9d174d; border-color: #f9a8d4; }
    .seat.t-maint  { background: #f1f5f9; color: #94a3b8; border-color: #cbd5e1; }

    .seat.is-source {
        outline: 3px solid #3b82f6;
        box-shadow: 0 0 0 5px rgba(59,130,246,.2);
        z-index: 30;
        transform: scale(1.1);
    }
    .seat.is-swap-tgt {
        outline: 3px solid #22c55e;
        box-shadow: 0 0 0 5px rgba(34,197,94,.2);
        animation: pg .8s infinite alternate;
        z-index: 25;
    }
    .seat.is-dimmed { opacity: .2; pointer-events: none; }

    .seat.is-multi {
        outline: 3px solid #6366f1;
        box-shadow: 0 0 0 4px rgba(99,102,241,.18);
        z-index: 20;
    }
    .seat.is-multi::after {
        content: '✓';
        position: absolute;
        top: -6px; right: -6px;
        width: 13px; height: 13px;
        background: #6366f1;
        border-radius: 50%;
        font-size: 8px;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #fff;
        border: 1px solid #a5b4fc;
    }

    @keyframes pg {
        from { box-shadow: 0 0 0 3px rgba(34,197,94,.15) }
        to   { box-shadow: 0 0 0 8px rgba(34,197,94,.3) }
    }

    .empty-slot {
        background: #f1f5f9;
        border: 1px dashed #cbd5e1;
        color: #cbd5e1;
        cursor: crosshair;
        font-size: 14px;
        font-weight: 300;
    }
    .empty-slot:hover {
        border-color: #22c55e;
        color: #16a34a;
        background: #f0fdf4;
        transform: scale(1.05);
    }

    .disabled-slot {
        background: #f8fafc;
        border: 1px dashed #e2e8f0;
        color: #e2e8f0;
        cursor: pointer;
        font-size: 12px;
        opacity: .5;
        border-radius: 8px;
    }
    .disabled-slot:hover {
        border-color: #93c5fd;
        color: #3b82f6;
        background: #eff6ff;
        opacity: 1;
        transform: scale(1.03);
    }

    .del-btn {
        position: absolute;
        top: -5px; right: -5px;
        width: 15px; height: 15px;
        border-radius: 50%;
        background: #dc2626;
        color: #fff;
        font-size: 9px;
        display: none;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        z-index: 50;
        border: 1px solid #fca5a5;
    }
    .seat:hover .del-btn { display: flex; }

    .legend {
        display: flex;
        justify-content: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-top: 16px;
        padding-bottom: 24px;
    }
    .legend-item { display: flex; align-items: center; gap: 7px; font-size: 11px; color: var(--text-sub); font-weight: 600; }
    .legend-box  { width: 18px; height: 18px; border-radius: 5px; }

    .grid-spinner { display: none; text-align: center; padding: 40px 0; color: var(--muted); }
    .grid-spinner.show { display: block; }

    .seat-grid.dragging { cursor: crosshair; }

    #toast {
        position: fixed;
        bottom: 24px; right: 24px;
        min-width: 260px;
        padding: 14px 20px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        z-index: 9999;
        opacity: 0;
        pointer-events: none;
        transform: translateY(10px);
        transition: all .3s;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    #toast.show    { opacity: 1; transform: translateY(0); }
    #toast.success { background: #f0fdf4; border: 1px solid #86efac; color: #166534; box-shadow: 0 4px 16px rgba(34,197,94,.15); }
    #toast.error   { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; box-shadow: 0 4px 16px rgba(239,68,68,.15); }
    #toast.info    { background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; box-shadow: 0 4px 16px rgba(59,130,246,.15); }
    #toast.warning { background: #fffbeb; border: 1px solid #fcd34d; color: #92400e; box-shadow: 0 4px 16px rgba(245,158,11,.15); }
</style>

{{-- PANEL 1: PHÒNG + STATS --}}
<div class="panel-card">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <div class="panel-title">Phòng chiếu</div>
            <form method="GET">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <select name="roomID" class="ctrl" onchange="this.form.submit()">
                    @forelse($rooms as $r)
                        <option value="{{ $r->roomID }}"
                            {{ (int)($roomID ?? 0) === (int)$r->roomID ? 'selected' : '' }}>
                            {{ $r->roomName }}
                        </option>
                    @empty
                        <option value="">Chưa có phòng</option>
                    @endforelse
                </select>
            </form>
        </div>
        <div class="col-md-8">
            <div class="panel-title">Thống kê</div>
            <div class="stat-pills">
                <span class="stat-pill"><span class="dot" style="background:#94a3b8"></span>Tổng: <b id="st-total">0</b></span>
                <span class="stat-pill"><span class="dot" style="background:#3b82f6"></span>Thường: <b id="st-normal">0</b></span>
                <span class="stat-pill"><span class="dot" style="background:#f59e0b"></span>VIP: <b id="st-vip">0</b></span>
                <span class="stat-pill"><span class="dot" style="background:#ec4899"></span>Đôi: <b id="st-couple">0</b></span>
                <span class="stat-pill"><span class="dot" style="background:#94a3b8"></span>Bảo trì: <b id="st-maint">0</b></span>
                <span class="stat-pill"><span class="dot" style="background:#cbd5e1"></span>Hàng: <b id="st-rows">0</b> | Cột: <b id="st-cols">0</b></span>
            </div>
        </div>
    </div>
</div>

{{-- PANEL 2: THAO TÁC GHẾ --}}
<div class="panel-card">
    <div class="panel-title">Thao tác ghế</div>

    <p id="swapIdle" style="color:var(--muted);font-size:13px;margin:0;">
        ← Bấm vào một ghế để thao tác đơn lẻ. Nhấn giữ và kéo chuột trên lưới để chọn nhiều ghế cùng lúc.
    </p>

    {{-- Panel thao tác GHẾ ĐƠN --}}
    <div class="swap-info" id="swapPanel">
        <div>
            <div class="form-label" style="margin-bottom:4px;">Ghế đang chọn</div>
            <div class="seat-badge" id="swapBadge">—</div>
        </div>
        <span style="color:var(--muted);font-size:18px;">⇄</span>
        <div style="flex:1;min-width:150px;">
            <div class="form-label" style="margin-bottom:4px;">Đổi sang loại</div>
            <select id="swapTargetType" class="ctrl">
                @foreach($seatTypes as $type)
                    @if(strtolower($type->seatTypeName) !== 'bảo trì')
                        <option value="{{ $type->seatTypeID }}">{{ $type->seatTypeName }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn-c bs" id="btnSwap"  onclick="startSwapPicking()">⇄ Hoán đổi</button>
            <button class="btn-c bw" id="btnMaint" onclick="toggleMaintenance()">⚙ Bảo trì</button>
            <button class="btn-c bg"               onclick="resetMode()">✕ Huỷ</button>
        </div>
    </div>

    <div class="mode-hint hint-swap" id="hintSwap"></div>

    {{-- Panel HOÁN ĐỔI NHIỀU GHẾ --}}
    <div class="batch-panel" id="batchPanel">
        <div class="batch-top">
            <div>
                <div class="form-label" style="margin-bottom:4px;">Ghế đã chọn</div>
                <div class="seat-badge">
                    <span style="color:#6366f1">⬡</span>
                    <span id="batchCount">0</span> ghế
                    <span id="batchSourceType" style="color:var(--muted);font-size:11px;font-weight:600"></span>
                </div>
            </div>

            <span style="color:var(--muted);font-size:20px;font-weight:300;">→</span>

            <div style="flex:1;min-width:160px;">
                <div class="form-label" style="margin-bottom:4px;">Đổi tất cả sang loại</div>
                <select id="batchTargetType" class="ctrl" onchange="validateBatch()">
                    @foreach($seatTypes as $type)
                        <option value="{{ $type->seatTypeID }}">{{ $type->seatTypeName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex gap-2 flex-wrap align-items-end">
                <button class="btn-c bm" id="btnApplyBatch" onclick="applyBatch()">✦ Áp dụng</button>
                <button class="btn-c bg" onclick="resetMode()">✕ Huỷ chọn</button>
            </div>
        </div>

        <div class="batch-validation" id="batchValidation"></div>
    </div>
</div>

{{-- PANEL 3: THÊM GHẾ --}}
<div class="panel-card">
    <div class="panel-title">Thêm ghế mới</div>
    <div class="add-grid">
        <div>
            <label class="form-label">Hàng (A–Z)</label>
            <input id="addRow" type="text" maxlength="1" placeholder="A"
                   class="ctrl" style="text-transform:uppercase">
        </div>
        <div>
            <label class="form-label">Cột</label>
            <input id="addCol" type="number" min="1" max="99" placeholder="1" class="ctrl">
        </div>
        <div>
            <label class="form-label">Loại ghế</label>
            <select id="addType" class="ctrl">
                @foreach($seatTypes as $type)
                    <option value="{{ $type->seatTypeID }}">{{ $type->seatTypeName }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn-c bp" onclick="addSeat()" id="btnAdd">+ Thêm ghế</button>
    </div>
    <div id="capWarn" style="display:none;color:#d97706;font-size:12px;margin-top:6px;font-weight:600;">
        ⚠ Số ghế hiện tại đã bằng sức chứa phòng.
    </div>
</div>

{{-- GRID --}}
<div class="screen-wrap">
    <div class="screen-bar"></div>
    <div class="screen-label">Màn hình</div>
</div>

<div class="grid-spinner" id="gridSpinner">
    <div class="spinner-border spinner-border-sm text-secondary me-2"></div>
    Đang tải sơ đồ ghế...
</div>

<div id="seatGrid" class="seat-grid"></div>

<div class="legend">
    <div class="legend-item">
        <div class="legend-box" style="background:#dbeafe;border:2px solid #93c5fd"></div>Ghế thường
    </div>
    <div class="legend-item">
        <div class="legend-box" style="background:#fef3c7;border:2px solid #fcd34d"></div>Ghế VIP
    </div>
    <div class="legend-item">
        <div class="legend-box" style="background:#fce7f3;border:2px solid #f9a8d4"></div>Ghế đôi
    </div>
    <div class="legend-item">
        <div class="legend-box" style="background:#f1f5f9;border:2px solid #cbd5e1"></div>Bảo trì
    </div>
    <div class="legend-item">
        <div class="legend-box" style="background:#f1f5f9;border:1px dashed #cbd5e1"></div>Ô trống
    </div>
    <div class="legend-item">
        <div class="legend-box" style="outline:3px solid #6366f1;border-radius:5px;background:#dbeafe"></div>Chọn nhiều
    </div>
</div>

<div id="toast"><span id="t-ico"></span><span id="t-msg"></span></div>

<script>
'use strict';

const ROOM_ID   = parseInt("{{ (int)($roomID ?? 0) }}", 10);
const CAPACITY  = parseInt("{{ $room?->capacity ?? 0 }}", 10);
const CSRF      = "{{ csrf_token() }}";

/*
 * ROOM_ROWS / ROOM_COLS: số hàng và cột tối đa của phòng, tính từ seats trong DB.
 * Controller tính và truyền xuống — không cần cột mới trong DB.
 * renderGrid() dùng để hiển thị đủ lưới kể cả ô trống hoàn toàn.
 */
const ROOM_ROWS = parseInt("{{ (int)($roomActualRows ?? 0) }}", 10);
const ROOM_COLS = parseInt("{{ (int)($roomActualCols ?? 0) }}", 10);

const SPECIAL_TYPE_IDS = {{ Js::from($specialTypeIds ?? ['vip'=>1,'normal'=>2,'couple'=>3,'maintenance'=>4]) }};
const SEAT_TYPE_NAMES  = {{ Js::from($seatTypeNames ?? []) }};

const VIP_TYPE_ID         = Number(SPECIAL_TYPE_IDS.vip);
const NORMAL_TYPE_ID      = Number(SPECIAL_TYPE_IDS.normal);
const COUPLE_TYPE_ID      = Number(SPECIAL_TYPE_IDS.couple);
const MAINTENANCE_TYPE_ID = Number(SPECIAL_TYPE_IDS.maintenance);

const TYPE_CLASS = {
    [VIP_TYPE_ID]:         't-vip',
    [NORMAL_TYPE_ID]:      't-normal',
    [COUPLE_TYPE_ID]:      't-couple',
    [MAINTENANCE_TYPE_ID]: 't-maint',
};
const TYPE_NAME = {
    [VIP_TYPE_ID]:         'VIP',
    [NORMAL_TYPE_ID]:      'Thường',
    [COUPLE_TYPE_ID]:      'Đôi',
    [MAINTENANCE_TYPE_ID]: 'Bảo trì',
};
const TYPE_COLOR = {
    [VIP_TYPE_ID]:         '#d97706',
    [NORMAL_TYPE_ID]:      '#2563eb',
    [COUPLE_TYPE_ID]:      '#db2777',
    [MAINTENANCE_TYPE_ID]: '#94a3b8',
};

let seatsData     = [];
let appMode       = 'idle';
let sourceSeat    = null;
let selectedMulti = new Set();

let drag = { active:false, pending:false, startX:0, startY:0 };
let dragJustFinished = false;
const DRAG_THRESHOLD = 6;

// ══════════════════════════════════════════════════════════
// LOAD & RENDER
// ══════════════════════════════════════════════════════════
function loadSeats(silent = false) {
    if (!ROOM_ID) {
        document.getElementById('seatGrid').innerHTML =
            '<p style="color:var(--muted);padding:40px 0;text-align:center">Chưa có phòng nào.</p>';
        return;
    }
    if (!silent) showSpinner(true);
    fetch(`/admins/seat/ajax/${ROOM_ID}`)
        .then(r => { if (!r.ok) throw new Error(`Lỗi ${r.status}`); return r.json(); })
        .then(data => {
            // API có thể trả array thuần hoặc object {seats, roomRows, roomCols}
            seatsData = Array.isArray(data) ? data : (Array.isArray(data.seats) ? data.seats : []);
            renderGrid();
            updateStats();
        })
        .catch(err => showToast(err.message, 'error'))
        .finally(() => showSpinner(false));
}

function updateStats() {
    const rows   = [...new Set(seatsData.map(s => s.rowSeat))];
    const maxCol = seatsData.reduce((m, s) => Math.max(m, Number(s.colSeat)), 0);
    const counts = {};
    seatsData.forEach(s => counts[s.seatTypeID] = (counts[s.seatTypeID] || 0) + 1);

    set('st-total',  seatsData.length);
    set('st-normal', counts[NORMAL_TYPE_ID] || 0);
    set('st-vip',    counts[VIP_TYPE_ID] || 0);
    set('st-couple', counts[COUPLE_TYPE_ID] || 0);
    set('st-maint',  counts[MAINTENANCE_TYPE_ID] || 0);
    set('st-rows',   rows.length);
    set('st-cols',   maxCol);

    document.getElementById('capWarn').style.display =
        seatsData.length >= CAPACITY ? 'block' : 'none';
}

function renderGrid() {
    const grid    = document.getElementById('seatGrid');
    const seatMap = {};
    seatsData.forEach(s => { seatMap[`${s.rowSeat}-${s.colSeat}`] = s; });

    /*
     * totalRows / totalCols: ưu tiên ROOM_ROWS/ROOM_COLS (inject từ PHP/SeatController).
     * Fallback về seatsData nếu biến PHP = 0 (phòng cũ).
     * Đảm bảo render đủ hàng/cột kể cả hàng chưa có ghế (toàn empty-slot).
     */
    const dataRows   = [...new Set(seatsData.map(s => s.rowSeat))].sort();
    const dataMaxCol = seatsData.reduce((m, s) => Math.max(m, +s.colSeat), 0);
    const totalRows  = Math.max(ROOM_ROWS || 0, dataRows.length, 1);
    const totalCols  = Math.max(ROOM_COLS || 0, dataMaxCol, 1);

    // Tạo mảng nhãn hàng A, B, C... theo totalRows
    const rowLabels = Array.from({ length: totalRows }, (_, i) => String.fromCharCode(65 + i));

    let html = '';
    rowLabels.forEach(row => {
        html += `<div class="seat-row"><span class="row-label">${row}</span>`;
        for (let col = 1; col <= totalCols; col++) {
            const seat = seatMap[`${row}-${col}`];
            if (!seat) {
                // Ô trống: có thể click để điền vào form thêm ghế
                html += `<div class="empty-slot" data-row="${row}" data-col="${col}">+</div>`;
                continue;
            }
            const cls          = TYPE_CLASS[seat.seatTypeID] || 't-normal';
            const originalType = (seat.originalSeatTypeID != null)
                ? seat.originalSeatTypeID
                : seat.seatTypeID;

            html += `<div class="seat ${cls}"
                          data-id="${seat.seatID}"
                          data-row="${row}"
                          data-col="${col}"
                          data-type="${seat.seatTypeID}"
                          data-original-type="${originalType}"
                          data-name="${row}${col}">
                        ${row}${col}
                        <span class="del-btn" onmousedown="event.stopPropagation()" onclick="deleteSeat(${seat.seatID},event)">×</span>
                     </div>`;
        }
        html += `</div>`;
    });

    /*
     * Hàng nextRow (disabled-slot gợi ý thêm hàng mới):
     * Chỉ hiện khi hàng cuối ĐÃ ĐẦY hoàn toàn VÀ chưa đến Z.
     * Nếu hàng cuối chưa đầy → có empty-slot để dùng, không cần thêm hàng.
     */
    if (rowLabels.length > 0 && rowLabels.length < 26) {
        const lastRow    = rowLabels[rowLabels.length - 1];
        let lastRowFull  = true;
        for (let col = 1; col <= totalCols; col++) {
            if (!seatMap[`${lastRow}-${col}`]) { lastRowFull = false; break; }
        }
        if (lastRowFull) {
            const nextRow = String.fromCharCode(lastRow.charCodeAt(0) + 1);
            html += `<div class="seat-row"><span class="row-label" style="color:#cbd5e1">${nextRow}</span>`;
            for (let col = 1; col <= totalCols; col++) {
                html += `<div class="disabled-slot" data-row="${nextRow}" data-col="${col}"
                              onclick="quickFill('${nextRow}',${col})"
                              title="Thêm ghế ${nextRow}${col}">+</div>`;
            }
            html += `</div>`;
        }
    }

    grid.innerHTML = html;
    bindGridEvents();
    reApplyStyles();
}

function quickFill(row, col) {
    if (appMode === 'multi_select') return;
    document.getElementById('addRow').value = row;
    document.getElementById('addCol').value = col;
    showToast(`Điền ${row}${col} → form thêm ghế`, 'info');
    document.getElementById('addRow').scrollIntoView({ behavior:'smooth', block:'center' });
}

// ══════════════════════════════════════════════════════════
// GRID EVENTS
// ══════════════════════════════════════════════════════════
function bindGridEvents() {
    const grid = document.getElementById('seatGrid');

    document.querySelectorAll('.seat').forEach(el => {
        el.addEventListener('click', function () {
            if (dragJustFinished) { dragJustFinished = false; return; }
            if (appMode === 'multi_select') return;
            if (appMode === 'swap_picking' && this.classList.contains('is-swap-tgt')) {
                confirmSwap(this); return;
            }
            selectSource(this);
        });
    });

    document.querySelectorAll('.empty-slot, .disabled-slot').forEach(el => {
        el.addEventListener('click', function () {
            if (appMode === 'idle' || appMode === 'selected') {
                quickFill(this.dataset.row, this.dataset.col);
            }
        });
    });

    grid.addEventListener('mousedown', function (e) {
        if (e.button !== 0) return;
        if (appMode === 'swap_picking') return;
        drag = { active:false, pending:true, startX:e.clientX, startY:e.clientY };
        e.preventDefault();
    });
}

document.addEventListener('mousemove', function (e) {
    if (!drag.pending && !drag.active) return;
    const dx = Math.abs(e.clientX - drag.startX), dy = Math.abs(e.clientY - drag.startY);
    if (!drag.active && (dx > DRAG_THRESHOLD || dy > DRAG_THRESHOLD)) {
        drag.active = true; drag.pending = false;
        if (appMode === 'selected') resetMode();
        document.getElementById('seatGrid').classList.add('dragging');
    }
    if (!drag.active) return;
    const el   = document.elementFromPoint(e.clientX, e.clientY);
    const seat = el ? el.closest('.seat') : null;
    if (seat && !selectedMulti.has(seat.dataset.id)) {
        selectedMulti.add(seat.dataset.id);
        seat.classList.add('is-multi');
    }
});

document.addEventListener('mouseup', function () {
    if (!drag.pending && !drag.active) return;
    const wasDragging = drag.active;
    drag = { active:false, pending:false, startX:0, startY:0 };
    document.getElementById('seatGrid').classList.remove('dragging');
    if (!wasDragging) return;
    if (selectedMulti.size > 0) {
        dragJustFinished = true;
        appMode = 'multi_select';
        document.getElementById('swapIdle').style.display  = 'none';
        document.getElementById('swapPanel').classList.remove('show');
        document.getElementById('batchPanel').classList.add('show');
        set('batchCount', selectedMulti.size);
        updateBatchSourceLabel();
        validateBatch();
        showToast(`Đã chọn ${selectedMulti.size} ghế — chọn loại muốn đổi rồi nhấn Áp dụng`, 'info');
    }
});

// ══════════════════════════════════════════════════════════
// SELECT / STYLES
// ══════════════════════════════════════════════════════════
function selectSource(el) {
    sourceSeat = {
        id:           el.dataset.id,
        name:         el.dataset.name,
        typeID:       +el.dataset.type,
        /*
        |--------------------------------------------------------------
        | FIX BUG 2: Đọc originalType từ data attribute.
        | Dùng để phục hồi đúng loại ghế khi bỏ bảo trì.
        |--------------------------------------------------------------
        */
        originalType: +el.dataset.originalType,
        row:          el.dataset.row,
        col:          +el.dataset.col,
    };
    appMode = 'selected';

    document.getElementById('swapIdle').style.display = 'none';
    document.getElementById('swapPanel').classList.add('show');
    document.getElementById('batchPanel').classList.remove('show');
    document.getElementById('hintSwap').classList.remove('show');

    const displayType = TYPE_NAME[sourceSeat.typeID] || SEAT_TYPE_NAMES[sourceSeat.typeID] || '';
    const badge = `<span style="color:${TYPE_COLOR[sourceSeat.typeID]}">■</span> ${sourceSeat.name}
                   <span style="color:var(--muted);font-size:11px">(${displayType})</span>`;
    document.getElementById('swapBadge').innerHTML = badge;

    const btnMaint = document.getElementById('btnMaint');
    if (sourceSeat.typeID === MAINTENANCE_TYPE_ID) {
        /*
        |--------------------------------------------------------------
        | Ghế đang bảo trì → nút "Phục hồi", hiện loại gốc sẽ về
        |--------------------------------------------------------------
        */
        const origName = TYPE_NAME[sourceSeat.originalType]
            || SEAT_TYPE_NAMES[sourceSeat.originalType]
            || '';
        btnMaint.textContent = origName ? `✓ Phục hồi (${origName})` : '✓ Phục hồi';
        btnMaint.className   = 'btn-c bs';
    } else {
        btnMaint.textContent = '⚙ Bảo trì';
        btnMaint.className   = 'btn-c bw';
    }

    const sel = document.getElementById('swapTargetType');
    for (const opt of sel.options) {
        if (+opt.value !== sourceSeat.typeID) { opt.selected = true; break; }
    }

    reApplyStyles();
}

function clearStyles() {
    document.querySelectorAll('.seat').forEach(el =>
        el.classList.remove('is-source','is-swap-tgt','is-dimmed','is-multi'));
}

function reApplyStyles() {
    clearStyles();
    if (!sourceSeat && !selectedMulti.size) return;

    if (selectedMulti.size > 0) {
        document.querySelectorAll('.seat').forEach(el => {
            if (selectedMulti.has(el.dataset.id)) el.classList.add('is-multi');
        });
        return;
    }

    const src = String(sourceSeat.id);

    if (appMode === 'selected') {
        document.querySelectorAll('.seat').forEach(el => {
            if (el.dataset.id === src) el.classList.add('is-source');
        });
    }

    if (appMode === 'swap_picking') {
        const tid = getSwapTarget();
        document.querySelectorAll('.seat').forEach(el => {
            if (el.dataset.id === src)    { el.classList.add('is-source');   return; }
            if (+el.dataset.type === tid) { el.classList.add('is-swap-tgt'); return; }
            el.classList.add('is-dimmed');
        });
    }
}

// ══════════════════════════════════════════════════════════
// SWAP (đơn lẻ)
// ══════════════════════════════════════════════════════════
function startSwapPicking() {
    if (!sourceSeat) return;
    const tid = getSwapTarget();
    if (tid === sourceSeat.typeID) { showToast('Chọn loại khác với hiện tại', 'warning'); return; }

    const avail = seatsData.filter(s => Number(s.seatTypeID) === tid && String(s.seatID) !== sourceSeat.id);
    if (!avail.length) { showToast(`Không có ghế ${TYPE_NAME[tid] || ''} để hoán đổi`, 'warning'); return; }

    appMode = 'swap_picking';
    reApplyStyles();

    const hint = document.getElementById('hintSwap');
    hint.textContent = `⇄ Chọn 1 ghế ${TYPE_NAME[tid] || ''} (tô xanh) để hoán đổi với ${sourceSeat.name}. [Esc] huỷ.`;
    hint.classList.add('show');
}

function confirmSwap(el) {
    if (!confirm(`Hoán đổi:\n${sourceSeat.name}(${TYPE_NAME[sourceSeat.typeID]}) ⇄ ${el.dataset.name}(${TYPE_NAME[+el.dataset.type]})?`)) return;
    apiFetch('/admins/seat/ajax-swap-type', 'POST', {
        seatID_a: parseInt(sourceSeat.id), seatID_b: parseInt(el.dataset.id)
    }).then(res => {
        if (!res.success) { showToast(res.message, 'error'); return; }
        showToast('Hoán đổi thành công', 'success');
        resetMode(); loadSeats(true);
    }).catch(err => showToast(err.message, 'error'));
}

// ══════════════════════════════════════════════════════════
// MAINTENANCE
// ══════════════════════════════════════════════════════════
function toggleMaintenance() {
    if (!sourceSeat) return;

    const isMaint = sourceSeat.typeID === MAINTENANCE_TYPE_ID;

    if (isMaint) {
        /*
        |--------------------------------------------------------------
        | FIX BUG 2: PHỤC HỒI — dùng originalType thay vì NORMAL_TYPE_ID
        | originalType được đọc từ data-original-type (đã lưu trong DB
        | qua cột originalSeatTypeID).
        |--------------------------------------------------------------
        */
        const restoreTypeID = sourceSeat.originalType || NORMAL_TYPE_ID;
        const restoreName   = TYPE_NAME[restoreTypeID]
            || SEAT_TYPE_NAMES[restoreTypeID]
            || 'loại gốc';

        if (!confirm(`Phục hồi ghế ${sourceSeat.name} về loại: ${restoreName}?`)) return;

        apiFetch('/admins/seat/ajax-update-type', 'POST', {
            seatID:     parseInt(sourceSeat.id),
            seatTypeID: restoreTypeID,   // gửi type gốc để server set lại
        }).then(res => {
            if (!res.success) { showToast(res.message, 'error'); return; }
            showToast(`Ghế ${sourceSeat.name} → ${restoreName}`, 'success');
            resetMode(); loadSeats(true);
        }).catch(err => showToast(err.message, 'error'));

    } else {
        /*
        |--------------------------------------------------------------
        | CHUYỂN SANG BẢO TRÌ — server sẽ tự lưu originalSeatTypeID
        |--------------------------------------------------------------
        */
        if (!confirm(`Chuyển ghế ${sourceSeat.name} (${TYPE_NAME[sourceSeat.typeID] || ''}) sang bảo trì?`)) return;

        apiFetch('/admins/seat/ajax-update-type', 'POST', {
            seatID:     parseInt(sourceSeat.id),
            seatTypeID: MAINTENANCE_TYPE_ID,
        }).then(res => {
            if (!res.success) { showToast(res.message, 'error'); return; }
            showToast(`Ghế ${sourceSeat.name} → Bảo trì`, 'success');
            resetMode(); loadSeats(true);
        }).catch(err => showToast(err.message, 'error'));
    }
}

// ══════════════════════════════════════════════════════════
// BATCH SWAP (nhiều ghế)
// ══════════════════════════════════════════════════════════
function updateBatchSourceLabel() {
    const selectedSeats = seatsData.filter(s => selectedMulti.has(String(s.seatID)));
    const types = [...new Set(selectedSeats.map(s => Number(s.seatTypeID)))];
    const label = types.length === 1
        ? `(${TYPE_NAME[types[0]] || SEAT_TYPE_NAMES[types[0]] || ''})`
        : '(hỗn hợp)';
    document.getElementById('batchSourceType').textContent = label;
}

function validateBatch() {
    const el        = document.getElementById('batchValidation');
    const btnApply  = document.getElementById('btnApplyBatch');
    const targetID  = parseInt(document.getElementById('batchTargetType').value);

    const selectedSeats  = seatsData.filter(s => selectedMulti.has(String(s.seatID)));
    const sourceTypes    = [...new Set(selectedSeats.map(s => Number(s.seatTypeID)))];
    const count          = selectedSeats.length;

    if (sourceTypes.length > 1) {
        el.className = 'batch-validation err';
        el.textContent = `❌ Các ghế được chọn không cùng loại (${sourceTypes.map(t => TYPE_NAME[t] || t).join(', ')}). Vui lòng chọn lại.`;
        btnApply.disabled = true;
        return false;
    }

    const sourceTypeID = sourceTypes[0];

    if (targetID === sourceTypeID) {
        el.className = 'batch-validation err';
        el.textContent = `❌ Loại đích phải khác loại nguồn (${TYPE_NAME[sourceTypeID] || ''}).`;
        btnApply.disabled = true;
        return false;
    }

    const availableTargets = seatsData.filter(
        s => Number(s.seatTypeID) === targetID && !selectedMulti.has(String(s.seatID))
    );

    if (availableTargets.length < count) {
        el.className = 'batch-validation err';
        el.textContent = `❌ Không đủ ghế ${TYPE_NAME[targetID] || ''} để hoán đổi. `
            + `Cần ${count} ghế, hiện có ${availableTargets.length}.`;
        btnApply.disabled = true;
        return false;
    }

    el.className = 'batch-validation ok';
    el.textContent = `✓ Hợp lệ: đổi ${count} ghế ${TYPE_NAME[sourceTypeID] || ''} `
        + `→ ${TYPE_NAME[targetID] || ''}, `
        + `đồng thời ${count} ghế ${TYPE_NAME[targetID] || ''} → ${TYPE_NAME[sourceTypeID] || ''}.`;
    btnApply.disabled = false;
    return true;
}

function applyBatch() {
    if (!selectedMulti.size) { showToast('Chưa chọn ghế nào', 'warning'); return; }
    if (!validateBatch()) return;

    const targetID      = parseInt(document.getElementById('batchTargetType').value);
    const selectedSeats = seatsData.filter(s => selectedMulti.has(String(s.seatID)));
    const sourceTypeID  = Number(selectedSeats[0].seatTypeID);
    const count         = selectedSeats.length;

    const targetsToSwap = seatsData
        .filter(s => Number(s.seatTypeID) === targetID && !selectedMulti.has(String(s.seatID)))
        .slice(0, count);

    const sourceIDs = [...selectedMulti].map(Number);
    const targetIDs = targetsToSwap.map(s => s.seatID);

    const sourceTypeName = TYPE_NAME[sourceTypeID] || '';
    const targetTypeName = TYPE_NAME[targetID] || '';

    if (!confirm(
        `Hoán đổi ${count} ghế:\n`
        + `• ${count} ghế ${sourceTypeName} (đang chọn) → ${targetTypeName}\n`
        + `• ${count} ghế ${targetTypeName} → ${sourceTypeName}\n\n`
        + `Ghế có vé đã bán sẽ bị bỏ qua.`
    )) return;

    const btnApply = document.getElementById('btnApplyBatch');
    btnApply.disabled = true;

    apiFetch('/admins/seat/ajax-batch-update-type', 'POST', {
        seatIDs: sourceIDs, seatTypeID: targetID
    })
    .then(res1 => {
        if (!res1.success) throw new Error(res1.message || 'Lỗi bước 1');
        return apiFetch('/admins/seat/ajax-batch-update-type', 'POST', {
            seatIDs: targetIDs, seatTypeID: sourceTypeID
        });
    })
    .then(res2 => {
        if (!res2.success) throw new Error(res2.message || 'Lỗi bước 2');
        showToast(`Hoán đổi thành công ${count} cặp ghế`, 'success');
        resetMode();
        loadSeats(true);
    })
    .catch(err => {
        showToast(err.message, 'error');
        loadSeats(true);
    })
    .finally(() => { btnApply.disabled = false; });
}

// ══════════════════════════════════════════════════════════
// ADD / DELETE
// ══════════════════════════════════════════════════════════
function addSeat() {
    const row    = document.getElementById('addRow').value.trim().toUpperCase();
    const col    = parseInt(document.getElementById('addCol').value);
    const typeID = parseInt(document.getElementById('addType').value);

    if (!row || !/^[A-Z]$/.test(row)) { showToast('Hàng phải là 1 chữ A–Z', 'error'); return; }
    if (!col || col < 1 || col > 99)  { showToast('Cột phải từ 1–99', 'error'); return; }
    if (seatsData.length >= CAPACITY) {
        if (!confirm(`Phòng đã có ${seatsData.length}/${CAPACITY} ghế. Vẫn thêm?`)) return;
    }
    if (seatsData.some(s => s.rowSeat === row && +s.colSeat === col)) {
        showToast(`Ghế ${row}${col} đã tồn tại`, 'error'); return;
    }
    const btn = document.getElementById('btnAdd');
    btn.disabled = true;
    apiFetch('/admins/seat/ajax-add', 'POST', {
        roomID: ROOM_ID, rowSeat: row, colSeat: col, seatTypeID: typeID
    }).then(res => {
        if (res.error) { showToast(res.error, 'error'); return; }
        showToast(`Đã thêm ghế ${row}${col} (${TYPE_NAME[typeID] || ''})`, 'success');
        document.getElementById('addRow').value = '';
        document.getElementById('addCol').value = '';
        loadSeats(true);
    }).catch(err => showToast(err.message, 'error'))
      .finally(() => { btn.disabled = false; });
}

function deleteSeat(id, event) {
    event.stopPropagation();
    if (!confirm('Xoá ghế này?')) return;
    fetch(`/admins/seat/ajax-delete/${id}`, {
        method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF }
    }).then(r => r.json()).then(res => {
        if (res.error) { showToast(res.error, 'error'); return; }
        if (sourceSeat && String(sourceSeat.id) === String(id)) resetMode();
        selectedMulti.delete(String(id));
        loadSeats(true);
        showToast('Đã xoá ghế', 'success');
    }).catch(() => showToast('Lỗi kết nối', 'error'));
}

// ══════════════════════════════════════════════════════════
// RESET
// ══════════════════════════════════════════════════════════
function resetMode() {
    appMode = 'idle';
    sourceSeat = null;
    selectedMulti.clear();
    clearStyles();
    document.getElementById('swapIdle').style.display = '';
    document.getElementById('swapPanel').classList.remove('show');
    document.getElementById('batchPanel').classList.remove('show');
    document.getElementById('hintSwap').classList.remove('show');
    document.getElementById('batchValidation').className = 'batch-validation';
    document.getElementById('batchValidation').textContent = '';
    document.getElementById('batchSourceType').textContent = '';
}

// ══════════════════════════════════════════════════════════
// UTILS
// ══════════════════════════════════════════════════════════
function getSwapTarget() { return parseInt(document.getElementById('swapTargetType').value); }

function apiFetch(url, method, body) {
    return fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(body),
    }).then(r => {
        if (!r.ok) return r.text().then(t => {
            try { const j = JSON.parse(t); throw new Error(j.message || j.error || `Lỗi ${r.status}`); }
            catch(e) { throw new Error(t.trim().startsWith('<') ? `Lỗi ${r.status}` : t); }
        });
        return r.json();
    });
}

function set(id, val) { const e = document.getElementById(id); if (e) e.textContent = val; }
function showSpinner(on) { document.getElementById('gridSpinner').classList.toggle('show', on); }

let _tt;
const TICO = { success:'✓', error:'✕', info:'ℹ', warning:'⚠' };
function showToast(msg, type = 'success') {
    const el = document.getElementById('toast');
    document.getElementById('t-ico').textContent = TICO[type] || '';
    document.getElementById('t-msg').textContent = msg;
    el.className = `show ${type}`;
    clearTimeout(_tt);
    _tt = setTimeout(() => { el.className = ''; }, 3500);
}

document.getElementById('addRow').addEventListener('keydown', e => { if (e.key === 'Enter') document.getElementById('addCol').focus(); });
document.getElementById('addCol').addEventListener('keydown', e => { if (e.key === 'Enter') addSeat(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') resetMode(); });

@if(session('success'))
    document.addEventListener('DOMContentLoaded', () => showToast({{ Js::from(session('success')) }}, 'success'));
@endif
@if(session('error'))
    document.addEventListener('DOMContentLoaded', () => showToast({{ Js::from(session('error')) }}, 'error'));
@endif

document.addEventListener('DOMContentLoaded', loadSeats);
</script>
@endsection