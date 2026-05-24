@extends('layouts.appAdmin')

@section('content')
<style>
:root {
    --bg-deep:  #080f1e; --bg-card: #0f1a2e;
    --bg-panel: #111827; --border: #1e2d45;
    --muted:    #6b7280; --accent: #3b82f6;
}
body { background: var(--bg-deep); color: #e5e7eb; }

.panel-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 16px; padding: 20px 24px; margin-bottom: 16px;
}
.panel-title {
    font-size: 11px; letter-spacing: .12em; text-transform: uppercase;
    color: var(--muted); margin-bottom: 10px; font-weight: 600;
}
.ctrl {
    background: var(--bg-panel); border: 1px solid var(--border);
    color: #e5e7eb; border-radius: 10px; padding: 9px 14px;
    font-size: 14px; width: 100%; transition: border-color .2s;
}
.ctrl:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(59,130,246,.15); }
.ctrl option { background: #1f2937; }

/* Buttons */
.btn-c { border-radius: 10px; font-weight: 700; font-size: 13px; padding: 9px 18px; cursor: pointer; border: none; transition: all .15s; white-space: nowrap; }
.btn-c:hover { transform: translateY(-1px); }
.btn-c:disabled { opacity:.4; cursor:not-allowed; transform:none; }
.btn-primary-c { background: var(--accent); color: #fff; }
.btn-primary-c:hover:not(:disabled) { background: #2563eb; }
.btn-ghost  { background: var(--bg-panel); color: var(--muted); border: 1px solid var(--border); }
.btn-ghost:hover:not(:disabled)  { background: #1f2937; color: #e5e7eb; }
.btn-danger-c  { background: #7f1d1d; color: #fca5a5; border: 1px solid #dc2626; }
.btn-success-c { background: #14532d; color: #86efac; border: 1px solid #22c55e; }
.btn-warning-c { background: #78350f; color: #fcd34d; border: 1px solid #f59e0b; }
.btn-couple-c  { background: #831843; color: #f9a8d4; border: 1px solid #ec4899; }
.btn-multi-c   { background: #1e1b4b; color: #a5b4fc; border: 1px solid #6366f1; }
.btn-multi-c:hover:not(:disabled) { background: #312e81; }

/* Swap panel */
.swap-info { background:#1e3a5f22; border:1px solid #1e40af; border-radius:12px; padding:14px 18px; display:none; gap:16px; align-items:center; flex-wrap:wrap; }
.swap-info.show { display: flex; }
.seat-badge { display:inline-flex; align-items:center; gap:8px; background:var(--bg-panel); border:1px solid var(--border); border-radius:8px; padding:6px 12px; font-weight:700; font-size:14px; }
.mode-hint { border-radius:10px; padding:10px 16px; font-size:13px; font-weight:600; display:none; margin-top:10px; }
.mode-hint.show { display:block; }
.mode-hint.swap  { background:#0f172a; border:1px dashed #22c55e; color:#86efac; }
.mode-hint.couple{ background:#1a0a1a; border:1px dashed #ec4899; color:#f9a8d4; }
.mode-hint.multi { background:#0f0f2a; border:1px dashed #6366f1; color:#a5b4fc; }

/* Multi-select batch panel */
.batch-panel { background:#0f172a; border:1px solid #312e81; border-radius:12px; padding:14px 18px; display:none; gap:14px; align-items:center; flex-wrap:wrap; margin-top:10px; }
.batch-panel.show { display:flex; }
.batch-count { font-weight:800; color:#a5b4fc; font-size:15px; }

/* Add seat grid */
.add-grid { display:grid; grid-template-columns:80px 100px 1fr auto; gap:10px; align-items:end; }
@media(max-width:640px){ .add-grid { grid-template-columns:1fr 1fr; } }
.cap-warning { color:#f59e0b; font-size:12px; margin-top:6px; display:none; }
.cap-warning.show { display:block; }

/* Stats */
.stat-pills { display:flex; gap:8px; flex-wrap:wrap; margin-top:12px; }
.stat-pill  { background:var(--bg-panel); border:1px solid var(--border); border-radius:999px; padding:5px 14px; font-size:12px; font-weight:700; display:flex; align-items:center; gap:6px; }
.stat-pill .dot { width:8px; height:8px; border-radius:50%; }

/* Screen */
.screen-wrap { text-align:center; margin-bottom:16px; }
.screen-bar  { width:55%; height:8px; margin:0 auto 6px; background:linear-gradient(to right,transparent,#fbbf24,transparent); border-radius:999px; box-shadow:0 0 24px rgba(251,191,36,.4); }
.screen-label{ font-size:11px; letter-spacing:.2em; color:#fbbf24; text-transform:uppercase; }

/* Grid */
.seat-grid { display:flex; flex-direction:column; gap:8px; align-items:center; overflow-x:auto; padding:16px 8px 24px; }
.seat-row  { display:flex; gap:6px; align-items:center; }
.row-label { width:26px; text-align:center; font-size:12px; color:var(--muted); font-weight:700; flex-shrink:0; }

/* Seat base */
.seat, .empty-slot, .disabled-slot {
    width:40px; height:40px; border-radius:8px;
    display:flex; justify-content:center; align-items:center;
    font-size:9px; font-weight:800;
    user-select:none; flex-shrink:0; position:relative;
    transition: transform .1s, box-shadow .1s, opacity .15s;
}
.seat { cursor:pointer; border:2px solid transparent; }
.seat:hover { transform:translateY(-2px) scale(1.07); z-index:10; }

/* Types */
.seat.t-normal { background:#1e3a5f; color:#93c5fd; border-color:#1e40af; }
.seat.t-vip    { background:#92400e; color:#fcd34d; border-color:#d97706; }
.seat.t-couple { background:#831843; color:#f9a8d4; border-color:#db2777; }
.seat.t-maint  { background:#374151; color:#9ca3af; border-color:#4b5563; }

/* States */
.seat.is-source { outline:3px solid #60a5fa; box-shadow:0 0 0 5px rgba(96,165,250,.3); z-index:30; transform:scale(1.1); }
.seat.is-couple-pair { outline:3px solid #60a5fa; box-shadow:0 0 0 5px rgba(96,165,250,.3); z-index:20; transform:scale(1.05); }
.seat.is-swap-target  { outline:3px solid #22c55e; box-shadow:0 0 0 5px rgba(34,197,94,.3); animation:pulse-g .8s infinite alternate; z-index:25; }
.seat.is-couple-target{ outline:3px solid #ec4899; box-shadow:0 0 0 5px rgba(236,72,153,.3); animation:pulse-p .8s infinite alternate; z-index:25; }
.seat.is-dimmed { opacity:.2; pointer-events:none; }

/* Multi-select state */
.seat.is-selected-multi { outline:3px solid #6366f1; box-shadow:0 0 0 5px rgba(99,102,241,.3); z-index:20; }
.seat.is-selected-multi::after { content:'✓'; position:absolute; top:-6px; right:-6px; width:14px; height:14px; background:#6366f1; border-radius:50%; font-size:9px; display:flex; justify-content:center; align-items:center; color:#fff; border:1px solid #a5b4fc; }

@keyframes pulse-g { from{box-shadow:0 0 0 3px rgba(34,197,94,.2)} to{box-shadow:0 0 0 8px rgba(34,197,94,.4)} }
@keyframes pulse-p { from{box-shadow:0 0 0 3px rgba(236,72,153,.2)} to{box-shadow:0 0 0 8px rgba(236,72,153,.4)} }

/* Empty slot — FIX #4: ô trống thực, bấm để thêm */
.empty-slot { background:#0f172a; border:1px dashed #1e293b; color:#1e3a5f; cursor:crosshair; font-size:16px; font-weight:300; }
.empty-slot:hover { border-color:#22c55e; color:#22c55e; background:#071a0e; transform:scale(1.05); }

/* FIX #4: disabled slot — chỗ trống ngoài biên, click để mở rộng */
.disabled-slot { background:#050c1a; border:1px dashed #0d1726; color:#0d1726; cursor:pointer; font-size:11px; font-weight:400; }
.disabled-slot:hover { border-color:#1e3a5f; color:#3b82f6; background:#080f1e; transform:scale(1.03); }
.disabled-slot .slot-plus { font-size:14px; }

/* Delete btn */
.del-btn { position:absolute; top:-5px; right:-5px; width:16px; height:16px; border-radius:50%; background:#dc2626; color:#fff; font-size:10px; display:none; justify-content:center; align-items:center; cursor:pointer; z-index:50; border:1px solid #fca5a5; }
.seat:hover .del-btn { display:flex; }

/* Legend */
.legend { display:flex; justify-content:center; gap:20px; flex-wrap:wrap; margin-top:20px; padding-bottom:24px; }
.legend-item { display:flex; align-items:center; gap:8px; font-size:12px; color:#9ca3af; }
.legend-box  { width:20px; height:20px; border-radius:5px; }

/* Drag-select overlay */
#dragRect { position:fixed; border:2px dashed #6366f1; background:rgba(99,102,241,.1); border-radius:4px; pointer-events:none; z-index:999; display:none; }

/* Spinner */
.grid-spinner { display:none; text-align:center; padding:40px 0; color:var(--muted); }
.grid-spinner.show { display:block; }

/* Toast */
#toast { position:fixed; bottom:24px; right:24px; min-width:260px; padding:14px 20px; border-radius:12px; font-size:14px; font-weight:600; z-index:9999; opacity:0; pointer-events:none; transform:translateY(10px); transition:all .3s ease; display:flex; align-items:center; gap:10px; }
#toast.show { opacity:1; transform:translateY(0); }
#toast.success { background:#14532d; border:1px solid #22c55e; color:#bbf7d0; }
#toast.error   { background:#7f1d1d; border:1px solid #ef4444; color:#fecaca; }
#toast.info    { background:#1e3a5f; border:1px solid #3b82f6; color:#bfdbfe; }
#toast.warning { background:#78350f; border:1px solid #f59e0b; color:#fcd34d; }
</style>

{{-- ══ PANEL 1: CHỌN PHÒNG + STATS ══ --}}
<div class="panel-card">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <div class="panel-title">Phòng chiếu</div>
            <form method="GET" id="roomForm">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <select name="roomID" class="ctrl" onchange="this.form.submit()">
                    @forelse($rooms as $r)
                        <option value="{{ $r->roomID }}"
                            {{ (int)($roomID??0)===(int)$r->roomID ? 'selected' : '' }}>
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
                <span class="stat-pill"><span class="dot" style="background:#6b7280"></span>Tổng: <b id="statTotal">0</b></span>
                <span class="stat-pill"><span class="dot" style="background:#3b82f6"></span>Thường: <b id="statNormal">0</b></span>
                <span class="stat-pill"><span class="dot" style="background:#f59e0b"></span>VIP: <b id="statVip">0</b></span>
                <span class="stat-pill"><span class="dot" style="background:#ec4899"></span>Đôi: <b id="statCouple">0</b></span>
                <span class="stat-pill"><span class="dot" style="background:#6b7280"></span>Bảo trì: <b id="statMaint">0</b></span>
                <span class="stat-pill"><span class="dot" style="background:#374151"></span>Hàng: <b id="statRows">0</b> | Cột: <b id="statCols">0</b></span>
            </div>
        </div>
    </div>
</div>

{{-- ══ PANEL 2: THAO TÁC GHẾ ══ --}}
<div class="panel-card">
    <div class="panel-title">Thao tác ghế</div>

    {{-- Idle --}}
    <p id="swapIdle" style="color:var(--muted);font-size:13px;margin:0;">
        ← Bấm vào một ghế để thao tác đơn lẻ. <b style="color:#a5b4fc">Kéo chuột</b> trên sơ đồ để chọn nhiều ghế cùng lúc.
    </p>

    {{-- Đã chọn ghế đơn --}}
    <div class="swap-info" id="swapPanel">
        <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Ghế đang chọn</div>
            <div class="seat-badge" id="swapSourceBadge">—</div>
        </div>

        <span style="color:var(--muted);font-size:18px;">⇄</span>

        <div style="flex:1;min-width:160px;">
            <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Đổi sang loại</div>
            <select id="swapTargetType" class="ctrl">
                @foreach($seatTypes as $type)
                    @if(strtolower($type->seatTypeName) !== 'bảo trì')
                        <option value="{{ $type->seatTypeID }}">{{ $type->seatTypeName }}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <button class="btn-c btn-success-c" id="btnStartSwap" onclick="startSwapPicking()">
                Tìm ghế hoán đổi
            </button>
            <button class="btn-c btn-couple-c"  id="btnStartCouple" onclick="startCouplePicking()">
                Tạo ghế đôi
            </button>
            <button class="btn-c btn-warning-c" id="btnMaint" onclick="toggleMaintenance()">
                ⚙ Bảo trì
            </button>
            <button class="btn-c btn-ghost" onclick="resetMode()">Huỷ</button>
        </div>
    </div>

    {{-- Hint swap --}}
    <div class="mode-hint swap" id="swapHint"></div>

    {{-- Hint couple --}}
    <div class="mode-hint couple" id="coupleHint">
        💑 Chọn 1 ghế liền kề (cùng hàng) được tô hồng để tạo thành cặp ghế đôi.
        Cả 2 ghế sẽ được đổi thành <b>Ghế đôi</b>.
    </div>

    {{-- FIX #3: Batch panel — hiện khi chọn nhiều ghế --}}
    <div class="batch-panel" id="batchPanel">
        <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Đã chọn</div>
            <div class="seat-badge"><span style="color:#a5b4fc">⬡</span> <span id="batchCount">0</span> ghế</div>
        </div>

        <div style="flex:1;min-width:160px;">
            <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Đổi tất cả sang loại</div>
            <select id="batchTargetType" class="ctrl">
                @foreach($seatTypes as $type)
                    <option value="{{ $type->seatTypeID }}">{{ $type->seatTypeName }}</option>
                @endforeach
            </select>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <button class="btn-c btn-multi-c" onclick="applyBatchUpdate()">
                ✦ Áp dụng cho tất cả
            </button>
            <button class="btn-c btn-ghost" onclick="resetMode()">Huỷ chọn</button>
        </div>
    </div>

    <div class="mode-hint multi" id="multiHint" style="display:none;margin-top:10px;padding:10px 16px;background:#0f0f2a;border:1px dashed #6366f1;border-radius:10px;color:#a5b4fc;font-size:13px;font-weight:600;">
        🖱 Đã chọn vùng ghế. Chọn loại đích rồi bấm <b>Áp dụng</b>. Bấm <b>Huỷ chọn</b> hoặc [Esc] để thoát.
    </div>
</div>

{{-- ══ PANEL 3: THÊM GHẾ MỚI ══ --}}
<div class="panel-card">
    <div class="panel-title">Thêm ghế mới</div>
    <div class="add-grid">
        <div>
            <label style="font-size:11px;color:var(--muted);display:block;margin-bottom:4px;">Hàng (A–Z)</label>
            <input id="addRow" type="text" maxlength="1" placeholder="A"
                   class="ctrl" style="text-transform:uppercase">
        </div>
        <div>
            <label style="font-size:11px;color:var(--muted);display:block;margin-bottom:4px;">Cột</label>
            <input id="addCol" type="number" min="1" max="99" placeholder="1" class="ctrl">
        </div>
        <div>
            <label style="font-size:11px;color:var(--muted);display:block;margin-bottom:4px;">Loại ghế</label>
            <select id="addType" class="ctrl">
                @foreach($seatTypes as $type)
                    <option value="{{ $type->seatTypeID }}">{{ $type->seatTypeName }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn-c btn-primary-c" onclick="addSeat()" id="btnAdd">+ Thêm ghế</button>
    </div>
    <div class="cap-warning" id="capWarn">
        ⚠ Số ghế hiện tại đã bằng sức chứa phòng. Ghế mới sẽ vượt quá capacity — hãy cập nhật thông tin phòng nếu cần.
    </div>
</div>

{{-- Form batch (hidden) --}}
<form id="updateSeatForm" method="POST" action="{{ route('seat.updateMultiple') }}">
    @csrf
    <div id="seatIDsContainer"></div>
    <input type="hidden" name="seatTypeID" id="seatTypeInput">
</form>

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
    <div class="legend-item"><div class="legend-box" style="background:#1e3a5f;border:2px solid #1e40af"></div>Thường</div>
    <div class="legend-item"><div class="legend-box" style="background:#92400e;border:2px solid #d97706"></div>VIP</div>
    <div class="legend-item"><div class="legend-box" style="background:#831843;border:2px solid #db2777"></div>Đôi</div>
    <div class="legend-item"><div class="legend-box" style="background:#374151;border:2px solid #4b5563"></div>Bảo trì</div>
    <div class="legend-item"><div class="legend-box" style="background:#050c1a;border:1px dashed #1e3a5f"></div>Ô tắt</div>
    <div class="legend-item"><div class="legend-box" style="outline:3px solid #6366f1;border-radius:5px;background:#1e3a5f"></div>Đã chọn nhiều</div>
</div>

{{-- FIX #3: Drag-select rectangle --}}
<div id="dragRect"></div>

<div id="toast"><span id="t-ico"></span><span id="t-msg"></span></div>

<script>
'use strict';

const ROOM_ID  = parseInt("{{ (int)($roomID ?? 0) }}", 10);
const CAPACITY = parseInt("{{ $room?->capacity ?? 0 }}", 10);
const CSRF     = "{{ csrf_token() }}";

const TYPE_CLASS = { 1:'t-vip', 2:'t-normal', 3:'t-couple', 4:'t-maint' };
const TYPE_NAME  = { 1:'VIP',   2:'Thường',   3:'Đôi',      4:'Bảo trì' };
const TYPE_COLOR = { 1:'#f59e0b', 2:'#3b82f6', 3:'#ec4899', 4:'#6b7280' };

// ── State ──────────────────────────────────────────────────────────────
let seatsData    = [];
let appMode      = 'idle';
let sourceSeat   = null;
let selectedMulti = new Set();   // FIX #3: set of seatID strings

// ══════════════════════════════════════════════════════════════════════
// LOAD & RENDER
// ══════════════════════════════════════════════════════════════════════
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
    seatsData.forEach(s => cnt[s.seatTypeID] = (cnt[s.seatTypeID]||0) + 1);

    set('statTotal',  seatsData.length);
    set('statNormal', cnt[2]||0);
    set('statVip',    cnt[1]||0);
    set('statCouple', cnt[3]||0);
    set('statMaint',  cnt[4]||0);
    set('statRows',   rows.length);
    set('statCols',   maxCol);

    document.getElementById('capWarn').classList.toggle('show', seatsData.length >= CAPACITY);
}

// ── FIX #2 helper: tìm ghế đôi kế cận ─────────────────────────────
function findCouplePair(seat) {
    if (seat.seatTypeID !== 3) return null;
    return seatsData.find(s =>
        s.seatTypeID === 3 &&
        s.rowSeat === seat.rowSeat &&
        Math.abs(+s.colSeat - +seat.colSeat) === 1 &&
        s.seatID !== seat.seatID
    ) || null;
}

function renderGrid() {
    const grid    = document.getElementById('seatGrid');
    const seatMap = {};
    seatsData.forEach(s => { seatMap[`${s.rowSeat}-${s.colSeat}`] = s; });

    const rows   = [...new Set(seatsData.map(s => s.rowSeat))].sort();
    // FIX #4: mở rộng thêm 1 cột và 1 hàng ngoài biên tối đa
    const maxCol = seatsData.reduce((m, s) => Math.max(m, +s.colSeat), 0);
    const extraCol = maxCol + 1;

    let html = '';

    rows.forEach(row => {
        html += `<div class="seat-row"><span class="row-label">${row}</span>`;
        for (let col = 1; col <= extraCol; col++) {
            const seat = seatMap[`${row}-${col}`];
            if (seat) {
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
            } else if (col === extraCol) {
                // FIX #4: cột mở rộng — disabled slot, click để thêm ghế ở cột đó
                html += `<div class="disabled-slot"
                              data-row="${row}" data-col="${col}"
                              title="Bấm để thêm ghế ${row}${col}"
                              onclick="quickFill('${row}',${col})">
                            <span class="slot-plus">+</span>
                         </div>`;
            } else {
                // ô trống bên trong biên
                html += `<div class="empty-slot"
                              data-row="${row}" data-col="${col}">+</div>`;
            }
        }
        html += `</div>`;
    });

    // FIX #4: Hàng mở rộng (hàng mới) — chỉ hiện 1 ô mở đầu + disabled slots
    const lastRow = rows[rows.length - 1];
    const nextRow = lastRow ? String.fromCharCode(lastRow.charCodeAt(0) + 1) : 'A';
    if (nextRow <= 'Z' && rows.length > 0) {
        html += `<div class="seat-row"><span class="row-label" style="color:#374151">${nextRow}</span>`;
        for (let col = 1; col <= extraCol; col++) {
            html += `<div class="disabled-slot"
                          data-row="${nextRow}" data-col="${col}"
                          title="Bấm để thêm ghế ${nextRow}${col}"
                          onclick="quickFill('${nextRow}',${col})">
                        <span class="slot-plus">+</span>
                     </div>`;
        }
        html += `</div>`;
    }

    grid.innerHTML = html;
    bindGridEvents();
    reApplyStyles();
}

function bindGridEvents() {
    document.querySelectorAll('.seat').forEach(el => {
        el.addEventListener('click', function(e) {
            // Nếu vừa kết thúc drag → bỏ qua click này
            if (dragJustFinished) { dragJustFinished = false; return; }
            // Trong mode multi_select, click đơn không làm gì
            if (appMode === 'multi_select') return;

            if (appMode === 'swap_picking') {
                if (this.classList.contains('is-swap-target')) confirmSwap(this);
            } else if (appMode === 'couple_picking') {
                if (this.classList.contains('is-couple-target')) confirmCouple(this);
            } else {
                selectSource(this);
            }
        });
    });

    // Ô trống bên trong → điền form
    document.querySelectorAll('.empty-slot').forEach(el => {
        el.addEventListener('click', function() {
            quickFill(this.dataset.row, this.dataset.col);
        });
    });
}

// FIX #4: Điền sẵn form thêm ghế
function quickFill(row, col) {
    if (appMode === 'multi_select') return;
    document.getElementById('addRow').value = row;
    document.getElementById('addCol').value = col;
    showToast(`Điền ${row}${col} vào form thêm ghế`, 'info');
    document.getElementById('addRow').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// ══════════════════════════════════════════════════════════════════════
// FIX #3: DRAG-SELECT — kéo chuột trên grid để chọn nhiều ghế
// ══════════════════════════════════════════════════════════════════════
function updateBatchPanel() {
    const count = selectedMulti.size;
    set('batchCount', count);
    const bp = document.getElementById('batchPanel');
    const mh = document.getElementById('multiHint');
    if (count > 0) {
        document.getElementById('swapIdle').style.display  = 'none';
        document.getElementById('swapPanel').classList.remove('show');
        bp.classList.add('show');
        mh.style.display = 'block';
        appMode = 'multi_select';
    } else {
        bp.classList.remove('show');
        mh.style.display = 'none';
        if (appMode === 'multi_select') {
            appMode = 'idle';
            document.getElementById('swapIdle').style.display = '';
        }
    }
}

function applyBatchUpdate() {
    if (!selectedMulti.size) { showToast('Chưa chọn ghế nào', 'warning'); return; }
    const typeID   = parseInt(document.getElementById('batchTargetType').value);
    const typeName = TYPE_NAME[typeID] || '?';
    if (!confirm(`Đổi ${selectedMulti.size} ghế → ${typeName}?\n(Ghế đôi và ghế đã có vé sẽ bị bỏ qua)`)) return;

    apiFetch('/admins/seat/ajax-batch-update-type', 'POST', {
        seatIDs: [...selectedMulti].map(Number),
        seatTypeID: typeID
    }).then(res => {
        if (!res.success) { showToast(res.message, 'error'); return; }
        showToast(res.message, 'success');
        resetMode();
        loadSeats(true);
    }).catch(err => showToast(err.message, 'error'));
}

// ── Drag-select logic ──────────────────────────────────────────────
let dragState      = { active: false, startX: 0, startY: 0 };
let dragJustFinished = false;   // flag để bỏ qua click ngay sau drag
const dragRect     = document.getElementById('dragRect');
const DRAG_THRESHOLD = 6;       // px tối thiểu để tính là drag

// Bắt đầu từ bất kỳ đâu trong seatGrid (kể cả trên ghế)
document.getElementById('seatGrid').addEventListener('mousedown', function(e) {
    if (e.button !== 0) return;
    // Không drag khi đang ở mode swap/couple picking
    if (appMode === 'swap_picking' || appMode === 'couple_picking') return;

    dragState = { active: false, pending: true, startX: e.clientX, startY: e.clientY };
    e.preventDefault();   // ngăn text selection khi kéo
});

document.addEventListener('mousemove', function(e) {
    if (!dragState.pending && !dragState.active) return;

    const dx = Math.abs(e.clientX - dragState.startX);
    const dy = Math.abs(e.clientY - dragState.startY);

    // Khi vượt ngưỡng → chính thức bắt đầu drag
    if (!dragState.active && (dx > DRAG_THRESHOLD || dy > DRAG_THRESHOLD)) {
        dragState.active  = true;
        dragState.pending = false;
        // Reset single-select nếu đang chọn đơn
        if (appMode === 'selected') { resetMode(); }
    }

    if (!dragState.active) return;

    const x = Math.min(e.clientX, dragState.startX);
    const y = Math.min(e.clientY, dragState.startY);
    const w = Math.abs(e.clientX - dragState.startX);
    const h = Math.abs(e.clientY - dragState.startY);
    dragRect.style.cssText = `display:block;position:fixed;left:${x}px;top:${y}px;width:${w}px;height:${h}px;`;

    // Live highlight khi đang kéo
    const r1 = { left: x, top: y, right: x + w, bottom: y + h };
    document.querySelectorAll('.seat').forEach(el => {
        const r2      = el.getBoundingClientRect();
        const overlap = !(r2.right < r1.left || r2.left > r1.right || r2.bottom < r1.top || r2.top > r1.bottom);
        el.classList.toggle('is-selected-multi', overlap);
    });
});

document.addEventListener('mouseup', function(e) {
    if (!dragState.pending && !dragState.active) return;

    const wasDragging = dragState.active;
    dragState = { active: false, pending: false, startX: 0, startY: 0 };
    dragRect.style.display = 'none';

    if (!wasDragging) return;   // chỉ click thường, không phải drag

    // Thu thập ghế đã được highlight
    selectedMulti.clear();
    document.querySelectorAll('.seat.is-selected-multi').forEach(el => {
        selectedMulti.add(el.dataset.id);
    });

    if (selectedMulti.size > 0) {
        dragJustFinished = true;   // bỏ qua click event ngay sau đây
        updateBatchPanel();
    } else {
        // Kéo nhưng không chọn được ghế nào → clear
        clearStyles();
    }
});

// ══════════════════════════════════════════════════════════════════════
// SINGLE SEAT SELECT
// ══════════════════════════════════════════════════════════════════════
function selectSource(el) {
    sourceSeat = {
        id:     el.dataset.id,
        name:   el.dataset.name,
        typeID: +el.dataset.type,
        row:    el.dataset.row,
        col:    +el.dataset.col,
    };
    appMode = 'selected';

    document.getElementById('swapIdle').style.display  = 'none';
    document.getElementById('swapPanel').classList.add('show');
    document.getElementById('swapHint').classList.remove('show');
    document.getElementById('coupleHint').classList.remove('show');
    document.getElementById('batchPanel').classList.remove('show');
    document.getElementById('multiHint').style.display = 'none';

    // FIX #1: Điều chỉnh label nút bảo trì dựa trên trạng thái hiện tại
    const btnMaint = document.getElementById('btnMaint');
    if (sourceSeat.typeID === 4) {
        btnMaint.textContent = '✓ Phục hồi ghế';
        btnMaint.className = 'btn-c btn-success-c';
    } else {
        btnMaint.textContent = '⚙ Bảo trì';
        btnMaint.className = 'btn-c btn-warning-c';
    }

    // FIX #2: Badge hiện cả cặp ghế đôi
    let badgeHtml = `<span style="color:${TYPE_COLOR[sourceSeat.typeID]}">■</span>
        ${sourceSeat.name} (${TYPE_NAME[sourceSeat.typeID]})`;

    if (sourceSeat.typeID === 3) {
        const pair = findCouplePair(seatsData.find(s => String(s.seatID) === sourceSeat.id));
        if (pair) {
            badgeHtml += ` <span style="color:#6b7280;font-size:11px">+</span>
                <span style="color:#ec4899">■</span>
                ${pair.rowSeat}${pair.colSeat} (Đôi)`;
        }
        // Ẩn nút hoán đổi và tạo đôi cho ghế đôi
        document.getElementById('btnStartSwap').style.display   = 'none';
        document.getElementById('btnStartCouple').style.display = 'none';
    } else {
        document.getElementById('btnStartSwap').style.display   = '';
        document.getElementById('btnStartCouple').style.display = '';

        const sel = document.getElementById('swapTargetType');
        for (let o of sel.options) { if (+o.value !== sourceSeat.typeID) { o.selected = true; break; } }
    }

    document.getElementById('swapSourceBadge').innerHTML = badgeHtml;
    reApplyStyles();
}

// ══════════════════════════════════════════════════════════════════════
// SWAP FLOW
// ══════════════════════════════════════════════════════════════════════
function startSwapPicking() {
    if (!sourceSeat) return;
    const tid = getTargetTypeID();
    if (tid === sourceSeat.typeID) { showToast('Loại đích phải khác loại hiện tại', 'warning'); return; }
    if (tid === 3) { showToast('Dùng nút "Tạo ghế đôi" để tạo ghế đôi', 'warning'); return; }

    const targets = seatsData.filter(s => s.seatTypeID === tid);
    if (!targets.length) { showToast(`Không có ghế ${TYPE_NAME[tid]} để hoán đổi`, 'warning'); return; }

    appMode = 'swap_picking';
    reApplyStyles();

    const hint = document.getElementById('swapHint');
    hint.textContent = `Đang hoán đổi: ${sourceSeat.name} (${TYPE_NAME[sourceSeat.typeID]}) ⇄ ${TYPE_NAME[tid]}. Chọn 1 ghế tô xanh. [Esc] để huỷ.`;
    hint.classList.add('show');
    document.getElementById('coupleHint').classList.remove('show');
}

function confirmSwap(targetEl) {
    const tid = String(targetEl.dataset.id);
    const tnm = targetEl.dataset.name;
    if (!confirm(`Hoán đổi loại:\n${sourceSeat.name} (${TYPE_NAME[sourceSeat.typeID]}) ⇄ ${tnm} (${TYPE_NAME[+targetEl.dataset.type]})?`)) return;

    apiFetch('/admins/seat/ajax-swap-type', 'POST', {
        seatID_a: parseInt(sourceSeat.id),
        seatID_b: parseInt(tid)
    }).then(res => {
        if (!res.success) { showToast(res.message, 'error'); return; }
        showToast(`Đã hoán đổi ${sourceSeat.name} ⇄ ${tnm}`, 'success');
        resetMode(); loadSeats(true);
    }).catch(err => showToast(err.message, 'error'));
}

// ══════════════════════════════════════════════════════════════════════
// COUPLE FLOW
// ══════════════════════════════════════════════════════════════════════
function startCouplePicking() {
    if (!sourceSeat) return;
    if (sourceSeat.typeID === 3) { showToast('Ghế đã là ghế đôi', 'warning'); return; }

    const adjacent = seatsData.filter(s =>
        s.rowSeat === sourceSeat.row &&
        Math.abs(+s.colSeat - sourceSeat.col) === 1 &&
        s.seatTypeID !== 3
    );

    if (!adjacent.length) {
        showToast(`Ghế ${sourceSeat.name} không có ghế liền kề để ghép đôi`, 'warning'); return;
    }

    appMode = 'couple_picking';
    reApplyStyles();
    document.getElementById('coupleHint').classList.add('show');
    document.getElementById('swapHint').classList.remove('show');
}

function confirmCouple(targetEl) {
    const tid  = String(targetEl.dataset.id);
    const tnm  = targetEl.dataset.name;
    const ttyp = TYPE_NAME[+targetEl.dataset.type];

    if (!confirm(
        `Tạo ghế đôi:\n` +
        `• ${sourceSeat.name} (${TYPE_NAME[sourceSeat.typeID]})\n` +
        `• ${tnm} (${ttyp})\n\n` +
        `Cả 2 ghế sẽ được chuyển thành Ghế đôi.`
    )) return;

    apiFetch('/admins/seat/ajax-convert-couple', 'POST', {
        seatID_a: parseInt(sourceSeat.id),
        seatID_b: parseInt(tid)
    }).then(res => {
        if (!res.success) { showToast(res.message, 'error'); return; }
        showToast(`Đã tạo ghế đôi: ${sourceSeat.name} + ${tnm}`, 'success');
        resetMode(); loadSeats(true);
    }).catch(err => showToast(err.message, 'error'));
}

// ══════════════════════════════════════════════════════════════════════
// FIX #1: BẢO TRÌ — cho phép phục hồi từ bảo trì
// ══════════════════════════════════════════════════════════════════════
function toggleMaintenance() {
    if (!sourceSeat) return;
    const isMaint  = sourceSeat.typeID === 4;
    const newType  = isMaint ? 2 : 4;
    const label    = isMaint ? 'Phục hồi thành ghế thường' : 'Chuyển sang bảo trì';
    if (!confirm(`${label}: ghế ${sourceSeat.name}?`)) return;

    apiFetch('/admins/seat/ajax-update-type', 'POST', {
        seatID: parseInt(sourceSeat.id), seatTypeID: newType
    }).then(res => {
        if (!res.success) { showToast(res.message, 'error'); return; }
        showToast(`Ghế ${sourceSeat.name} → ${TYPE_NAME[newType]}`, 'success');
        resetMode(); loadSeats(true);
    }).catch(err => showToast(err.message, 'error'));
}

// ══════════════════════════════════════════════════════════════════════
// THÊM GHẾ MỚI
// ══════════════════════════════════════════════════════════════════════
function addSeat() {
    const row    = document.getElementById('addRow').value.trim().toUpperCase();
    const col    = parseInt(document.getElementById('addCol').value);
    const typeID = parseInt(document.getElementById('addType').value);

    if (!row || !/^[A-Z]$/.test(row)) {
        showToast('Hàng phải là 1 chữ cái A–Z', 'error'); return;
    }
    if (!col || col < 1 || col > 99) {
        showToast('Cột phải từ 1 đến 99', 'error'); return;
    }

    if (seatsData.length >= CAPACITY) {
        if (!confirm(`Phòng đã có ${seatsData.length} ghế (capacity: ${CAPACITY}).\nVẫn tiếp tục thêm?`)) return;
    }

    const dup = seatsData.some(s => s.rowSeat === row && +s.colSeat === col);
    if (dup) { showToast(`Ghế ${row}${col} đã tồn tại`, 'error'); return; }

    const btn = document.getElementById('btnAdd');
    btn.disabled = true;

    apiFetch('/admins/seat/ajax-add', 'POST', {
        roomID: ROOM_ID, rowSeat: row, colSeat: col, seatTypeID: typeID
    }).then(res => {
        if (res.error) { showToast(res.error, 'error'); return; }
        showToast(`Đã thêm ghế ${row}${col} (${TYPE_NAME[typeID]||''})`, 'success');
        document.getElementById('addRow').value = '';
        document.getElementById('addCol').value = '';
        loadSeats(true);
    }).catch(err => showToast(err.message, 'error'))
      .finally(() => { btn.disabled = false; });
}

// ══════════════════════════════════════════════════════════════════════
// XOÁ GHẾ
// ══════════════════════════════════════════════════════════════════════
function deleteSeat(id, event) {
    event.stopPropagation();
    if (!confirm('Xoá ghế này?')) return;

    fetch(`/admins/seat/ajax-delete/${id}`, {
        method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF }
    }).then(r => r.json())
      .then(res => {
        if (res.error) { showToast(res.error, 'error'); return; }
        if (sourceSeat && String(sourceSeat.id) === String(id)) resetMode();
        selectedMulti.delete(String(id));
        loadSeats(true);
        showToast('Đã xoá ghế', 'success');
    }).catch(() => showToast('Lỗi kết nối', 'error'));
}

// ══════════════════════════════════════════════════════════════════════
// APPLY STYLES
// ══════════════════════════════════════════════════════════════════════
function reApplyStyles() {
    clearStyles();
    if (!sourceSeat && !selectedMulti.size) return;

    // Multi-select mode
    if (selectedMulti.size > 0) {
        document.querySelectorAll('.seat').forEach(el => {
            if (selectedMulti.has(el.dataset.id)) el.classList.add('is-selected-multi');
        });
        return;
    }

    const src = String(sourceSeat.id);

    if (appMode === 'selected') {
        document.querySelectorAll('.seat').forEach(el => {
            if (el.dataset.id === src) { el.classList.add('is-source'); return; }
            // FIX #2: nếu ghế nguồn là ghế đôi, highlight cả cặp
            if (sourceSeat.typeID === 3) {
                const seatObj = seatsData.find(s => String(s.seatID) === src);
                const pair = seatObj ? findCouplePair(seatObj) : null;
                if (pair && String(pair.seatID) === el.dataset.id) {
                    el.classList.add('is-couple-pair');
                }
            }
        });
    }

    if (appMode === 'swap_picking') {
        const targetTypeID = getTargetTypeID();
        document.querySelectorAll('.seat').forEach(el => {
            if (el.dataset.id === src) { el.classList.add('is-source'); return; }
            if (+el.dataset.type === targetTypeID) { el.classList.add('is-swap-target'); return; }
            el.classList.add('is-dimmed');
        });
    }

    if (appMode === 'couple_picking') {
        const srcRow = sourceSeat.row;
        const srcCol = sourceSeat.col;
        document.querySelectorAll('.seat').forEach(el => {
            if (el.dataset.id === src) { el.classList.add('is-source'); return; }
            const sameRow  = el.dataset.row === srcRow;
            const adjacent = Math.abs(+el.dataset.col - srcCol) === 1;
            const notCouple = +el.dataset.type !== 3;
            if (sameRow && adjacent && notCouple) { el.classList.add('is-couple-target'); return; }
            el.classList.add('is-dimmed');
        });
    }
}

function clearStyles() {
    document.querySelectorAll('.seat').forEach(el =>
        el.classList.remove('is-source','is-couple-pair','is-swap-target','is-couple-target','is-dimmed','is-selected-multi')
    );
}

// ══════════════════════════════════════════════════════════════════════
// RESET MODE
// ══════════════════════════════════════════════════════════════════════
function resetMode() {
    appMode = 'idle';
    sourceSeat = null;
    selectedMulti.clear();
    clearStyles();
    document.getElementById('swapIdle').style.display  = '';
    document.getElementById('swapPanel').classList.remove('show');
    document.getElementById('swapHint').classList.remove('show');
    document.getElementById('coupleHint').classList.remove('show');
    document.getElementById('batchPanel').classList.remove('show');
    document.getElementById('multiHint').style.display = 'none';
}

// ══════════════════════════════════════════════════════════════════════
// HELPERS
// ══════════════════════════════════════════════════════════════════════
function getTargetTypeID() { return parseInt(document.getElementById('swapTargetType').value); }

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
                if (e.message && !e.message.startsWith('Lỗi')) throw e;
                throw new Error(`Lỗi ${r.status}`);
            }
        });
        return r.json();
    });
}

function set(id, val) { const el = document.getElementById(id); if (el) el.textContent = val; }
function showSpinner(on) { document.getElementById('gridSpinner').classList.toggle('show', on); }

let _tt;
const T_ICO = { success:'✓', error:'✕', info:'ℹ', warning:'⚠' };
function showToast(msg, type = 'success') {
    const el = document.getElementById('toast');
    document.getElementById('t-ico').textContent = T_ICO[type] || '';
    document.getElementById('t-msg').textContent = msg;
    el.className = `show ${type}`;
    clearTimeout(_tt); _tt = setTimeout(() => { el.className = ''; }, 3500);
}

// Keyboard shortcuts
document.getElementById('addRow').addEventListener('keydown', e => { if (e.key === 'Enter') document.getElementById('addCol').focus(); });
document.getElementById('addCol').addEventListener('keydown', e => { if (e.key === 'Enter') addSeat(); });
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') resetMode();
});

@if(session('success'))
    document.addEventListener('DOMContentLoaded',()=>showToast({{ Js::from(session('success')) }},'success'));
@endif
@if(session('error'))
    document.addEventListener('DOMContentLoaded',()=>showToast({{ Js::from(session('error')) }},'error'));
@endif

document.addEventListener('DOMContentLoaded', loadSeats);
</script>
@endsection