@extends('layouts.appAdmin')

@section('content')
<style>
    :root {
        --bg-deep: #080f1e;
        --bg-card: #0f1a2e;
        --bg-panel: #111827;
        --border: #1e2d45;
        --muted: #6b7280;
        --accent: #3b82f6;
    }

    body {
        background: var(--bg-deep);
        color: #e5e7eb;
    }

    .panel-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 20px 24px;
        margin-bottom: 16px;
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
        color: #e5e7eb;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14px;
        width: 100%;
        transition: border-color .2s;
    }

    .ctrl:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .15);
    }

    .ctrl option { background: #1f2937; }

    .form-label {
        font-size: 12px;
        color: #9ca3af;
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

    .bg {
        background: var(--bg-panel);
        color: var(--muted);
        border: 1px solid var(--border);
    }
    .bg:hover:not(:disabled) { background: #1f2937; color: #e5e7eb; }

    .bs { background: #14532d; color: #86efac; border: 1px solid #22c55e; }
    .bw { background: #78350f; color: #fcd34d; border: 1px solid #f59e0b; }
    .bk { background: #831843; color: #f9a8d4; border: 1px solid #ec4899; }
    .bm { background: #1e1b4b; color: #a5b4fc; border: 1px solid #6366f1; }
    .bm:hover:not(:disabled) { background: #312e81; }

    .swap-info {
        background: #1e3a5f22;
        border: 1px solid #1e40af;
        border-radius: 12px;
        padding: 14px 18px;
        display: none;
        gap: 16px;
        align-items: center;
        flex-wrap: wrap;
    }
    .swap-info.show { display: flex; }

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
    }

    .batch-panel {
        background: #0f172a;
        border: 1px solid #312e81;
        border-radius: 12px;
        padding: 14px 18px;
        display: none;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
        margin-top: 10px;
    }
    .batch-panel.show { display: flex; }

    .mode-hint {
        border-radius: 10px;
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 600;
        display: none;
        margin-top: 10px;
    }
    .mode-hint.show { display: block; }
    .hint-swap { background: #0f172a; border: 1px dashed #22c55e; color: #86efac; }
    .hint-couple { background: #1a0a1a; border: 1px dashed #ec4899; color: #f9a8d4; }

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
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .stat-pill .dot { width: 8px; height: 8px; border-radius: 50%; }

    .screen-wrap { text-align: center; margin-bottom: 16px; }

    .screen-bar {
        width: 50%;
        height: 6px;
        margin: 0 auto 6px;
        background: linear-gradient(to right, transparent, #fbbf24, transparent);
        border-radius: 999px;
        box-shadow: 0 0 20px rgba(251, 191, 36, .4);
    }

    .screen-label {
        font-size: 10px;
        letter-spacing: .18em;
        color: #fbbf24;
        text-transform: uppercase;
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

    .seat.t-normal { background: #1e3a5f; color: #93c5fd; border-color: #1e40af; }
    .seat.t-vip    { background: #92400e; color: #fcd34d; border-color: #d97706; }
    .seat.t-couple { background: #831843; color: #f9a8d4; border-color: #db2777; }
    .seat.t-maint  { background: #374151; color: #9ca3af; border-color: #4b5563; }

    .seat.t-couple.couple-wide {
        width: 68px;
        border-radius: 10px;
        font-size: 8px;
        gap: 2px;
        flex-direction: column;
        line-height: 1.1;
    }

    .seat.is-source {
        outline: 3px solid #60a5fa;
        box-shadow: 0 0 0 5px rgba(96, 165, 250, .3);
        z-index: 30;
        transform: scale(1.1);
    }
    .seat.is-couple-pair {
        outline: 3px solid #60a5fa;
        box-shadow: 0 0 0 4px rgba(96, 165, 250, .2);
        z-index: 20;
        transform: scale(1.05);
    }
    .seat.is-swap-tgt {
        outline: 3px solid #22c55e;
        box-shadow: 0 0 0 5px rgba(34, 197, 94, .3);
        animation: pg .8s infinite alternate;
        z-index: 25;
    }
    .seat.is-couple-tgt {
        outline: 3px solid #ec4899;
        box-shadow: 0 0 0 5px rgba(236, 72, 153, .3);
        animation: pp .8s infinite alternate;
        z-index: 25;
    }
    .seat.is-dimmed { opacity: .2; pointer-events: none; }
    .seat.is-multi {
        outline: 3px solid #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, .25);
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
        from { box-shadow: 0 0 0 3px rgba(34,197,94,.2) }
        to   { box-shadow: 0 0 0 8px rgba(34,197,94,.4) }
    }
    @keyframes pp {
        from { box-shadow: 0 0 0 3px rgba(236,72,153,.2) }
        to   { box-shadow: 0 0 0 8px rgba(236,72,153,.4) }
    }

    .empty-slot {
        background: #0f172a;
        border: 1px dashed #1e293b;
        color: #1e3a5f;
        cursor: crosshair;
        font-size: 14px;
        font-weight: 300;
    }
    .empty-slot:hover {
        border-color: #22c55e;
        color: #22c55e;
        background: #071a0e;
        transform: scale(1.05);
    }

    .disabled-slot {
        background: #050c1a33;
        border: 1px dashed #0d172633;
        color: #0d172633;
        cursor: pointer;
        font-size: 12px;
        opacity: .4;
        border-radius: 8px;
    }
    .disabled-slot:hover {
        border-color: #1e3a5f;
        color: #3b82f6;
        background: #080f1e;
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
    .legend-item { display: flex; align-items: center; gap: 7px; font-size: 11px; color: #9ca3af; }
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
    #toast.success { background: #14532d; border: 1px solid #22c55e; color: #bbf7d0; }
    #toast.error   { background: #7f1d1d; border: 1px solid #ef4444; color: #fecaca; }
    #toast.info    { background: #1e3a5f; border: 1px solid #3b82f6; color: #bfdbfe; }
    #toast.warning { background: #78350f; border: 1px solid #f59e0b; color: #fcd34d; }
</style>

{{-- ══ PANEL 1: PHÒNG + STATS ══ --}}
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
                            {{ $r->roomName }} — {{ $r->capacity }} ghế
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
                <span class="stat-pill"><span class="dot" style="background:#6b7280"></span>Tổng: <b id="st-total">0</b></span>
                <span class="stat-pill"><span class="dot" style="background:#3b82f6"></span>Thường: <b id="st-normal">0</b></span>
                <span class="stat-pill"><span class="dot" style="background:#f59e0b"></span>VIP: <b id="st-vip">0</b></span>
                <span class="stat-pill"><span class="dot" style="background:#ec4899"></span>Đôi: <b id="st-couple">0</b></span>
                <span class="stat-pill"><span class="dot" style="background:#6b7280"></span>Bảo trì: <b id="st-maint">0</b></span>
                <span class="stat-pill"><span class="dot" style="background:#374151"></span>Hàng: <b id="st-rows">0</b> | Cột: <b id="st-cols">0</b></span>
            </div>
        </div>
    </div>
</div>

{{-- ══ PANEL 2: THAO TÁC GHẾ ══ --}}
<div class="panel-card">
    <div class="panel-title">Thao tác ghế</div>

    <p id="swapIdle" style="color:var(--muted);font-size:13px;margin:0;">
        ← Bấm vào một ghế để thao tác đơn lẻ. Nhấn giữ và kéo chuột trên lưới để chọn nhiều ghế.
    </p>

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
            <button class="btn-c bs" id="btnSwap"   onclick="startSwapPicking()">⇄ Hoán đổi</button>
            <button class="btn-c bk" id="btnCouple" onclick="startCouplePicking()">💑 Ghế đôi</button>
            <button class="btn-c bw" id="btnMaint"  onclick="toggleMaintenance()">⚙ Bảo trì</button>
            <button class="btn-c bg"                onclick="resetMode()">✕ Huỷ</button>
        </div>
    </div>

    <div class="mode-hint hint-swap"   id="hintSwap"></div>
    <div class="mode-hint hint-couple" id="hintCouple">
        💑 Bấm vào ghế <b>liền kề cùng hàng</b> (tô hồng) để ghép thành cặp ghế đôi.
    </div>

    <div class="batch-panel" id="batchPanel">
        <div>
            <div class="form-label" style="margin-bottom:4px;">Đã chọn</div>
            <div class="seat-badge">
                <span style="color:#a5b4fc">⬡</span>
                <span id="batchCount">0</span> ghế
            </div>
        </div>
        <div style="flex:1;min-width:150px;">
            <div class="form-label" style="margin-bottom:4px;">Đổi tất cả sang loại</div>
            <select id="batchTargetType" class="ctrl">
                @foreach($seatTypes as $type)
                    <option value="{{ $type->seatTypeID }}">{{ $type->seatTypeName }}</option>
                @endforeach
            </select>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn-c bm" onclick="applyBatch()">✦ Áp dụng</button>
            <button class="btn-c bg" onclick="resetMode()">✕ Huỷ chọn</button>
        </div>
    </div>
</div>

{{-- ══ PANEL 3: THÊM GHẾ ══ --}}
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
    <div id="capWarn" style="display:none;color:#f59e0b;font-size:12px;margin-top:6px;">
        ⚠ Số ghế hiện tại đã bằng sức chứa phòng.
    </div>
</div>

{{-- ══ GRID ══ --}}
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
        <div class="legend-box" style="background:#1e3a5f;border:2px solid #1e40af"></div>Ghế thường
    </div>
    <div class="legend-item">
        <div class="legend-box" style="background:#92400e;border:2px solid #d97706"></div>Ghế VIP
    </div>
    <div class="legend-item">
        <div class="legend-box" style="background:#831843;border:2px solid #db2777;width:36px"></div>Ghế đôi
    </div>
    <div class="legend-item">
        <div class="legend-box" style="background:#374151;border:2px solid #4b5563"></div>Bảo trì
    </div>
    <div class="legend-item">
        <div class="legend-box" style="background:#0f172a;border:1px dashed #1e293b"></div>Ô trống
    </div>
    <div class="legend-item">
        <div class="legend-box" style="outline:3px solid #6366f1;border-radius:5px;background:#1e3a5f"></div>Chọn nhiều
    </div>
</div>

<div id="toast"><span id="t-ico"></span><span id="t-msg"></span></div>

<script>
'use strict';

const ROOM_ID  = parseInt("{{ (int)($roomID ?? 0) }}", 10);
const CAPACITY = parseInt("{{ $room?->capacity ?? 0 }}", 10);
const CSRF     = "{{ csrf_token() }}";

const TYPE_CLASS = { 1:'t-vip', 2:'t-normal', 3:'t-couple', 4:'t-maint' };
const TYPE_NAME  = { 1:'VIP',   2:'Thường',   3:'Đôi',      4:'Bảo trì' };
const TYPE_COLOR = { 1:'#f59e0b', 2:'#3b82f6', 3:'#ec4899', 4:'#6b7280' };

// ── State ──────────────────────────────────────────────────────────────────
let seatsData     = [];
let appMode       = 'idle'; // idle | selected | swap_picking | couple_picking | multi_select
let sourceSeat    = null;
let selectedMulti = new Set();

// ── Drag ───────────────────────────────────────────────────────────────────
let drag = { active:false, pending:false, startX:0, startY:0 };
let dragJustFinished = false;
const DRAG_THRESHOLD = 6;

// ══════════════════════════════════════════════════════════════════════════
// HELPERS
// ══════════════════════════════════════════════════════════════════════════

// Tìm ghế đôi partner — cột kề trong cùng hàng, cùng type=3
function findCouplePair(seat) {
    if (!seat || seat.seatTypeID !== 3) return null;
    const col = +seat.colSeat;
    return seatsData.find(s =>
        s.seatTypeID === 3 &&
        s.rowSeat    === seat.rowSeat &&
        Math.abs(+s.colSeat - col) === 1 &&
        s.seatID !== seat.seatID
    ) || null;
}

// Set tất cả seatID đã được ghép đôi (cả 2 ô)
function buildPairedIDs() {
    const paired = new Set();
    seatsData.forEach(s => {
        if (s.seatTypeID === 3) {
            const p = findCouplePair(s);
            if (p) {
                paired.add(String(s.seatID));
                paired.add(String(p.seatID));
            }
        }
    });
    return paired;
}

// Set seatID là "ô phải" (ô thứ 2) của cặp đôi → dùng để skip khi render
function buildSecondSeatIDs() {
    const seconds = new Set();
    seatsData.forEach(s => {
        if (s.seatTypeID !== 3) return;
        const pair = findCouplePair(s);
        if (!pair) return;
        // Ô có cột LỚN HƠN = ô thứ 2 → skip
        if (+s.colSeat > +pair.colSeat) {
            seconds.add(String(s.seatID));
        }
    });
    return seconds;
}

// Đếm ghế thực tế theo capacity (ghế đôi 1 cặp = 1 đơn vị)
function getRealSeatCount() {
    const secondIDs = buildSecondSeatIDs();
    let n = 0;
    seatsData.forEach(s => {
        if (s.seatTypeID === 3) {
            if (!secondIDs.has(String(s.seatID))) n++; // chỉ đếm ô trái
        } else {
            n++;
        }
    });
    return n;
}

// ══════════════════════════════════════════════════════════════════════════
// LOAD & RENDER
// ══════════════════════════════════════════════════════════════════════════
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
            seatsData = Array.isArray(data) ? data : [];
            renderGrid();
            updateStats();
        })
        .catch(err => showToast(err.message, 'error'))
        .finally(() => showSpinner(false));
}

function updateStats() {
    const rows   = [...new Set(seatsData.map(s => s.rowSeat))];
    const maxCol = seatsData.reduce((m, s) => Math.max(m, +s.colSeat), 0);
    const cnt    = { 1:0, 2:0, 3:0, 4:0 };
    seatsData.forEach(s => cnt[s.seatTypeID] = (cnt[s.seatTypeID] || 0) + 1);

    set('st-total',  getRealSeatCount());
    set('st-normal', cnt[2] || 0);
    set('st-vip',    cnt[1] || 0);
    set('st-couple', Math.floor((cnt[3] || 0) / 2)); // mỗi cặp = 2 record
    set('st-maint',  cnt[4] || 0);
    set('st-rows',   rows.length);
    set('st-cols',   maxCol);

    document.getElementById('capWarn').style.display =
        getRealSeatCount() >= CAPACITY ? 'block' : 'none';
}

// ── Render grid ────────────────────────────────────────────────────────────
function renderGrid() {
    const grid = document.getElementById('seatGrid');

    // Build map và tính secondIDs 1 lần
    const seatMap   = {};
    seatsData.forEach(s => { seatMap[`${s.rowSeat}-${s.colSeat}`] = s; });

    const secondIDs = buildSecondSeatIDs(); // ô phải của cặp đôi → skip

    const rows      = [...new Set(seatsData.map(s => s.rowSeat))].sort();
    const maxCol    = seatsData.reduce((m, s) => Math.max(m, +s.colSeat), 0);
    const renderCols = maxCol + 1; // +1 cho expansion slot

    let html = '';

    rows.forEach(row => {
        html += `<div class="seat-row"><span class="row-label">${row}</span>`;

        for (let col = 1; col <= renderCols; col++) {
            const seat = seatMap[`${row}-${col}`];

            // Ô trống hoặc expansion
            if (!seat) {
                if (col === renderCols) {
                    html += `<div class="disabled-slot"
                                  data-row="${row}" data-col="${col}"
                                  onclick="quickFill('${row}',${col})"
                                  title="Thêm ghế ${row}${col}">+</div>`;
                } else {
                    html += `<div class="empty-slot"
                                  data-row="${row}" data-col="${col}">+</div>`;
                }
                continue;
            }

            // ── Ghế đôi ──
            if (seat.seatTypeID === 3) {
                // Ô phải → đã được render bởi ô trái, skip
                if (secondIDs.has(String(seat.seatID))) continue;

                // Ô trái → render wide (đại diện cho cả cặp)
                const pair     = findCouplePair(seat);
                const pairName = pair ? `${pair.rowSeat}${pair.colSeat}` : '';
                html += `<div class="seat t-couple couple-wide"
                              data-id="${seat.seatID}"
                              data-row="${row}" data-col="${col}"
                              data-type="${seat.seatTypeID}"
                              data-name="${row}${col}"
                              data-pairid="${pair ? pair.seatID : ''}"
                              data-pairname="${pairName}">
                            <span>${row}${col}${pairName ? '·' + pairName : ''}</span>
                            <span class="del-btn"
                                  onmousedown="event.stopPropagation()"
                                  onclick="deleteSeat(${seat.seatID},event)">×</span>
                         </div>`;
                continue;
            }

            // ── Ghế thường / VIP / bảo trì ──
            const cls = TYPE_CLASS[seat.seatTypeID] || 't-normal';
            html += `<div class="seat ${cls}"
                          data-id="${seat.seatID}"
                          data-row="${row}" data-col="${col}"
                          data-type="${seat.seatTypeID}"
                          data-name="${row}${col}">
                        ${row}${col}
                        <span class="del-btn"
                              onmousedown="event.stopPropagation()"
                              onclick="deleteSeat(${seat.seatID},event)">×</span>
                     </div>`;
        }

        html += `</div>`;
    });

    // Hàng mở rộng tiếp theo
    if (rows.length > 0) {
        const lastRow = rows[rows.length - 1];
        const nextRow = lastRow < 'Z'
            ? String.fromCharCode(lastRow.charCodeAt(0) + 1)
            : null;

        if (nextRow) {
            html += `<div class="seat-row">
                        <span class="row-label" style="color:#374151">${nextRow}</span>`;
            for (let col = 1; col <= renderCols; col++) {
                html += `<div class="disabled-slot"
                              data-row="${nextRow}" data-col="${col}"
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

// ── Quick fill ─────────────────────────────────────────────────────────────
function quickFill(row, col) {
    if (appMode === 'multi_select') return;
    document.getElementById('addRow').value = row;
    document.getElementById('addCol').value = col;
    showToast(`Điền ${row}${col} → form thêm ghế`, 'info');
    document.getElementById('addRow').scrollIntoView({ behavior:'smooth', block:'center' });
}

// ══════════════════════════════════════════════════════════════════════════
// GRID EVENTS
// ══════════════════════════════════════════════════════════════════════════
function bindGridEvents() {
    const grid = document.getElementById('seatGrid');

    document.querySelectorAll('.seat').forEach(el => {
        el.addEventListener('click', function(e) {
            if (dragJustFinished) { dragJustFinished = false; return; }
            if (appMode === 'multi_select') return;
            if (appMode === 'swap_picking') {
                if (this.classList.contains('is-swap-tgt')) confirmSwap(this);
                return;
            }
            if (appMode === 'couple_picking') {
                if (this.classList.contains('is-couple-tgt')) confirmCouple(this);
                return;
            }
            selectSource(this);
        });
    });

    document.querySelectorAll('.empty-slot').forEach(el => {
        el.addEventListener('click', function() {
            quickFill(this.dataset.row, this.dataset.col);
        });
    });

    grid.addEventListener('mousedown', function(e) {
        if (e.button !== 0) return;
        if (appMode === 'swap_picking' || appMode === 'couple_picking') return;
        drag = { active:false, pending:true, startX:e.clientX, startY:e.clientY };
        e.preventDefault();
    });
}

document.addEventListener('mousemove', function(e) {
    if (!drag.pending && !drag.active) return;
    const dx = Math.abs(e.clientX - drag.startX);
    const dy = Math.abs(e.clientY - drag.startY);
    if (!drag.active && (dx > DRAG_THRESHOLD || dy > DRAG_THRESHOLD)) {
        drag.active  = true;
        drag.pending = false;
        if (appMode === 'selected') resetMode();
        document.getElementById('seatGrid').classList.add('dragging');
    }
    if (!drag.active) return;

    const el   = document.elementFromPoint(e.clientX, e.clientY);
    const seat = el ? el.closest('.seat') : null;
    if (seat && !selectedMulti.has(seat.dataset.id)) {
        selectedMulti.add(seat.dataset.id);
        seat.classList.add('is-multi');
        set('batchCount', selectedMulti.size);
    }
});

document.addEventListener('mouseup', function() {
    if (!drag.pending && !drag.active) return;
    const wasDragging = drag.active;
    drag = { active:false, pending:false, startX:0, startY:0 };
    document.getElementById('seatGrid').classList.remove('dragging');
    if (!wasDragging) return;

    if (selectedMulti.size > 0) {
        dragJustFinished = true;
        appMode = 'multi_select';
        document.getElementById('swapIdle').style.display = 'none';
        document.getElementById('swapPanel').classList.remove('show');
        document.getElementById('batchPanel').classList.add('show');
        set('batchCount', selectedMulti.size);
        showToast(`Đã chọn ${selectedMulti.size} ghế`, 'info');
    }
});

// ══════════════════════════════════════════════════════════════════════════
// SELECT SINGLE SEAT
// ══════════════════════════════════════════════════════════════════════════
function selectSource(el) {
    sourceSeat = {
        id:       el.dataset.id,
        name:     el.dataset.name,
        typeID:   +el.dataset.type,
        row:      el.dataset.row,
        col:      +el.dataset.col,
        pairID:   el.dataset.pairid   || null,
        pairName: el.dataset.pairname || null,
    };
    appMode = 'selected';

    document.getElementById('swapIdle').style.display = 'none';
    document.getElementById('swapPanel').classList.add('show');
    document.getElementById('batchPanel').classList.remove('show');
    document.getElementById('hintSwap').classList.remove('show');
    document.getElementById('hintCouple').classList.remove('show');

    // Badge
    let badge = `<span style="color:${TYPE_COLOR[sourceSeat.typeID]}">■</span> ${sourceSeat.name}`;
    if (sourceSeat.typeID === 3 && sourceSeat.pairName) {
        badge += ` <span style="color:#6b7280;font-size:10px">+</span>
                   <span style="color:#ec4899">■</span> ${sourceSeat.pairName}`;
    }
    badge += ` <span style="color:#6b7280;font-size:11px">(${TYPE_NAME[sourceSeat.typeID]})</span>`;
    document.getElementById('swapBadge').innerHTML = badge;

    // Nút bảo trì
    const btnMaint = document.getElementById('btnMaint');
    if (sourceSeat.typeID === 4) {
        btnMaint.textContent = '✓ Phục hồi';
        btnMaint.className   = 'btn-c bs';
    } else {
        btnMaint.textContent = '⚙ Bảo trì';
        btnMaint.className   = 'btn-c bw';
    }

    // Ẩn/hiện nút theo loại ghế
    const isCouple = sourceSeat.typeID === 3;
    document.getElementById('btnSwap').style.display   = isCouple ? 'none' : '';
    document.getElementById('btnCouple').style.display = isCouple ? 'none' : '';

    // Default swap target khác loại hiện tại
    const sel = document.getElementById('swapTargetType');
    for (let o of sel.options) {
        if (+o.value !== sourceSeat.typeID) { o.selected = true; break; }
    }

    reApplyStyles();
}

// ══════════════════════════════════════════════════════════════════════════
// STYLES
// ══════════════════════════════════════════════════════════════════════════
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
            if (el.dataset.id === src) { el.classList.add('is-source'); return; }
            if (sourceSeat.pairID && el.dataset.id === String(sourceSeat.pairID)) {
                el.classList.add('is-couple-pair'); return;
            }
        });
    }

    if (appMode === 'swap_picking') {
        const tid = getSwapTarget();
        document.querySelectorAll('.seat').forEach(el => {
            if (el.dataset.id === src)       { el.classList.add('is-source');   return; }
            if (+el.dataset.type === tid)    { el.classList.add('is-swap-tgt'); return; }
            el.classList.add('is-dimmed');
        });
    }

    if (appMode === 'couple_picking') {
        const pairedIDs = buildPairedIDs();
        document.querySelectorAll('.seat').forEach(el => {
            if (el.dataset.id === src) { el.classList.add('is-source'); return; }
            const adj   = el.dataset.row === sourceSeat.row &&
                          Math.abs(+el.dataset.col - sourceSeat.col) === 1;
            const valid = adj && +el.dataset.type !== 3 && +el.dataset.type !== 4 &&
                          !pairedIDs.has(el.dataset.id);
            if (valid) { el.classList.add('is-couple-tgt'); return; }
            el.classList.add('is-dimmed');
        });
    }
}

function clearStyles() {
    document.querySelectorAll('.seat').forEach(el =>
        el.classList.remove('is-source','is-couple-pair','is-swap-tgt',
                            'is-couple-tgt','is-dimmed','is-multi')
    );
}

// ══════════════════════════════════════════════════════════════════════════
// SWAP
// ══════════════════════════════════════════════════════════════════════════
function startSwapPicking() {
    if (!sourceSeat) return;
    const tid = getSwapTarget();
    if (tid === sourceSeat.typeID) { showToast('Chọn loại khác với hiện tại', 'warning'); return; }
    if (tid === 3) { showToast('Dùng nút "Ghế đôi" để tạo ghế đôi', 'warning'); return; }

    const avail = seatsData.filter(s => s.seatTypeID === tid && String(s.seatID) !== sourceSeat.id);
    if (!avail.length) { showToast(`Không có ghế ${TYPE_NAME[tid]} để hoán đổi`, 'warning'); return; }

    appMode = 'swap_picking';
    reApplyStyles();
    const h = document.getElementById('hintSwap');
    h.textContent = `⇄ Chọn 1 ghế ${TYPE_NAME[tid]} (tô xanh) để hoán đổi với ${sourceSeat.name}. [Esc] huỷ.`;
    h.classList.add('show');
}

function confirmSwap(el) {
    if (!confirm(`Hoán đổi:\n${sourceSeat.name}(${TYPE_NAME[sourceSeat.typeID]}) ⇄ ${el.dataset.name}(${TYPE_NAME[+el.dataset.type]})?`)) return;
    apiFetch('/admins/seat/ajax-swap-type', 'POST', {
        seatID_a: parseInt(sourceSeat.id),
        seatID_b: parseInt(el.dataset.id)
    }).then(res => {
        if (!res.success) { showToast(res.message, 'error'); return; }
        showToast('Hoán đổi thành công', 'success');
        resetMode(); loadSeats(true);
    }).catch(err => showToast(err.message, 'error'));
}

// ══════════════════════════════════════════════════════════════════════════
// COUPLE
// ══════════════════════════════════════════════════════════════════════════
function startCouplePicking() {
    if (!sourceSeat || sourceSeat.typeID === 3) return;
    const pairedIDs = buildPairedIDs();
    const adj = seatsData.filter(s =>
        s.rowSeat === sourceSeat.row &&
        Math.abs(+s.colSeat - sourceSeat.col) === 1 &&
        s.seatTypeID !== 3 && s.seatTypeID !== 4 &&
        !pairedIDs.has(String(s.seatID))
    );
    if (!adj.length) { showToast('Không có ghế liền kề hợp lệ để ghép đôi', 'warning'); return; }

    appMode = 'couple_picking';
    reApplyStyles();
    document.getElementById('hintCouple').classList.add('show');
    document.getElementById('hintSwap').classList.remove('show');
}

function confirmCouple(el) {
    if (!confirm(`Tạo ghế đôi:\n• ${sourceSeat.name} (${TYPE_NAME[sourceSeat.typeID]})\n• ${el.dataset.name} (${TYPE_NAME[+el.dataset.type]})\nCả 2 → Ghế đôi.`)) return;
    apiFetch('/admins/seat/ajax-convert-couple', 'POST', {
        seatID_a: parseInt(sourceSeat.id),
        seatID_b: parseInt(el.dataset.id)
    }).then(res => {
        if (!res.success) { showToast(res.message, 'error'); return; }
        showToast(`Đã tạo ghế đôi: ${sourceSeat.name} + ${el.dataset.name}`, 'success');
        resetMode(); loadSeats(true);
    }).catch(err => showToast(err.message, 'error'));
}

// ══════════════════════════════════════════════════════════════════════════
// MAINTENANCE
// ══════════════════════════════════════════════════════════════════════════
function toggleMaintenance() {
    if (!sourceSeat) return;
    const isMaint = sourceSeat.typeID === 4;
    const newType = isMaint ? 2 : 4;
    const label   = isMaint ? 'Phục hồi thành ghế thường' : 'Chuyển sang bảo trì';
    if (!confirm(`${label}: ghế ${sourceSeat.name}?`)) return;

    apiFetch('/admins/seat/ajax-update-type', 'POST', {
        seatID:     parseInt(sourceSeat.id),
        seatTypeID: newType
    }).then(res => {
        if (!res.success) { showToast(res.message, 'error'); return; }
        showToast(`Ghế ${sourceSeat.name} → ${TYPE_NAME[newType]}`, 'success');
        resetMode(); loadSeats(true);
    }).catch(err => showToast(err.message, 'error'));
}

// ══════════════════════════════════════════════════════════════════════════
// BATCH UPDATE
// ══════════════════════════════════════════════════════════════════════════
function applyBatch() {
    if (!selectedMulti.size) { showToast('Chưa chọn ghế nào', 'warning'); return; }
    const typeID   = parseInt(document.getElementById('batchTargetType').value);
    const typeName = TYPE_NAME[typeID] || '?';
    if (!confirm(`Đổi ${selectedMulti.size} ghế → ${typeName}?\n(Ghế đôi đang ghép và ghế có vé sẽ bị bỏ qua)`)) return;

    apiFetch('/admins/seat/ajax-batch-update-type', 'POST', {
        seatIDs:    [...selectedMulti].map(Number),
        seatTypeID: typeID
    }).then(res => {
        if (!res.success) { showToast(res.message, 'error'); return; }
        showToast(res.message || 'Cập nhật thành công', 'success');
        resetMode(); loadSeats(true);
    }).catch(err => showToast(err.message, 'error'));
}

// ══════════════════════════════════════════════════════════════════════════
// THÊM GHẾ
// ══════════════════════════════════════════════════════════════════════════
function addSeat() {
    const row    = document.getElementById('addRow').value.trim().toUpperCase();
    const col    = parseInt(document.getElementById('addCol').value);
    const typeID = parseInt(document.getElementById('addType').value);

    if (!row || !/^[A-Z]$/.test(row)) { showToast('Hàng phải là 1 chữ A–Z', 'error'); return; }
    if (!col || col < 1 || col > 99)  { showToast('Cột phải từ 1–99', 'error'); return; }

    if (getRealSeatCount() >= CAPACITY) {
        if (!confirm(`Phòng đã có ${getRealSeatCount()}/${CAPACITY} ghế. Vẫn thêm?`)) return;
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

// ══════════════════════════════════════════════════════════════════════════
// XOÁ GHẾ
// ══════════════════════════════════════════════════════════════════════════
function deleteSeat(id, event) {
    event.stopPropagation();
    if (!confirm('Xoá ghế này?')) return;
    fetch(`/admins/seat/ajax-delete/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF }
    }).then(r => r.json()).then(res => {
        if (res.error) { showToast(res.error, 'error'); return; }
        if (sourceSeat && String(sourceSeat.id) === String(id)) resetMode();
        selectedMulti.delete(String(id));
        loadSeats(true);
        showToast('Đã xoá ghế', 'success');
    }).catch(() => showToast('Lỗi kết nối', 'error'));
}

// ══════════════════════════════════════════════════════════════════════════
// RESET
// ══════════════════════════════════════════════════════════════════════════
function resetMode() {
    appMode = 'idle';
    sourceSeat = null;
    selectedMulti.clear();
    clearStyles();
    document.getElementById('swapIdle').style.display = '';
    document.getElementById('swapPanel').classList.remove('show');
    document.getElementById('batchPanel').classList.remove('show');
    document.getElementById('hintSwap').classList.remove('show');
    document.getElementById('hintCouple').classList.remove('show');
}

// ══════════════════════════════════════════════════════════════════════════
// UTILITIES
// ══════════════════════════════════════════════════════════════════════════
function getSwapTarget() {
    return parseInt(document.getElementById('swapTargetType').value);
}

function apiFetch(url, method, body) {
    return fetch(url, {
        method,
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(body)
    }).then(r => {
        if (!r.ok) return r.text().then(t => {
            try {
                const j = JSON.parse(t);
                throw new Error(j.message || j.error || `Lỗi ${r.status}`);
            } catch(e) {
                throw new Error(t.trim().startsWith('<') ? `Lỗi ${r.status}` : t);
            }
        });
        return r.json();
    });
}

function set(id, val) {
    const e = document.getElementById(id);
    if (e) e.textContent = val;
}

function showSpinner(on) {
    document.getElementById('gridSpinner').classList.toggle('show', on);
}

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

document.getElementById('addRow').addEventListener('keydown', e => {
    if (e.key === 'Enter') document.getElementById('addCol').focus();
});
document.getElementById('addCol').addEventListener('keydown', e => {
    if (e.key === 'Enter') addSeat();
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') resetMode();
});

@if(session('success'))
    document.addEventListener('DOMContentLoaded', () =>
        showToast({{ Js::from(session('success')) }}, 'success'));
@endif
@if(session('error'))
    document.addEventListener('DOMContentLoaded', () =>
        showToast({{ Js::from(session('error')) }}, 'error'));
@endif

document.addEventListener('DOMContentLoaded', loadSeats);
</script>
@endsection